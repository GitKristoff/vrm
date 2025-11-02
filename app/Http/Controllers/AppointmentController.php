<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Veterinarian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Notifications\AppointmentReminder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewAppointmentNotification;
use App\Services\MedicalRecordService;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;


class AppointmentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $appointments = Appointment::with(['pet', 'veterinarian.user']);

        if($user->role === 'veterinarian') {
            $appointments->where('veterinarian_id', $user->veterinarian->id);
        } elseif($user->role === 'owner') {
            $petIds = $user->owner->pets->pluck('id');
            $appointments->whereIn('pet_id', $petIds);
        }

        return view('appointments.index', [
            'appointments' => $appointments->paginate(10)
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        // Get pets based on user role
        if ($user->role === 'owner' && $user->owner) {
            $pets = $user->owner->pets;
        } else {
            $pets = Pet::all();
        }

        // Check if a specific pet is pre-selected
        if (request()->has('pet_id')) {
            $selectedPet = request('pet_id');
        } else {
            $selectedPet = $pets->first()->id ?? null;
        }

        // Exclude veterinarians who belong to system admin users
        $vets = Veterinarian::with('user')
            ->whereHas('user', function($q) {
                $q->where('role', '!=', 'admin');
            })
            ->get();

        // compute remaining slots for today (Asia/Manila)
        $limits = config('appointment_limits', []);
        $todayManila = Carbon::now('Asia/Manila');
        $dayStartUtc = $todayManila->copy()->startOfDay()->setTimezone('UTC');
        $dayEndUtc = $todayManila->copy()->endOfDay()->setTimezone('UTC');

        $remainingLimits = [];
        foreach ($limits as $type => $limit) {
            $count = Appointment::where('type', $type)
                ->where('status', 'Scheduled')
                ->whereBetween('appointment_date', [$dayStartUtc, $dayEndUtc])
                ->count();
            $remainingLimits[$type] = max(0, $limit - $count);
        }

        return view('appointments.create', compact('pets', 'vets', 'selectedPet', 'remainingLimits'));
    }

    public function store(Request $request)
    {
        // Validate request first
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:veterinarians,id',
            'appointment_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'type' => 'required|string|max:50',
        ]);

        // Find selected veterinarian
        $vet = Veterinarian::find($request->veterinarian_id);
        if (!$vet) {
            return back()->withInput()->withErrors(['veterinarian_id' => 'Selected veterinarian not found.']);
        }

        // Do not allow booking if vet status is not "in"
        if (!empty($vet->status) && $vet->status !== 'in') {
            return back()->withInput()
                ->withErrors(['veterinarian_id' => 'The selected veterinarian is currently unavailable (' . ucfirst($vet->status) . ').']);
        }

        // Parse appointment datetime in Manila timezone (owner input assumed Manila)
        $appointmentManila = Carbon::parse($request->appointment_date, 'Asia/Manila');
        $dayName = $appointmentManila->format('l'); // e.g. Monday

        // Enforce working days if configured
        if (!empty($vet->working_days) && is_array($vet->working_days) && count($vet->working_days) > 0) {
            if (!in_array($dayName, $vet->working_days)) {
                return back()->withInput()
                    ->withErrors(['appointment_date' => 'The selected veterinarian does not work on ' . $dayName . '. Please choose another day.']);
            }
        }

        // Enforce working hours if configured (compare H:i)
        if (!empty($vet->start_time) && !empty($vet->end_time)) {
            $apptTime = $appointmentManila->format('H:i');
            // ensure start_time < end_time
            if ($vet->start_time >= $vet->end_time) {
                return back()->withInput()
                    ->withErrors(['veterinarian_id' => 'Veterinarian schedule is invalid. Please contact admin.']);
            }
            if ($apptTime < $vet->start_time || $apptTime >= $vet->end_time) {
                return back()->withInput()
                    ->withErrors(['appointment_date' => 'Please choose a time between ' . Carbon::parse($vet->start_time)->format('g:i A') . ' and ' . Carbon::parse($vet->end_time)->format('g:i A') . '.']);
            }
        }

        // BEFORE creating: enforce per-day type limit (Asia/Manila)
        $limits = config('appointment_limits', []);
        $apptType = $request->type;

        $appointmentManila = Carbon::parse($request->appointment_date, 'Asia/Manila');
        $dayStartUtc = $appointmentManila->copy()->startOfDay()->setTimezone('UTC');
        $dayEndUtc = $appointmentManila->copy()->endOfDay()->setTimezone('UTC');

        $limitForType = $limits[$apptType] ?? null;
        if ($limitForType !== null) {
            $existingCount = Appointment::where('type', $apptType)
                ->where('status', 'Scheduled')
                ->whereBetween('appointment_date', [$dayStartUtc, $dayEndUtc])
                ->count();

            if ($existingCount >= $limitForType) {
                return back()->withInput()
                    ->withErrors(['appointment_date' => 'Daily limit reached for ' . ucfirst($apptType) . '. Please choose another day or type.']);
            }
        }

        // Convert to UTC for storage and conflict checks
        $startTimeUtc = $appointmentManila->copy()->setTimezone('UTC');
        $endTimeUtc = $startTimeUtc->copy()->addMinutes((int)$request->duration_minutes);

        // Check vet availability conflicts (DB stores UTC)
        $conflictingAppointments = Appointment::where('veterinarian_id', $request->veterinarian_id)
            ->where('status', 'Scheduled')
            ->where(function ($query) use ($startTimeUtc, $endTimeUtc) {
                $query->where(function ($q) use ($startTimeUtc, $endTimeUtc) {
                    $q->where('appointment_date', '<', $endTimeUtc)
                      ->whereRaw("DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > ?", [$startTimeUtc]);
                });
            })
            ->exists();

        if ($conflictingAppointments) {
            return back()->withInput()
                ->withErrors(['appointment_date' => 'The veterinarian is not available at this time. Please choose another time slot.']);
        }

        Log::info($request->all());

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'veterinarian_id' => $request->veterinarian_id,
            // save in UTC
            'appointment_date' => $startTimeUtc->format('Y-m-d H:i:s'),
            'reason' => $request->reason,
            'duration_minutes' => $request->duration_minutes,
            'type' => $request->type,
            'status' => 'Scheduled',
        ]);

        // Send notifications
        $vetModel = Veterinarian::with('user')->find($request->veterinarian_id);
        $vetModel->user->notify(new NewAppointmentNotification($appointment));

        // Also notify admin
        $admins = User::where('role', 'admin')->get();
        Notification::send($admins, new NewAppointmentNotification($appointment));

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment created successfully!');
    }

    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        // Authorization
        if ($user->role === 'owner') {
            if (!$appointment->pet || $appointment->pet->owner_id !== $user->owner->id) {
                abort(403);
            }
        } elseif ($user->role === 'veterinarian') {
            if ($appointment->veterinarian_id !== $user->veterinarian->id) {
                abort(403);
            }
        } elseif ($user->role !== 'admin') {
            abort(403);
        }

        $medicalHistory = MedicalRecord::where('pet_id', $appointment->pet_id)
        ->orderBy('record_date', 'desc')
        ->limit(5)
        ->get();

        return view('appointments.show', compact('appointment', 'medicalHistory'));
    }

    public function update(Request $request, Appointment $appointment)
    {

        $request->validate([
        'status' => 'required|in:Scheduled,Completed,Cancelled,No-show',
        'notes' => 'nullable|string'
        ]);

        // Only allow changing status to Cancelled
        if ($request->status === 'Cancelled') {
            $appointment->update(['status' => 'Cancelled']);
            return back()->with('success', 'Appointment has been cancelled!');
        }

        return back()->with('error', 'You can only cancel appointments');
    }

    public function destroy(Appointment $appointment)
    {
        // Authorization
        $user = Auth::user();
        if ($user->role === 'owner') {
            if ($appointment->pet->owner_id !== $user->owner->id) {
                abort(403);
            }
        } elseif ($user->role === 'veterinarian') {
            if ($appointment->veterinarian_id !== $user->veterinarian->id) {
                abort(403);
            }
        } elseif ($user->role !== 'admin') {
            abort(403);
        }

        // Only allow cancellation of scheduled appointments
        if ($appointment->status === 'Scheduled') {
            $appointment->update(['status' => 'Cancelled']);
            return back()->with('success', 'Appointment has been cancelled!');
        }

        return back()->with('error', 'Only scheduled appointments can be cancelled');
    }

    public function checkinForm(Appointment $appointment)
    {
        // Authorization
        $user = Auth::user();
        if ($user->role !== 'veterinarian' ||
            $appointment->veterinarian_id !== $user->veterinarian->id) {
            abort(403);
        }

        return view('appointments.checkin', compact('appointment'));
    }

    public function checkinStore(Request $request, Appointment $appointment)
    {
        // Authorization
        $user = Auth::user();
        if ($user->role !== 'veterinarian' ||
            $appointment->veterinarian_id !== $user->veterinarian->id) {
            abort(403);
        }

        $service = new MedicalRecordService();

        $record = $service->createRecord([
            'veterinarian_id' => $appointment->veterinarian_id,
            'record_date' => now(),
            'subjective_notes' => $request->subjective_notes,
            'objective_notes' => $request->objective_notes,
            'assessment' => $request->assessment,
            'plan' => $request->plan,
            'temperature' => $request->temperature,
            'heart_rate' => $request->heart_rate,
            'respiratory_rate' => $request->respiratory_rate,
            'weight' => $request->weight,
            'vaccination_history' => $request->vaccination_history,
            'medications' => $request->medications ?? []
        ], $appointment->pet_id, $appointment->id);

        $appointment->update(['status' => 'Completed']);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment completed and medical record created!');
    }

    public function calendar()
    {
        $user = Auth::user();
        $appointments = Appointment::with(['pet', 'veterinarian.user']);

        // Filter appointments based on user role
        if ($user->role === 'veterinarian') {
            $appointments->where('veterinarian_id', $user->veterinarian->id);
        } elseif ($user->role === 'owner') {
            $petIds = $user->owner->pets->pluck('id');
            $appointments->whereIn('pet_id', $petIds);
        }

        $appointments = $appointments->get();

        // Prepare events for FullCalendar
        $events = $appointments->map(function ($appointment) {
            return [
                'title' => $appointment->pet->name . ' with ' . $appointment->veterinarian->user->name,
                'start' => $appointment->appointment_date
                    ->setTimezone('Asia/Manila')
                    ->format('Y-m-d\TH:i:s'),
                'end' => $appointment->appointment_date
                    ->copy()
                    ->addMinutes($appointment->duration_minutes)
                    ->setTimezone('Asia/Manila')
                    ->format('Y-m-d\TH:i:s'),
                'url' => route('appointments.show', $appointment->id),
                'color' => '#4f46e5', // Indigo
            ];
        });

        return view('appointments.calendar', ['events' => $events]);
    }

    public function sendReminders()
    {
        $appointments = Appointment::where('status', 'Scheduled')
            ->whereBetween('appointment_date', [now(), now()->addDay()])
            ->get();

        foreach ($appointments as $appointment) {
            $user = $appointment->pet->owner->user;
            $user->notify(new AppointmentReminder($appointment));
        }

        return response()->json(['sent' => count($appointments)]);
    }

    /**
     * Return remaining appointment slots for a given datetime (owner selects date/time).
     * Expects 'date' param (any parseable datetime string). Parses as Asia/Manila.
     */
    public function remainingSlots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $requested = Carbon::parse($request->input('date'), 'Asia/Manila');
        $dayStartUtc = $requested->copy()->startOfDay()->setTimezone('UTC');
        $dayEndUtc = $requested->copy()->endOfDay()->setTimezone('UTC');

        $limits = config('appointment_limits', []);

        $remaining = [];
        foreach ($limits as $type => $limit) {
            $count = Appointment::where('type', $type)
                ->where('status', 'Scheduled')
                ->whereBetween('appointment_date', [$dayStartUtc, $dayEndUtc])
                ->count();
            $remaining[$type] = max(0, $limit - $count);
        }

        return response()->json(['remaining' => $remaining]);
    }
}
