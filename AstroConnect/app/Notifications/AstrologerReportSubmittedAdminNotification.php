<?php

namespace App\Notifications;

use App\Models\AstrologerReport;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AstrologerReportSubmittedAdminNotification extends Notification
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
        $reporterName = $this->report->reporter?->name ?? 'Verified user';
        $astrologerName = $this->report->astrologer?->user?->name ?? 'Astrologer';

        return (new MailMessage)
            ->subject('New astrologer report submitted')
            ->view('emails.astrologer-report-submitted', [
                'reporterName' => $reporterName,
                'astrologerName' => $astrologerName,
                'reason' => $this->report->reason,
                'status' => ucfirst($this->report->status),
                'actionUrl' => route('admin.reports.index'),
            ]);
    }
}
