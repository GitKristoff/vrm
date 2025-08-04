<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Support\Facades\Notification;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:reminders';
    protected $description = 'Send appointment reminders';

    public function handle()
    {
        $appointments = Appointment::where('status', 'Scheduled')
            ->whereBetween('appointment_date', [now(), now()->addDay()])
            ->with(['pet.owner.user', 'veterinarian.user'])
            ->get();

        foreach ($appointments as $appointment) {
            try {
                // Notify owner
                $appointment->pet->owner->user->notify(
                    new AppointmentReminder($appointment)
                );

                // Notify veterinarian
                $appointment->veterinarian->user->notify(
                    new AppointmentReminder($appointment)
                );

                $this->info("Sent reminder for appointment: {$appointment->id}");
            } catch (\Exception $e) {
                $this->error("Failed to send reminder for appointment {$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Sent {$appointments->count()} appointment reminders");
    }
}
