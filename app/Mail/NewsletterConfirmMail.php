<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\NewsletterSubscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

final class NewsletterConfirmMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Potvrďte odběr newsletteru Důchody.cz',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.newsletter.confirm',
            with: [
                'confirmUrl' => URL::temporarySignedRoute(
                    'newsletter.confirm',
                    now()->addDays(7),
                    ['subscriber' => $this->subscriber->id],
                ),
            ],
        );
    }
}
