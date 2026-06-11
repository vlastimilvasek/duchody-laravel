# Důchody.cz — SEO & Content strategie

---

## Keyword mapa — prioritní klíčová slova

### Tier 1 — Hlavní (vysoký objem, transakcional/kalkulační záměr)

| Klíčové slovo | Objem/měs | Záměr | Cílová URL |
|---------------|-----------|-------|------------|
| kdy půjdu do důchodu | ~18 000 | kalkulační | /kalkulacka/vek |
| výpočet důchodu | ~12 000 | kalkulační | /kalkulacka/vyse |
| kalkulačka důchodu | ~9 000 | kalkulační | /kalkulacka |
| důchodový věk | ~8 000 | informační | /pruvodce/duchodovy-vek |
| valorizace důchodů 2026 | ~15 000 | informační | /magazin/valorizace-2026 |
| kolik budu mít důchod | ~7 000 | kalkulační | /kalkulacka/vyse |
| předčasný důchod | ~6 000 | informační | /pruvodce/predcasny-duchod |
| penzijní spoření srovnání | ~4 000 | srovnávací | /fondy |

### Tier 2 — Středně objemová (long-tail, vyšší konverze)

| Klíčové slovo | Objem/měs | Cílová URL |
|---------------|-----------|------------|
| průměrný důchod 2026 | ~3 500 | /statistiky |
| důchod ročník 1965 | ~2 000 | /duchod/rocnik/1965 |
| minimální důchod 2026 | ~2 800 | /pruvodce/minimalni-duchod |
| penzijní spoření státní příspěvek | ~2 200 | /pruvodce/penzijni-sporeni |
| kdy jít do předčasného důchodu kalkulačka | ~1 800 | /kalkulacka/predcasny |
| důchod pro ženy s dětmi | ~1 500 | /pruvodce/duchod-zeny |
| výchovné důchod | ~2 100 | /pruvodce/vychovne |
| DIP daňová úspora | ~1 200 | /dip |

### Tier 3 — Programatické SEO (tisíce URL)

```
/duchod/rocnik/{rok}      — 1940–1980 (41 stránek)
/statistiky/{kraj}        — 14 krajů + ČR celkem
/fondy/{nazev-fondu}      — ~20 fondů
/dip/{poskytovatel}       — ~15 poskytovatelů
```

---

## Programatické SEO — šablony

### `/duchod/rocnik/{rok}`

Route s parametrem + controller, který validuje rozsah a předá data do Blade view. Odpovědi cachujeme (roční revalidace) přes `Cache::remember` nebo `spatie/laravel-responsecache`.

```php
// routes/web.php
Route::get('/duchod/rocnik/{rok}', [RocnikController::class, 'show'])
    ->whereNumber('rok')
    ->name('duchod.rocnik');

// app/Http/Controllers/Seo/RocnikController.php
declare(strict_types=1);

namespace App\Http\Controllers\Seo;

use Illuminate\Support\Facades\Cache;

final class RocnikController
{
    public function show(int $rok)
    {
        abort_unless($rok >= 1940 && $rok <= 1980, 404);

        $data = Cache::remember("rocnik.$rok", now()->addYear(), function () use ($rok) {
            return [
                'rok'           => $rok,
                'retirementAge' => \App\Services\Pension\RetirementAge::byFormula($rok),
                // průměrné roky pojištění generace, FAQ atd.
            ];
        });

        return view('seo.rocnik', [
            ...$data,
            'meta' => [
                'title'       => "Důchod pro ročník {$rok} — kdy a kolik | Důchody.cz",
                'description' => "Zjistěte, kdy půjde do důchodu ročník {$rok}, jaký je důchodový věk a průměrná výše důchodu pro vaši generaci.",
                'canonical'   => route('duchod.rocnik', $rok),
            ],
        ]);
    }
}
```

**Obsah stránky:**
- Headline: "Důchod pro ročník {rok}"
- Inline kalkulačka (Vue island — datum + pohlaví → přesný datum)
- Tabulka: důchodový věk pro muže, ženy dle počtu dětí
- Průměrné roky pojištění pro daný ročník (historická data)
- Odpovědi na FAQ specifické pro generaci
- Related: ročník ±5 let

### `/statistiky/{kraj}`

**Obsah:**
- Název kraje + mapa zvýraznění
- Průměrný starobní/invalidní/vdovský důchod
- Srovnání s celostátním průměrem (± Kč a %)
- Vývoj za poslední 5 let (mini LineChart — Vue/Chart.js)
- Top 3 okresy v kraji dle průměru
- Odkaz na celostátní mapu

---

## Technické SEO

### Meta tagy — vzor (Blade)

