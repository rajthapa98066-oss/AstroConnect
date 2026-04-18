<?php

namespace App\Notifications;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogReviewOutcomeNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Blog $blog)
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
        $status = ucfirst($this->blog->review_status ?? 'pending');
        $approved = ($this->blog->review_status ?? '') === 'approved';

        return (new MailMessage)
            ->subject('Blog review update: '.$status)
            ->view('emails.blog-review-outcome', [
                'status' => strtolower($this->blog->review_status ?? 'pending'),
                'title' => $this->blog->title,
                'actionText' => 'Open My Blogs',
                'actionUrl' => route('astrologer.blogs.index'),
                'outro' => $approved
                    ? 'Keep sharing your astrological insights with the AstroConnect community.'
                    : 'Please revise and resubmit your blog when ready.',
            ]);
    }
}
