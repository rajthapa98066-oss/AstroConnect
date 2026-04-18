<?php

namespace App\Notifications;

use App\Models\Astrologer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AstrologerApplicationSubmittedAdminNotification extends Notification
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
        $applicant = $this->astrologer->user?->name ?? 'Unknown user';

        return (new MailMessage)
            ->subject('New astrologer application submitted')
            ->view('emails.astrologer-application-submitted', [
                'applicantName' => $applicant,
                'specialization' => $this->astrologer->specialization ?? 'N/A',
                'experienceYears' => $this->astrologer->experience_years ?? 0,
                'status' => ucfirst($this->astrologer->verification_status ?? 'pending'),
                'actionUrl' => route('admin.astrologers.index'),
            ]);
    }
}