Sdílenou logiku řešíme partialem `layouts/partials/meta.blade.php`, který přijímá pole `$meta`. Každý controller předá `meta` do view.

```blade
{{-- resources/views/layouts/partials/meta.blade.php --}}
@props(['meta' => []])

<title>{{ $meta['title'] ?? 'Důchody.cz' }}</title>
<meta name="description" content="{{ $meta['description'] ?? '' }}">
<link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
<meta name="robots" content="{{ $meta['robots'] ?? 'index, follow' }}">

{{-- OpenGraph --}}
<meta property="og:title" content="{{ $meta['ogTitle'] ?? ($meta['title'] ?? 'Důchody.cz') }}">
<meta property="og:description" content="{{ $meta['description'] ?? '' }}">
<meta property="og:type" content="{{ $meta['ogType'] ?? 'website' }}">
<meta property="og:locale" content="cs_CZ">
<meta property="og:site_name" content="Důchody.cz">
<meta property="og:image" content="{{ $meta['ogImage'] ?? route('og.default') }}">

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] ?? 'Důchody.cz' }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">

{{-- Použití v layoutu --}}
{{-- <head> ... <x-layout.meta :meta="$meta" /> ... </head> --}}
```

> Každá stránka MUSÍ mít: `title` (max 60 znaků), `description` (max 160 znaků s klíčovým slovem přirozeně) a `canonical`.

> Dynamický OG obrázek: route `/og` renderuje obrázek přes `spatie/browsershot` (Blade → screenshot) nebo statickou šablonu. Cachovat na disk.

### Strukturovaná data — vzory (Blade)

JSON-LD vkládáme jako `<script type="application/ld+json">`. Pole sestavíme v PHP a vypíšeme přes `@json` (bezpečné escapování).

```blade
{{-- Kalkulačka — WebApplication --}}
@php
  $webApp = [
    '@context'            => 'https://schema.org',
    '@type'               => 'WebApplication',
    'name'                => 'Kalkulačka důchodu',
    'applicationCategory' => 'FinanceApplication',
    'description'         => 'Bezplatná online kalkulačka pro výpočet výše starobního důchodu v ČR',
    'url'                 => route('kalkulacka.vyse'),
    'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'CZK'],
    'inLanguage'          => 'cs',
    'operatingSystem'     => 'Web',
  ];
@endphp
<script type="application/ld+json">@json($webApp)</script>
```

```php
// FAQ stránka — sestaveno v controlleru, předáno do view
$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => collect($faqs)->map(fn ($faq) => [
        '@type'          => 'Question',
        'name'           => $faq['question'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
    ])->all(),
];

// Článek
$articleSchema = [
    '@context'      => 'https://schema.org',
    '@type'         => 'Article',
    'headline'      => $article->title,
    'description'   => $article->perex,
    'datePublished' => $article->published_at?->toIso8601String(),
    'dateModified'  => $article->updated_at?->toIso8601String(),
    'author'        => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
    'publisher'     => [
        '@type' => 'Organization',
        'name'  => 'Důchody.cz',
        'logo'  => ['@type' => 'ImageObject', 'url' => asset('logo.png')],
    ],
];
```

```blade
{{-- Vykreslení ve view --}}
<script type="application/ld+json">@json($faqSchema)</script>
```

### Sitemap

Generujeme `sitemap.xml` přes route + `spatie/laravel-sitemap`, nebo vlastní controller. Doporučeno generovat plánovaně (scheduler) a servírovat statický soubor.

```php
// app/Console/Commands/GenerateSitemap.php — spouštěno scheduler-em (denně)
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

$sitemap = Sitemap::create();

// Statické stránky
$static = [
    ['/', 1.0, 'weekly'],
    ['/kalkulacka', 0.9, 'weekly'],
    ['/kalkulacka/vek', 0.9, 'weekly'],
    ['/kalkulacka/vyse', 0.9, 'weekly'],
    ['/fondy', 0.8, 'weekly'],
    ['/mapa', 0.7, 'monthly'],
    ['/statistiky', 0.7, 'monthly'],
    ['/magazin', 0.8, 'daily'],
];
foreach ($static as [$url, $priority, $freq]) {
    $sitemap->add(Url::create($url)->setPriority($priority)->setChangeFrequency($freq));
}

// Programatické — ročníky
for ($rok = 1940; $rok <= 1980; $rok++) {
    $sitemap->add(Url::create("/duchod/rocnik/{$rok}")->setPriority(0.6)->setChangeFrequency('yearly'));
}

// Programatické — kraje
foreach (config('regions') as $kraj) {
    $sitemap->add(Url::create("/statistiky/{$kraj['slug']}")->setPriority(0.5)->setChangeFrequency('monthly'));
}

// Články z DB
\App\Models\Article::whereNotNull('published_at')->each(function ($a) use ($sitemap) {
    $sitemap->add(
        Url::create("/magazin/{$a->slug}")
            ->setLastModificationDate($a->updated_at)
            ->setPriority(0.7)
            ->setChangeFrequency('weekly')
    );
});

$sitemap->writeToFile(public_path('sitemap.xml'));
```

