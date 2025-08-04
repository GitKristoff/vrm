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

        $vets = Veterinarian::with('user')->get();

        return view('appointments.create', compact('pets', 'vets', 'selectedPet'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'veterinarian_id' => 'required|exists:veterinarians,id',
            'appointment_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:15',
            'type' => 'required|string|max:50',
        ]);

        // Convert to Carbon instance
        $startTime = Carbon::parse($request->appointment_date);
        $endTime = $startTime->copy()->addMinutes((int)$request->duration_minutes);

        // Check vet availability
        $conflictingAppointments = Appointment::where('veterinarian_id', $request->veterinarian_id)
            ->where('status', 'Scheduled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('appointment_date', '<', $endTime)
                    ->whereRaw("DATE_ADD(appointment_date, INTERVAL duration_minutes MINUTE) > ?", [$startTime]);
                });
            })
            ->exists();

        if ($conflictingAppointments) {
            return back()->withInput()
                ->withErrors(['appointment_date' => 'The veterinarian is not available at this time. Please choose another time slot.']);
        }

        Log::info($request->all());

        $appointment = Appointment::create($request->all());

        // Send notifications
        $vet = Veterinarian::with('user')->find($request->veterinarian_id);
        $vet->user->notify(new NewAppointmentNotification($appointment));

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
                'start' => $appointment->appointment_date->format('Y-m-d\TH:i:s'),
                'end' => $appointment->appointment_date->copy()->addMinutes($appointment->duration_minutes)->format('Y-m-d\TH:i:s'),
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
}
