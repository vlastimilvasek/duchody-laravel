<?php

declare(strict_types=1);

use App\Mail\NewsletterConfirmMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('stores a pending subscriber and queues confirmation mail', function () {
    Mail::fake();

    $this->post('/newsletter', ['email' => 'jan.novak@example.com'])
        ->assertRedirect()
        ->assertSessionHas('newsletter_success');

    $subscriber = NewsletterSubscriber::where('email', 'jan.novak@example.com')->first();

    expect($subscriber)->not->toBeNull()
        ->and($subscriber->confirmed_at)->toBeNull();

    Mail::assertQueued(NewsletterConfirmMail::class, fn ($mail) => $mail->hasTo('jan.novak@example.com'));
});

it('confirms subscription via signed url and queues welcome mail', function () {
    Mail::fake();

    $subscriber = NewsletterSubscriber::create(['email' => 'jan.novak@example.com']);

    $url = URL::temporarySignedRoute('newsletter.confirm', now()->addDay(), ['subscriber' => $subscriber->id]);

    $this->get($url)->assertRedirect(route('home'));

    expect($subscriber->fresh()->isConfirmed())->toBeTrue();

    Mail::assertQueued(NewsletterWelcomeMail::class);
});

it('rejects confirmation with invalid signature', function () {
    $subscriber = NewsletterSubscriber::create(['email' => 'jan.novak@example.com']);

    $this->get(route('newsletter.confirm', ['subscriber' => $subscriber->id]))
        ->assertForbidden();

    expect($subscriber->fresh()->confirmed_at)->toBeNull();
});

it('does not resend confirmation to already confirmed subscriber', function () {
    Mail::fake();

    NewsletterSubscriber::create(['email' => 'jan.novak@example.com', 'confirmed_at' => now()]);

    $this->post('/newsletter', ['email' => 'jan.novak@example.com'])
        ->assertRedirect()
        ->assertSessionHas('newsletter_success');

    Mail::assertNothingQueued();
});

it('unsubscribes via signed url', function () {
    $subscriber = NewsletterSubscriber::create(['email' => 'jan.novak@example.com', 'confirmed_at' => now()]);

    $url = URL::signedRoute('newsletter.unsubscribe', ['subscriber' => $subscriber->id]);

    $this->get($url)->assertRedirect(route('home'));

    expect($subscriber->fresh()->unsubscribed_at)->not->toBeNull()
        ->and($subscriber->fresh()->isConfirmed())->toBeFalse();
});

it('restarts double opt-in after resubscribe', function () {
    Mail::fake();

    $subscriber = NewsletterSubscriber::create([
        'email'           => 'jan.novak@example.com',
        'confirmed_at'    => now()->subMonth(),
        'unsubscribed_at' => now()->subDay(),
    ]);

    $this->post('/newsletter', ['email' => 'jan.novak@example.com'])->assertRedirect();

    expect($subscriber->fresh()->confirmed_at)->toBeNull()
        ->and($subscriber->fresh()->unsubscribed_at)->toBeNull();

    Mail::assertQueued(NewsletterConfirmMail::class);
});

it('rejects invalid email', function () {
    $this->from('/')->post('/newsletter', ['email' => 'neplatny-email'])
        ->assertRedirect('/')
        ->assertSessionHasErrors('email');
});
