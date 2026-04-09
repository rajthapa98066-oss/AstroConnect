<?php

namespace App\Notifications;

use App\Models\Blog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BlogSubmittedForReviewAdminNotification extends Notification
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
        $author = $this->blog->astrologer?->user?->name ?? 'Astrologer';

        return (new MailMessage)
            ->subject('Blog submitted for review')
            ->view('emails.blog-submitted-for-review', [
                'authorName' => $author,
                'title' => $this->blog->title,
                'category' => $this->blog->category ?? 'N/A',
                'status' => ucfirst($this->blog->review_status ?? 'pending'),
                'actionUrl' => route('admin.blogs.index'),
            ]);
    }
}
