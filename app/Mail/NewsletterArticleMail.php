<?php

namespace App\Mail;

use App\Models\BlogPost;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterArticleMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BlogPost $post)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'مقال جديد: ' . $this->post->title);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.newsletter-article', with: [
            'post' => $this->post,
            'url'  => route('blog.show', $this->post->slug),
        ]);
    }
}
