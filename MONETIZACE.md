# Důchody.cz — Monetizační strategie

---

## Revenue model — přehled

```
Rok 1:
├── Google AdSense / display       ~20 %
├── Affiliate — penzijní fondy     ~50 %
├── Affiliate — DIP / investice    ~20 %
└── Přímá inzerce (banky)          ~10 %

Rok 2–3:
├── Affiliate (scaling)            ~45 %
├── Přímá inzerce                  ~25 %
├── Newsletter sponzoring          ~15 %
└── Premium / B2B                  ~15 %
```

---

## 1. Affiliate marketing

### Penzijní spoření (DPS) — nejvyšší CPA

**Partneři k oslovení:**
- Česká spořitelna Penzijní společnost (affiliate přes Finpartner nebo přímý kontakt)
- ČSOB Penzijní společnost
- Allianz penzijní společnost
- Conseq penzijní společnost
- NN Penzijní společnost
- Generali Česká penzijní společnost

**Typická CPA:** 800–2 500 Kč za novou smlouvu DPS

**Tracking implementace (PHP):**
```php
// app/Services/Affiliate/AffiliateLink.php
declare(strict_types=1);

namespace App\Services\Affiliate;

final class AffiliateLink
{
    public static function build(
        string $baseUrl,
        string $partnerId,
        string $source,
        string $medium = 'affiliate'
    ): string {
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl . $separator . http_build_query([
            'utm_source'   => 'duchody.cz',
            'utm_medium'   => $medium,
            'utm_campaign' => $source,
            'partner_id'   => $partnerId, // pro interní tracking
        ]);
    }
}
```

```javascript
// resources/js/analytics.js — GA4 event při kliknutí na affiliate odkaz
export function trackAffiliateClick(partner, product) {
  window.gtag?.('event', 'affiliate_click', {
    partner_name: partner,
    product_type: product,
    page_url: window.location.href,
  })
}
// Připojit přes data-atributy: na <a data-affiliate data-partner data-product>
// delegovaný listener v app.js zavolá trackAffiliateClick().
```

**Zobrazení affiliate odkazů (Blade komponenta):**
```blade
{{-- resources/views/components/affiliate/fund-cta.blade.php --}}
@props(['fund'])

@php
  $href = \App\Services\Affiliate\AffiliateLink::build(
      $fund->affiliate_url, $fund->partner_id, 'fund-detail'
  );
@endphp

<div class="border border-emerald-200 bg-emerald-50 rounded-xl p-6">
  <p class="font-semibold text-emerald-800">Sjednat {{ $fund->name }}</p>
  <p class="text-sm text-emerald-700 mt-1">
    Výnos za rok: {{ $fund->return_1y }} % · Poplatek: {{ $fund->fee_management }} % ročně
  </p>
  <a
    href="{{ $href }}"
    target="_blank"
    rel="noopener sponsored"
    data-affiliate
    data-partner="{{ $fund->company }}"
    data-product="DPS"
    class="mt-4 inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-5 py-3 rounded-lg transition-colors"
  >
    Sjednat online
    <svg class="h-4 w-4"><!-- ExternalLink ikona --></svg>
  </a>
  <p class="text-xs text-slate-500 mt-3">
    * Reklamní spolupráce. Tato informace neovlivňuje naše hodnocení.
  </p>
</div>
```

### DIP (Dlouhodobý investiční produkt) — nový trh

**Partneři:**
- Portu (portu.cz) — affiliate program, provize ~500–1 000 Kč
- Fondee — affiliate program
- XTB — affiliate za otevření účtu
- Degiro
- Interactive Brokers
- Banky (KB, ČSOB, Air Bank) — DIP účty

### Pojišťovny a banky

Přímé oslovení pro affiliate smlouvy:
- Životní pojištění
- Stavební spoření (Raiffeisen BS, ČMSS)

---

## 2. Display reklama

### Google AdSense implementace

