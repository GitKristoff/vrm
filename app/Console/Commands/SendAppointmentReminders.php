<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:reminders';
    protected $description = 'Send appointment reminders';

    public function handle()
    {
        $nowManila = now('Asia/Manila');
        $tomorrowManila = $nowManila->copy()->addDay();

        $appointments = Appointment::where('status', 'Scheduled')
            ->whereBetween('appointment_date', [
                $nowManila->format('Y-m-d H:i:s'),
                $tomorrowManila->format('Y-m-d H:i:s')
            ])
            ->with(['pet.owner.user', 'veterinarian.user'])
            ->get();

        Log::info('Manila Reminder time window', [
            'start' => $nowManila->format('Y-m-d H:i:s'),
            'end' => $tomorrowManila->format('Y-m-d H:i:s')
        ]);
        Log::info('Found appointments:', ['count' => $appointments->count()]);

        foreach ($appointments as $appointment) {
            // Convert appointment_date to Carbon instance if not already
            $appointmentDate = $appointment->appointment_date instanceof Carbon
                ? $appointment->appointment_date
                : Carbon::parse($appointment->appointment_date, 'UTC');

            // Convert to Manila time for notifications
            $appointmentDateManila = $appointmentDate->setTimezone('Asia/Manila');

            // Log appointment details
            Log::info('Processing appointment', [
                'id' => $appointment->id,
                'appointment_date_utc' => $appointmentDate->format('Y-m-d H:i:s'),
                'appointment_date_manila' => $appointmentDateManila->format('Y-m-d H:i:s'),
                'status' => $appointment->status,
            ]);

            // Check for missing relationships
            if (!$appointment->pet || !$appointment->pet->owner || !$appointment->pet->owner->user) {
                Log::error('Missing owner relationship', ['appointment_id' => $appointment->id]);
                continue;
            }
            if (!$appointment->veterinarian || !$appointment->veterinarian->user) {
                Log::error('Missing veterinarian relationship', ['appointment_id' => $appointment->id]);
                continue;
            }

            // Check if within Manila's next 24 hours
            if ($appointmentDateManila->between($nowManila, $tomorrowManila)) {
                try {
                    $appointment->pet->owner->user->notify(new AppointmentReminder($appointment));
                    $appointment->veterinarian->user->notify(new AppointmentReminder($appointment));

                    Log::info("Sent email to: " . $appointment->pet->owner->user->email .
                            " and " . $appointment->veterinarian->user->email);

                    $this->info("Sent reminder for appointment: {$appointment->id}");

                } catch (\Exception $e) {
                    Log::error("Failed to send reminder", [
                        'appointment_id' => $appointment->id,
                        'error' => $e->getMessage(),
                    ]);
                    $this->error("Failed to send reminder for appointment {$appointment->id}: " . $e->getMessage());
                }
            }
        }

        $this->info("Sent {$appointments->count()} appointment reminders");
    }
}
