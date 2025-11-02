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
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:veterinarians,id',
            'appointment_date' => 'required|string', // adjust to your input shape
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

        // Accept the datetime-local value (no manual timezone conversion).
        // The Appointment model's mutator will parse this using config('app.timezone') and store UTC.
        $inputDate = $request->input('appointment_date'); // e.g. "2025-11-02T16:00" from <input type="datetime-local">

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'veterinarian_id' => $request->veterinarian_id,
            'appointment_date' => $inputDate,
            'reason' => $request->reason,
            'duration_minutes' => $request->duration_minutes,
            'type' => $request->type,
            'status' => 'scheduled',
            'approved' => false,
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

        // Require approval before check-in
        if (!$appointment->approved) {
            return back()->with('error', 'Appointment must be approved by the veterinarian before check-in.');
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

        // Require approval before storing check-in/medical record
        if (!$appointment->approved) {
            return back()->with('error', 'Appointment must be approved before completing.');
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

    /**
     * Veterinarian (or admin) approves an appointment so check-in is allowed.
     */
    public function approve(Appointment $appointment)
    {
        $user = Auth::user();

        // Only veterinarian assigned to the appointment or admin can approve
        if ($user->role === 'veterinarian') {
            if (!$user->veterinarian || $appointment->veterinarian_id !== $user->veterinarian->id) {
                abort(403);
            }
        } elseif ($user->role !== 'admin') {
            abort(403);
        }

        $appointment->approved = true;
        // Use normalized status value that matches DB (lowercase)
        $appointment->status = 'approved';
        $appointment->save();

        return back()->with('success', 'Appointment approved.');
    }
}