AdSense řešíme jako **Vue island** (kvůli lazy-loadu přes IntersectionObserver) nebo jednoduchou Blade komponentu s malým inline skriptem. Místo `useEffect`/`useRef` používáme Vue lifecycle.

```vue
<!-- resources/js/components/ads/AdSense.vue -->
<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  slot: { type: String, required: true },
  format: { type: String, default: 'auto' },
})

const root = ref(null)
let observer = null

onMounted(() => {
  // Lazy load — načíst jen když viditelné
  observer = new IntersectionObserver(([entry]) => {
    if (entry.isIntersecting) {
      try {
        (window.adsbygoogle = window.adsbygoogle || []).push({})
      } catch (e) { /* noop */ }
      observer.disconnect()
    }
  }, { rootMargin: '200px' })

  if (root.value) observer.observe(root.value)
})

onUnmounted(() => observer?.disconnect())
</script>

<template>
  <div ref="root" class="min-h-[90px]">
    <ins
      class="adsbygoogle"
      style="display:block"
      data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"
      :data-ad-slot="slot"
      :data-ad-format="format"
      data-full-width-responsive="true"
    ></ins>
  </div>
</template>
```

**Umístění reklam (CLS-safe) — rezervovat místo v Blade:**
```blade
{{-- Banner --}}
<div class="min-h-[90px] my-6" data-ad data-slot="XXXX" data-format="horizontal"></div>

{{-- Rectangle --}}
<div class="min-h-[250px]" data-ad data-slot="YYYY" data-format="rectangle"></div>

{{-- app.js namountuje AdSense.vue na všechny [data-ad] elementy --}}
```

**Kde umístit reklamy:**
- Pod hero sekcí na homepage (horizontal banner)
- Sidebar na stránkách článků (rectangle)
- Mezi paragrafy v dlouhých článcích (in-article)
- Pod výsledky kalkulačky (NIKDY nad výsledky — degradace UX)

**Kde reklamy NEumísťovat:**
- Na kalkulačkových stránkách nad foldem
- Přerušující formulářové kroky
- Pop-up ani interstitial

### Přímá inzerce

**Cíloví inzerenti:**
- Komerční banky (KB, ČSOB, Air Bank, Česká spořitelna)
- Penzijní společnosti (přímé bannery)
- Pojišťovny (Allianz, Generali, NN)
- Brokeři (Portu, XTB, Degiro, Fondee)

**Ceník orientační (po dosažení 50 000 návštěv/měsíc):**
```
Billboard 970×250: 15 000 Kč/měsíc
Leaderboard 728×90: 8 000 Kč/měsíc
Rectangle 300×250: 6 000 Kč/měsíc
Newsletter sponzoring (10 000 odběratelů): 5 000 Kč/vydání
```

---

## 3. Newsletter monetizace

Newsletter řešíme přes **Laravel Mail** (Mailable třídy + SMTP/Mailgun/Postmark) a frontu (`QUEUE_CONNECTION=database`). Odběratele ukládáme do tabulky `newsletter_subscribers` (Eloquent model).

```php
// app/Http/Controllers/NewsletterController.php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Mail\WelcomeNewsletter;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

final class NewsletterController
{
    public function subscribe(Request $request)
    {
        $data = $request->validate([
            'email'  => ['required', 'email'],
            'source' => ['nullable', 'string'],
        ]);

        // Upsert do SQLite (firstOrCreate / updateOrCreate)
        $subscriber = NewsletterSubscriber::updateOrCreate(
            ['email' => $data['email']],
            ['source' => $data['source'] ?? 'web', 'subscribed_at' => now()],
        );

        // Odeslat welcome email (do fronty)
        Mail::to($subscriber->email)->queue(new WelcomeNewsletter($subscriber));

        return back()->with('status', 'Děkujeme! Potvrďte prosím přihlášení v e-mailu.');
    }
}
```

