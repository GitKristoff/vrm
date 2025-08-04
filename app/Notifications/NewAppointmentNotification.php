<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewAppointmentNotification extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('New Appointment Scheduled')
            ->line('A new appointment has been booked:')
            ->line('Pet: ' . $this->appointment->pet->name)
            ->line('Date: ' . $this->appointment->appointment_date->format('M d, Y h:i A'))
            ->line('Reason: ' . $this->appointment->reason)
            ->action('View Appointment', route('appointments.show', $this->appointment));
    }

    public function toArray($notifiable)
    {
        return [
            'appointment_id' => $this->appointment->id,
            'pet_name' => $this->appointment->pet->name,
            'date' => $this->appointment->appointment_date->format('M d, Y h:i A'),
            'message' => 'New appointment scheduled for ' . $this->appointment->pet->name,
        ];
    }
}
