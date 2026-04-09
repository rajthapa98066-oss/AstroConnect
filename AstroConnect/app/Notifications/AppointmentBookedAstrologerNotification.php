<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedAstrologerNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Appointment $appointment)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->appointment->user?->name ?? 'A user';

        return (new MailMessage)
            ->subject('New appointment request')
            ->view('emails.appointment-booked-astrologer', [
                'clientName' => $clientName,
                'topic' => $this->appointment->topic,
                'scheduledAt' => $this->appointment->scheduled_at?->format('M d, Y h:i A'),
                'actionUrl' => route('astrologer.appointments'),
            ]);
    }
}