```php
// app/Mail/WelcomeNewsletter.php — Mailable s Blade šablonou
// subject: 'Vítejte v newsletteru Důchody.cz'
// view:    'emails.newsletter.welcome' (Blade + Markdown mail)
```

**Newsletter struktura (měsíčně):**
```
1. Aktuální čísla (valorizace, průměrné důchody, novinky)
2. Tip měsíce (jak zvýšit důchod, DPS, DIP)
3. [SPONZOR]: "Tento newsletter sponzoruje [XY Penzijní]"
4. Nové na webu (top 3 články)
5. Otázka čtenáře
```

> Double opt-in: po přihlášení poslat potvrzovací odkaz (signed URL přes `URL::signedRoute`), teprve po kliknutí nastavit `confirmed_at`.

---

## 4. Premium funkce (Fáze 4)

Platby přes **laravel/cashier (Stripe)** nebo přímo Stripe PHP SDK. Jednorázové platby za PDF report.

```php
// app/Http/Controllers/CheckoutController.php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

final class CheckoutController
{
    public function create(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'czk',
                    'product_data' => [
                        'name'        => 'Osobní důchodový plán PDF',
                        'description' => 'Detailní analýza vašeho důchodu s doporučeními',
                    ],
                    'unit_amount'  => 19900, // 199 Kč
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('kalkulacka.vyse', ['success' => 1]),
            'cancel_url'  => route('kalkulacka.vyse'),
        ]);

        return response()->json(['url' => $session->url]);
    }
}
```

> PDF report generujeme přes `barryvdh/laravel-dompdf` nebo `spatie/laravel-pdf` (Blade → PDF) — viz ROADMAP fáze 4.

---

## 5. B2B API (Fáze 4)

```php
// Embeddovatelný widget — iframe route s white-label parametry
// routes/web.php
Route::get('/embed/kalkulacka', [EmbedController::class, 'calculator'])->name('embed.kalkulacka');

// app/Http/Controllers/EmbedController.php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;

final class EmbedController
{
    public function calculator(Request $request)
    {
        // Přijímá URL parametry pro white-label:
        // ?primaryColor=%23059669&logoUrl=...&partnerName=...&affiliateId=...
        $config = [
            'primaryColor' => $request->query('primaryColor', '#059669'),
            'logoUrl'      => $request->query('logoUrl'),
            'partnerName'  => $request->query('partnerName', 'Důchody.cz'),
            'affiliateId'  => $request->query('affiliateId'), // tracking B2B partnerů
        ];

        // Bez layoutu/nav — čistá embed Blade šablona s Vue kalkulačkou
        return view('embed.calculator', ['config' => $config])
            ->withHeaders(['Content-Security-Policy' => "frame-ancestors *"]);
    }
}
```

**B2B pricing:**
```
Starter (do 10 000 výpočtů/měsíc): 990 Kč/měsíc
Pro (do 100 000 výpočtů/měsíc): 4 900 Kč/měsíc
Enterprise (neomezeno + vlastní branding): dohodou
```

---

## Právní požadavky

### Affiliate disclosures (zákonná povinnost)
Každá affiliate stránka musí mít viditelný disclosure v souladu s § 2 zákona č. 40/1995 Sb. a GDPR:

```blade
{{-- Přidat na každou stránku s affiliate obsahem --}}
<div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800 my-4">
  <strong>Reklamní sdělení:</strong> Tento web spolupracuje s poskytovateli finančních produktů
  a může obdržet provizi za sjednání produktu. Tato skutečnost neovlivňuje naši nezávislost,
  hodnocení ani pořadí produktů.
</div>
```

### Cookies a GDPR
- Cookie consent banner (při AdSense je povinný)
- Privacy policy stránka
- Google Consent Mode v2 implementace
- Ochrana osobních dat uživatelů — validace vstupů (Form Requests), CSRF ochrana (Laravel default), session-based auth (Breeze), žádné zbytečné ukládání citlivých dat
