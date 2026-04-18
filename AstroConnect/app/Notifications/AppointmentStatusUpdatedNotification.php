<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusUpdatedNotification extends Notification
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
        $astrologerName = $this->appointment->astrologer?->user?->name ?? 'your astrologer';
        $status = ucfirst($this->appointment->status);

        return (new MailMessage)
            ->subject('Appointment status updated: '.$status)
            ->view('emails.appointment-status-updated', [
                'astrologerName' => $astrologerName,
                'topic' => $this->appointment->topic,
                'scheduledAt' => $this->appointment->scheduled_at?->format('M d, Y h:i A'),
                'status' => $status,
                'actionUrl' => route('appointments.user.index'),
            ]);
    }
}
