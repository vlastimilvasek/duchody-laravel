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

final class NewsletterWelcomeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly NewsletterSubscriber $subscriber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Vítejte v newsletteru Důchody.cz',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.newsletter.welcome',
            with: [
                'unsubscribeUrl' => URL::signedRoute(
                    'newsletter.unsubscribe',
                    ['subscriber' => $this->subscriber->id],
                ),
            ],
        );
    }
}
