<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmMail;
use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

final class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // DNS kontrola e-mailu jen mimo testy (testovací domény nemají MX záznamy)
        $emailRule = app()->environment('testing') ? 'email:rfc' : 'email:rfc,dns';

        $validated = $request->validate(
            ['email' => ['required', $emailRule, 'max:255']],
            [
                'email.required' => 'Prosím vyplňte e-mailovou adresu.',
                'email.email'    => 'Zadejte platnou e-mailovou adresu.',
            ],
        );

        $subscriber = NewsletterSubscriber::firstOrCreate(['email' => $validated['email']]);

        if ($subscriber->isConfirmed()) {
            return redirect()->back()
                ->with('newsletter_success', 'Tento e-mail už newsletter odebírá. Děkujeme!');
        }

        // Opakované přihlášení po odhlášení — restart double opt-in
        if ($subscriber->unsubscribed_at !== null) {
            $subscriber->update(['unsubscribed_at' => null, 'confirmed_at' => null]);
        }

        Mail::to($subscriber->email)->queue(new NewsletterConfirmMail($subscriber));

        return redirect()->back()
            ->with('newsletter_success', "Skoro hotovo! Na {$subscriber->email} jsme poslali potvrzovací odkaz.");
    }

    public function confirm(NewsletterSubscriber $subscriber): RedirectResponse
    {
        if (! $subscriber->isConfirmed()) {
            $subscriber->update(['confirmed_at' => now(), 'unsubscribed_at' => null]);

            Mail::to($subscriber->email)->queue(new NewsletterWelcomeMail($subscriber));
        }

        return redirect()->route('home')
            ->with('newsletter_success', 'Odběr newsletteru je potvrzen. Vítejte!');
    }

    public function unsubscribe(NewsletterSubscriber $subscriber): RedirectResponse
    {
        $subscriber->update(['unsubscribed_at' => now()]);

        return redirect()->route('home')
            ->with('newsletter_success', 'Odběr newsletteru byl zrušen. Mrzí nás to — kdykoli se můžete přihlásit znovu.');
    }
}
