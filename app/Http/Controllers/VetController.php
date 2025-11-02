<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Models\Veterinarian;
use App\Models\Appointment;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Carbon\Carbon;


class VetController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $isAdmin = false;

        if ($user->role === 'veterinarian') {
            $veterinarian = $user->veterinarian;
            $isAdmin = $veterinarian && $veterinarian->is_admin;
        }

        // Create profile if missing
        $veterinarian = $user->veterinarian()->firstOrCreate(
            [],
            [
            ]
        );

        // Admin stats (only for admin veterinarians)
        $adminStats = null;
        if ($isAdmin) {
            $adminStats = [
            ];
        }

        // Regular veterinarian stats
        $todaysAppointments = 0;
        $completedAppointments = 0;
        $scheduledAppointments = 0;
        $appointments = collect();

        if (!$isAdmin) {
            // load appointments as a Collection (eager load relations used in views)
            $appointments = $veterinarian->appointments()->with(['pet', 'pet.owner.user'])->get();

            $todayManila = Carbon::now('Asia/Manila');

            // Count today's appointments (compare in Asia/Manila)
            $todaysAppointments = $appointments->filter(function($appointment) use ($todayManila) {
                if (! $appointment->appointment_date) return false;
                $apptLocal = $appointment->appointment_date->setTimezone('Asia/Manila');
                return $apptLocal->isSameDay($todayManila);
            })->count();

            // Use case-insensitive status checks to avoid mismatches
            $completedAppointments = $appointments->filter(fn($a) => strtolower($a->status) === 'completed')->count();
            $scheduledAppointments = $appointments->filter(fn($a) => strtolower($a->status) === 'scheduled')->count();
        }

        // --- NEW: compute remaining limits for today ---
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
        // --- /NEW ---

        return view('vet.dashboard', [
            'isAdmin' => $isAdmin,
            'adminStats' => $adminStats,
            'todaysAppointments' => $todaysAppointments,
            'completedAppointments' => $completedAppointments,
            'scheduledAppointments' => $scheduledAppointments,
            'appointments' => $appointments,
            'remainingLimits' => $remainingLimits
        ]);
    }

        public function medicalRecords()
    {
        $user = Auth::user();

        // Check if veterinarian is admin
        $isAdmin = false;
        $vetId = null;

        if ($user->role === 'veterinarian') {
            $vetId = $user->veterinarian->id;
            $isAdmin = $user->veterinarian->is_admin;
        }

        // If admin, show all records. Otherwise, only show their records.
        if ($isAdmin) {
            $records = MedicalRecord::with(['pet.owner.user', 'appointment', 'veterinarian.user'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        } else {
            $records = MedicalRecord::where('veterinarian_id', $vetId)
                ->with(['pet.owner.user', 'appointment'])
                ->orderBy('record_date', 'desc')
                ->paginate(10);
        }

        return view('vet.records.index', compact('records', 'isAdmin'));
    }

    public function settings(): View
    {
        $user = Auth::user();
        $vet = $user->veterinarian; // <-- Add this line
        return view('vet.settings', compact('vet'));
    }

    public function updateSettings(Request $request)
    {
        // Normalize time inputs to H:i before validation
        if ($request->start_time) {
            $request->merge([
                'start_time' => \Carbon\Carbon::parse($request->start_time)->format('H:i')
            ]);
        }
        if ($request->end_time) {
            $request->merge([
                'end_time' => \Carbon\Carbon::parse($request->end_time)->format('H:i')
            ]);
        }

        $request->validate([
            'working_days' => 'array',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'status' => 'required|in:in,out,on leave',
        ]);

        $vet = Auth::user()->veterinarian()->first();

        if ($vet) {
            $vet->working_days = $request->working_days;
            $vet->start_time = $request->start_time;
            $vet->end_time = $request->end_time;
            $vet->status = $request->status;
            $vet->save();
        }

        return redirect()->route('vet.settings')->with('success', 'Settings updated!');
    }
}
