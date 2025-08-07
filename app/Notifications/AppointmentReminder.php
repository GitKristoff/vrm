<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        // Convert to Manila time
        $appointmentDate = $this->appointment->appointment_date
            ->setTimezone('Asia/Manila')
            ->format('M d, Y h:i A');

        return (new MailMessage)
            ->subject('Appointment Reminder: ' . $this->appointment->pet->name)
            ->view('emails.appointment-reminder', [
                'appointment' => $this->appointment,
                'appointmentDate' => $appointmentDate
            ]);
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'pet_name' => $this->appointment->pet->name,
            'date' => $this->appointment->appointment_date
                ->setTimezone('Asia/Manila')
                ->format('M d, Y h:i A'),
            'message' => 'Appointment reminder for ' . $this->appointment->pet->name,
        ];
    }
}
