<?php

namespace App\Notifications;

use App\Models\AstrologerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AstrologerReportResolvedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly AstrologerReport $report)
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
        $resolution = ucfirst($this->report->resolution ?? 'resolved');
        $astrologerName = $this->report->astrologer?->user?->name ?? 'the reported astrologer';

        return (new MailMessage)
            ->subject('Your report has been reviewed')
            ->view('emails.astrologer-report-resolved', [
                'astrologerName' => $astrologerName,
                'resolution' => $resolution,
                'adminNote' => $this->report->admin_note ?? 'No additional note provided.',
                'actionUrl' => route('astrologers.index'),
            ]);
    }
}
