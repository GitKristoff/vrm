<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Carbon\Carbon;


class VetController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Create profile if missing
        $veterinarian = $user->veterinarian()->firstOrCreate(
            [],
            [
                'user_id' => $user->id,
                'license_number' => 'TEMP-' . Str::uuid(),
                'specialization' => 'General Practice',
                'phone' => $user->phone ?? 'N/A',
            ]
        );

        // Get all appointments for this vet
        $allAppointments = $veterinarian->appointments()
            ->with('pet.owner.user')
            ->get();

        // Calculate counts
        $todaysAppointments = $allAppointments->filter(function($appointment) {
            return $appointment->appointment_date->isToday() &&
                   $appointment->status === 'Scheduled';
        })->count();

        $completedAppointments = $allAppointments->where('status', 'Completed')->count();
        $scheduledAppointments = $allAppointments->where('status', 'Scheduled')->count();

        // Get upcoming appointments for the table
        $upcomingAppointments = $veterinarian->appointments()
            ->with('pet.owner.user')
            ->where('status', 'Scheduled')
            ->where('appointment_date', '>=', Carbon::today())
            ->orderBy('appointment_date', 'asc')
            ->get();

        return view('vet.dashboard', [
            'todaysAppointments' => $todaysAppointments,
            'completedAppointments' => $completedAppointments,
            'scheduledAppointments' => $scheduledAppointments,
            'appointments' => $upcomingAppointments
        ]);
    }

    public function medicalRecords()
    {
        $user = Auth::user();
        $vetId = $user->veterinarian->id;

        $records = MedicalRecord::where('veterinarian_id', $vetId)
            ->with(['pet.owner.user', 'appointment'])
            ->orderBy('record_date', 'desc')
            ->paginate(10);

        return view('vet.records.index', compact('records'));
    }

    public function settings(): View
    {
        // Your logic for settings
        return view('vet.settings.index');
    }
}
