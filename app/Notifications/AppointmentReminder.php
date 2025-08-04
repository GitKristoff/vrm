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
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Appointment Reminder: ' . $this->appointment->pet->name)
            ->line('You have an upcoming appointment for ' . $this->appointment->pet->name)
            ->line('Date: ' . $this->appointment->appointment_date->format('M d, Y h:i A'))
            ->line('Veterinarian: ' . $this->appointment->veterinarian->user->name)
            ->action('View Appointment', route('appointments.show', $this->appointment))
            ->line('Thank you for using our veterinary service!');
    }
}
