<?php

namespace App\Notifications;

use App\Models\Astrologer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AstrologerApplicationReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Astrologer $astrologer)
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
        $status = ucfirst($this->astrologer->verification_status ?? 'pending');
        $isApproved = ($this->astrologer->verification_status ?? '') === 'approved';

        return (new MailMessage)
            ->subject('Astrologer application update: '.$status)
            ->view('emails.astrologer-application-reviewed', [
                'status' => strtolower($this->astrologer->verification_status ?? 'pending'),
                'specialization' => $this->astrologer->specialization ?? 'N/A',
                'actionText' => $isApproved ? 'Go to Astrologer Dashboard' : 'View Application',
                'actionUrl' => $isApproved ? route('astrologer.dashboard') : route('astrologer.apply'),
                'outro' => $isApproved
                    ? 'Welcome aboard. We are excited to have you on AstroConnect.'
                    : 'You can update your profile and submit again if needed.',
            ]);
    }
}
