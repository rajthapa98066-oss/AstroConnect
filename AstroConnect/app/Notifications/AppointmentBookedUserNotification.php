<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBookedUserNotification extends Notification
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
        $astrologerName = $this->appointment->astrologer?->user?->name ?? 'Your astrologer';

        return (new MailMessage)
            ->subject('Appointment request received')
            ->view('emails.appointment-booked-user', [
                'astrologerName' => $astrologerName,
                'topic' => $this->appointment->topic,
                'scheduledAt' => $this->appointment->scheduled_at?->format('M d, Y h:i A'),
                'status' => ucfirst($this->appointment->status),
                'actionUrl' => route('appointments.user.index'),
            ]);
    }
}
