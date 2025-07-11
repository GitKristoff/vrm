<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\MedicalRecord;
use App\Models\Veterinarian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

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
            'duration_minutes' => 'required|integer|min:15'
        ]);

        Appointment::create($request->all());

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

        return view('appointments.show', compact('appointment'));
    }

    // public function edit(Appointment $appointment)
    // {
    //     $pets = Pet::all();
    //     $vets = Veterinarian::with('user')->get();

    //     return view('appointments.edit', compact('appointment', 'pets', 'vets'));
    // }

    public function update(Request $request, Appointment $appointment)
    {
        // $request->validate([
        //     'status' => 'required|in:Scheduled,Completed,Cancelled,No-show',
        //     'notes' => 'nullable|string'
        // ]);

        // $appointment->update($request->only('status', 'notes'));

        // return redirect()->route('appointments.index')
        //     ->with('success', 'Appointment updated successfully!');

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

    // public function checkin(Appointment $appointment)
    // {
    //     // Authorization
    //     if (Auth::user()->role !== 'veterinarian' ||
    //         $appointment->veterinarian_id !== Auth::user()->veterinarian->id) {
    //         abort(403);
    //     }

    //     // Only allow check-in for scheduled appointments
    //     if ($appointment->status === 'Scheduled') {
    //         $appointment->update(['status' => 'Completed']);
    //         return back()->with('success', 'Appointment marked as completed!');
    //     }

    //     return back()->with('error', 'Only scheduled appointments can be checked in');
    // }

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

        $request->validate([
            'subjective_notes' => 'required|string',
            'objective_notes' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|numeric',
            'respiratory_rate' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'vaccination_history' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string',
            'medications.*.dosage' => 'required|string',
            'medications.*.frequency' => 'required|string',
            'medications.*.start_date' => 'required|date',
            'medications.*.end_date' => 'required|date',
            'medications.*.purpose' => 'required|string',
        ]);

        // Create medical record
        $medicalRecord = MedicalRecord::create([
            'pet_id' => $appointment->pet_id,
            'veterinarian_id' => $appointment->veterinarian_id,
            'appointment_id' => $appointment->id,
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
        ]);

        // Create medications
        if ($request->filled('medications')) {
            foreach ($request->medications as $medication) {
                $medicalRecord->medications()->create([
                    'name' => $medication['name'],
                    'dosage' => $medication['dosage'],
                    'frequency' => $medication['frequency'],
                    'start_date' => $medication['start_date'],
                    'end_date' => $medication['end_date'],
                    'purpose' => $medication['purpose'],
                ]);
            }
        }

        // Update appointment status
        $appointment->update(['status' => 'Completed']);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment completed and medical record created!');
    }
}