```php
// app/Console/Kernel.php — plánování
$schedule->command('sitemap:generate')->daily();
```

`robots.txt` umístíme do `public/robots.txt` s odkazem na `Sitemap: https://duchody.cz/sitemap.xml`.

---

## Content calendar — launch obsah

### Pillar pages (průvodci, evergreen)

1. **Vše o starobním důchodu v ČR 2026** (3500 slov)
   - Podmínky nároku, výpočet, žádost, timeline
   - Cílí na: "starobní důchod", "jak získat důchod"

2. **Důchodový věk 2026: kompletní tabulky** (2000 slov)
   - Tabulky dle ročníku a pohlaví, kalkulačka inline
   - Cílí na: "důchodový věk", "kdy půjdu do důchodu"

3. **Výpočet důchodu krok po kroku** (2500 slov)
   - OVZ, redukční hranice, procentní výměra — vysvětleno lidsky
   - Cílí na: "jak se počítá důchod", "výpočet důchodu vzorec"

4. **Penzijní spoření 2026: průvodce** (3000 slov)
   - DPS vs transformovaný fond, DIP, stavební spoření
   - Cílí na: "penzijní spoření jak funguje"

5. **Předčasný důchod: kdy se vyplatí?** (2000 slov)
   - Podmínky, krácení, kalkulačka inline
   - Cílí na: "předčasný důchod podmínky 2026"

6. **Valorizace důchodů: jak funguje?** (1500 slov)
   - Vzorec, historický vývoj, prognóza
   - Cílí na: "valorizace důchodu", "jak se počítá valorizace"

7. **Výchovné k důchodu 2026** (1200 slov)
   - Kdo má nárok, kolik, jak požádat
   - Cílí na: "výchovné důchod", "500 Kč za dítě důchod"

8. **Minimální a maximální důchod 2026** (1000 slov)
   - Konkrétní čísla, kdo dostane minimum
   - Cílí na: "minimální důchod 2026"

9. **Důchod pro OSVČ: co potřebujete vědět** (2000 slov)
   - Odlišnosti, dobrovolné pojištění, výpočet
   - Cílí na: "důchod OSVČ", "penzijní pojištění OSVČ"

10. **Invalidní důchod: podmínky a výše** (2000 slov)
    - 3 stupně, výpočet, žádost
    - Cílí na: "invalidní důchod podmínky"

### Aktuální obsah (updateable)

- Valorizace 2026 — každý leden (hned jak ČSSZ oznámí)
- Nové parametry kalkulačky — leden
- Důchodová reforma — průběžně při legislativních změnách
- Výnosy penzijních fondů za rok — únor/březen

### Formát článků (Blade komponenty v obsahu)

Články jsou uloženy jako Markdown (`content_markdown`) a renderovány přes `league/commonmark`. Pro bohaté prvky používáme buď Blade komponenty (renderované při buildu) nebo vlastní Markdown extensions / shortcody, které se nahradí Blade komponentami:

```blade
{{-- Číselný callout --}}
<x-content.number-callout value="21 400 Kč" label="Průměrný starobní důchod 2026" trend="+358 Kč" />

{{-- Info box --}}
<x-content.info-box type="tip" title="Věděli jste?">
  Každý rok přesluhování zvyšuje důchod o 0,4 % výpočtového základu.
</x-content.info-box>

{{-- Warning box --}}
<x-content.info-box type="warning" title="Pozor">
  Tato kalkulačka je orientační. Přesný výpočet provede jen ČSSZ.
</x-content.info-box>

{{-- Inline kalkulačka (Vue island) --}}
<x-content.inline-calculator type="retirement-age" />

{{-- Srovnávací tabulka (Vue island) --}}
<x-content.comparison-table :data="$pensionFunds" :columns="['name','return1y','fee']" />
```

---

## Link building strategie

### Přirozené linky
- Citace v médiích — dodat tiskové zprávy o statistikách
- Guest posts na finančních blozích (Peníze.cz, Finmag, Měšec.cz)
- Zmínky při valorizaci — kontaktovat redakce s daty

### Programatické linky
- Embed kódy kalkulačky → weby HR portálů, odborů (iframe → `/embed/kalkulacka`)
- Widget pro penzijní poradce

### PR strategie
- Roční report "Stav důchodů v ČR" — tiskový výstup ke stažení
- Interaktivní data stories (pro sdílení na sociálních sítích)
