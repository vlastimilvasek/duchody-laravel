# Důchody.cz — Master Project Brief

> **Mise:** Vybudovat #1 portál o důchodech v České republice — přesnější než ČSSZ.cz, přehlednější než státní weby, krásný jako nejlepší fintech produkty.

---

## Kontext projektu

Portál **Důchody.cz** je obsahově-nástrojový web zaměřený na téma penzí, důchodů a finančního zajištění na stáří v ČR. Kombinuje:

- Výpočetní nástroje (kalkulačky) naprogramované z veřejných dat ČSSZ a MPSV
- Srovnávače finančních produktů (penzijní spoření, DIP, investice)
- Aktuální magazín (valorizace, reforma, rady)
- Interaktivní datové vizualizace (mapy, grafy)
- AI asistenta pro dotazy o důchodech

**Cílová skupina:**
- Primárně: 45–65 let, plánují nebo blíží se důchodu
- Sekundárně: 30–44 let, zajímají se o zajištění na stáří
- Terciárně: finanční poradci, HR profesionálové (B2B)

---

## Tech stack

```
Backend:    Laravel 12 (PHP 8.3) — MVC, Eloquent ORM
Frontend:   Blade šablony + Vue 3 (interaktivní ostrůvky / islands)
Build:      Vite (Laravel Vite plugin)
Styling:    Tailwind CSS + vlastní Blade komponenty
Animace:    @vueuse/motion (Vue) + CSS přechody (statický obsah)
Charts:     Chart.js (vue-chartjs wrapper) + vlastní SVG mapa
Icons:      lucide-vue-next (Vue) + inline SVG sprite (Blade)
Fonty:      Inter Variable (UI) + Instrument Serif (editorially), self-hosted
DB:         SQLite (soubor database/database.sqlite)
Auth:       Laravel Breeze (volitelné přihlášení, session-based)
CMS:        Články v DB (Eloquent) + Markdown přes commonmark/league
Hosting:    VPS / Laravel Forge / Ploi (PHP-FPM + Nginx)
Analytics:  Google Analytics 4 (volitelně Plausible self-hosted)
Search:     Laravel Scout (database driver) nebo Meilisearch
Fronta:     Laravel Queue (database driver pro SQLite)
Cache:      File / database cache + spatie/laravel-responsecache
```

> **Poznámka k Vue integraci:** Používáme **Blade + Vue islands**. Stránky renderuje server přes Blade; Vue 3 montujeme jen tam, kde je potřeba reaktivita (kalkulačky, srovnávací tabulka, mapa). Žádné samostatné SPA ani odděleného API frontendu — jeden Laravel monolit. Vue komponenty se připojují přes `id`/`data-*` atributy v Blade a montují v `resources/js/app.js`.

---

## Design systém

### Barevná paleta (Tailwind tokeny)

```css
/* Primární */
--slate-50:   #f8fafc   /* page background */
--slate-100:  #f1f5f9   /* card surface */
--slate-200:  #e2e8f0   /* border, divider */
--slate-600:  #475569   /* muted text */
--slate-800:  #1e293b   /* dark surface, nav */
--slate-900:  #0f172a   /* headings, primary text */

/* Akcent — Emerald */
--emerald-50:  #ecfdf5  /* emerald surface light */
--emerald-100: #d1fae5  /* tag background */
--emerald-500: #10b981  /* hover */
--emerald-600: #059669  /* primary CTA, links */
--emerald-700: #047857  /* CTA hover */

/* Sémantika */
--amber-400:  #f59e0b   /* warning, highlight, "novinka" */
--red-500:    #ef4444   /* chyba, krácení důchodu */
--blue-500:   #3b82f6   /* info, odkaz */
```

### Typografie

```css
/* Headings — Inter Variable */
font-family: 'Inter Variable', system-ui, sans-serif;
font-feature-settings: 'cv11', 'ss01';  /* lepší číslice a a */

/* Editoriální akcent — Instrument Serif */
font-family: 'Instrument Serif', Georgia, serif;  /* pull quotes, perex */

/* Velikosti (design tokens) */
--text-xs:   12px / 1.5
--text-sm:   14px / 1.5
--text-base: 16px / 1.7
--text-lg:   18px / 1.6
--text-xl:   20px / 1.5
--text-2xl:  24px / 1.3
--text-3xl:  30px / 1.25
--text-4xl:  36px / 1.2  (tracking: -0.02em)
--text-5xl:  48px / 1.1  (tracking: -0.03em)
```

### Prostor a layout

```
max-width: 1280px (container)
content-width: 768px (článek)
gap-unit: 8px base, škálujeme × 1, 2, 3, 4, 6, 8, 12, 16

Padding sekce:
  desktop: py-24 (96px)
  tablet:  py-16 (64px)
  mobile:  py-12 (48px)
```

### Komponenty — princip

- **Blade komponenty** (`resources/views/components/`) pro statické UI prvky — vlastníme je, plná kontrola
- **Vue 3 islands** pro interaktivní prvky (kalkulačky, tabulky, mapa) — reaktivita bez plného SPA
- **@vueuse/motion / CSS přechody** pro počítadla čísel, roll-in animace
- **Žádné gradients na pozadích** — čisté plochy, kontrast přes vrstvení karet
- **Shadows:** pouze `shadow-sm` a `shadow-md` — nic těžkého
- **Rounded:** `rounded-xl` pro karty, `rounded-2xl` pro hero prvky, `rounded-full` pro tagy/badge
- **Přístupnost:** ARIA atributy a klávesnicová navigace ručně doplněné u všech interaktivních komponent

---

## Struktura projektu (Laravel)

```
duchody-cz/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php
│   │   │   ├── Calculator/        # Kalkulačky
│   │   │   ├── Funds/             # Srovnávač fondů
│   │   │   ├── Content/           # Magazín, průvodci
│   │   │   └── Seo/               # Programatické SEO (ročníky, kraje)
│   │   ├── Requests/              # Form Request validace (náhrada za Zod)
│   │   └── Middleware/
│   ├── Models/                    # Eloquent modely
│   │   ├── PensionParameter.php
│   │   ├── WageCoefficient.php
│   │   ├── PensionStatistic.php
│   │   ├── PensionFund.php
│   │   ├── SavedCalculation.php
│   │   └── Article.php
│   ├── Services/
│   │   └── Pension/               # Výpočetní engine (core business logika)
│   │       ├── PensionCalculator.php
│   │       ├── RetirementAge.php
│   │       └── Data/Parameters.php
│   └── View/Components/           # Blade komponenty (PHP třídy)
├── resources/
│   ├── views/
│   │   ├── layouts/               # app.blade.php, nav, footer
│   │   ├── components/            # Blade UI komponenty (.blade.php)
│   │   ├── home.blade.php
│   │   ├── calculator/
│   │   ├── funds/
│   │   └── content/
│   ├── js/
│   │   ├── app.js                 # Mount point Vue islands
│   │   └── components/            # Vue 3 komponenty (.vue)
│   │       ├── calculators/
│   │       ├── charts/
│   │       └── FundsTable.vue
│   └── css/
│       └── app.css                # Tailwind entry + CSS variables
├── routes/
│   └── web.php
├── database/
│   ├── migrations/                # Schema (SQLite-kompatibilní)
│   ├── seeders/                   # Seed dat (parametry, koeficienty)
│   └── database.sqlite            # SQLite soubor (gitignored)
├── tests/
│   ├── Feature/                   # HTTP testy (Pest)
│   └── Unit/Pension/              # Testy výpočetního enginu
├── public/
│   └── fonts/                     # Self-hosted Inter, Instrument Serif
├── composer.json
├── package.json
├── vite.config.js
└── tailwind.config.js
```

---

## Klíčové principy vývoje

### 1. Performance first

```
Cíle Lighthouse:
  Performance:     95+
  Accessibility:   100
  Best Practices:  100
  SEO:             100

Techniky:
- Server-side Blade rendering pro statický obsah (žádný JS pro běžné stránky)
- Vue islands hydratované jen tam, kde je potřeba interaktivita
- spatie/laravel-responsecache pro cache vyrenderovaných stránek
- Lazy loading obrázků (loading="lazy") + případně intervention/image
- Dynamický import těžkých Vue komponent (mapy, grafy) přes Vite code-splitting
- Font subsetting — Inter jen použité znaky (latin + latin-ext)
- Vite asset bundling, minifikace, cache-busting hashe
```

### 2. SEO architektura

```
- Per-view meta tagy přes Blade @section('meta') nebo sdílený SEO helper
- Strukturovaná data (schema.org):
    FAQPage, HowTo, Article, BreadcrumbList, WebApplication
- Sitemap.xml generovaný automaticky (spatie/laravel-sitemap nebo vlastní route)
- robots.txt správně nakonfigurovaný
- Canonical URLs všude
- OpenGraph + Twitter cards
- Programatické stránky (ročníky, kraje) — cachované route s roční revalidací
```

### 3. Přístupnost

```
- WCAG 2.1 AA jako minimum
- Keyboard navigace všude
- Screen reader labels na kalkulačkách (aria-label, aria-live)
- Focus management při interakcích ve Vue komponentách
- Dostatečné kontrasty (nástroj: axe DevTools)
- Skip to content link
- Všechna čísla formátovaná do češtiny (Number formatter, locale cs-CZ)
```

### 4. Kódové standardy

```php
// PHP 8.3, strict_types=1 ve všech souborech
// Laravel Pint pro formátování (PSR-12)
// Larastan / PHPStan level max pro statickou analýzu
// Form Requests pro validaci všech vstupů kalkulaček (náhrada za Zod)
// Typed properties a return types všude
// Žádné magické řetězce — enums pro pohlaví, typy fondů atd.

// Frontend:
// ESLint + Prettier na Vue komponentách
// Vue 3 Composition API (<script setup>)
// TypeScript ve Vue komponentách (volitelné, doporučené pro kalkulačky)
```

---

## Výpočetní engine (app/Services/Pension)

Celý výpočet důchodu musí být v samostatné vrstvě `app/Services/Pension` s plnou test coverage (Pest/PHPUnit). Je to core business logika a běží **na serveru** (PHP) — Vue komponenta jen posílá vstupy a zobrazuje výsledek.

```php
// Klíčové třídy a metody:
RetirementAge::calculate(Carbon $birthDate, Gender $gender, int $children): RetirementAgeResult
PensionCalculator::calculate(PensionParams $params): PensionResult
PensionCalculator::calculateEarly(EarlyPensionParams $params): EarlyPensionResult
WageCoefficients::forYear(int $year): array
Parameters::reductionCoefficients(int $year): array  // 1986–2025

// PensionParams (DTO / readonly třída):
final readonly class PensionParams
{
    public function __construct(
        public Carbon $birthDate,
        public Gender $gender,            // enum Gender: Male, Female
        public int $children,
        public Carbon $retirementDate,
        public array $yearlyIncome,       // [rok => hrubý příjem Kč]
        public array $excludedPeriods,    // Period[] — vyloučené doby
        public array $insurancePeriods,   // InsurancePeriod[] — doby pojištění
    ) {}
}

// PensionResult (DTO):
final readonly class PensionResult
{
    public function __construct(
        public int $basicAmount,            // základní výměra (4900 Kč / 2026)
        public int $percentageAmount,       // procentní výměra
        public int $childBonus,             // výchovné
        public int $totalMonthly,           // celkem
        public int $personalAssessmentBase, // OVZ
        public int $calculationBase,        // výpočtový základ
        public int $insuranceYears,
        public array $breakdown,            // PensionBreakdown[] — pro vizualizaci
    ) {}
}
```

> Endpoint kalkulačky: `POST /kalkulacka/vyse/spocitat` → controller validuje přes Form Request → volá `PensionCalculator` → vrací JSON pro Vue komponentu (nebo redirect s daty pro no-JS fallback).

---

## Databáze (SQLite — Laravel migrace, klíčové tabulky)

> SQLite nepodporuje nativní pole (`text[]`) ani `jsonb` — používáme JSON sloupce (`$table->json(...)`), které Eloquent automaticky (de)serializuje přes casts. UUID nahrazujeme `ulid` (Laravel `HasUlids`) nebo auto-increment ID; `timestamptz` → `timestamp`.

```php
// database/migrations/xxxx_create_pension_parameters_table.php
// Parametry pro kalkulačky (aktualizovatelné adminem)
Schema::create('pension_parameters', function (Blueprint $table) {
    $table->integer('year')->primary();          // rok
    $table->integer('basic_amount');             // základní výměra
    $table->integer('average_wage');             // průměrná mzda (§15 ZDP)
    $table->integer('reduction_boundary_1');     // 1. redukční hranice
    $table->integer('reduction_boundary_2');     // 2. redukční hranice
    $table->integer('reduction_boundary_3');     // 3. redukční hranice
    $table->decimal('wage_growth_coefficient', 8, 4);
    $table->timestamps();
});

// Koeficienty nárůstu vyměřovacího základu
Schema::create('wage_coefficients', function (Blueprint $table) {
    $table->integer('reference_year');           // rok důchodu
    $table->integer('income_year');              // rok příjmu
    $table->decimal('coefficient', 8, 4);
    $table->primary(['reference_year', 'income_year']);
});

// Statistiky důchodů z ČSSZ (pro vizualizace)
Schema::create('pension_statistics', function (Blueprint $table) {
    $table->id();
    $table->date('period');
    $table->string('region_code', 10);
    $table->string('pension_type', 20);          // 'starobni','invalidni','vdovsky','sirotci'
    $table->integer('count');
    $table->integer('average_amount');
    $table->string('source', 50)->nullable();
    $table->index(['region_code', 'pension_type', 'period']);
});

// Penzijní fondy (pro srovnávač)
Schema::create('pension_funds', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('slug')->unique();
    $table->string('name', 200);
    $table->string('company', 200);
    $table->string('fund_type', 50);             // 'konzervativni','vyvazeny','dynamicky','transformovany'
    $table->decimal('fee_management', 4, 2);
    $table->decimal('fee_performance', 4, 2);
    $table->decimal('return_1y', 5, 2)->nullable();
    $table->decimal('return_3y', 5, 2)->nullable();
    $table->decimal('return_5y', 5, 2)->nullable();
    $table->integer('total_assets_mil')->nullable();
    $table->integer('participants_count')->nullable();
    $table->string('affiliate_url')->nullable();
    $table->string('partner_id')->nullable();
    $table->timestamps();
});

// Uložené výsledky kalkulaček (anonymní nebo přihlášený)
Schema::create('saved_calculations', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->string('session_id', 64)->nullable(); // pro anonymní
    $table->string('calc_type', 50);
    $table->json('input_params');
    $table->json('result');
    $table->timestamps();
});

// Články magazínu (CMS v DB)
Schema::create('articles', function (Blueprint $table) {
    $table->ulid('id')->primary();
    $table->string('slug', 200)->unique();
    $table->string('title', 300);
    $table->text('perex');
    $table->longText('content_markdown');         // Markdown, renderovaný league/commonmark
    $table->string('category', 50);
    $table->json('tags')->nullable();             // místo Postgres text[]
    $table->timestamp('published_at')->nullable();
    $table->string('seo_title', 60)->nullable();
    $table->string('seo_description', 160)->nullable();
    $table->timestamps();
});
```

---

## Environment variables

```bash
# .env
APP_NAME="Důchody.cz"
APP_ENV=local
APP_URL=https://duchody.cz
APP_LOCALE=cs

# SQLite
DB_CONNECTION=sqlite
# DB_DATABASE necháváme prázdné → Laravel použije database/database.sqlite

# Fronta a cache (database driver — žádný Redis nutný pro start)
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_DRIVER=database

GA_MEASUREMENT_ID=

# Mail (newsletter, welcome emaily)
MAIL_MAILER=smtp
MAIL_HOST=
MAIL_FROM_ADDRESS="newsletter@duchody.cz"
MAIL_FROM_NAME="Důchody.cz"

# Volitelné
OPENAI_API_KEY=          # pro AI asistenta (fáze 4)
SCOUT_DRIVER=database     # nebo meilisearch
MEILISEARCH_HOST=
STRIPE_KEY=               # premium (fáze 4)
STRIPE_SECRET=
```

---

## Spuštění projektu

```bash
# 1. Clone a instalace závislostí
git clone https://github.com/[org]/duchody-cz
cd duchody-cz
composer install
npm install

# 2. Konfigurace
cp .env.example .env
php artisan key:generate

# 3. SQLite databáze
touch database/database.sqlite
php artisan migrate

# 4. Seed dat — důchodové parametry a koeficienty
php artisan db:seed

# 5. Dev server (Laravel + Vite současně)
composer run dev        # spustí php artisan serve + npm run dev + queue
# nebo zvlášť:
php artisan serve
npm run dev

# 6. Testy výpočetního enginu
php artisan test --filter=Pension
# nebo Pest: ./vendor/bin/pest tests/Unit/Pension
```

---

## Definice "hotovo"

Každá feature je hotová až tehdy, když:

1. ✅ Statická analýza bez chyb (`./vendor/bin/phpstan analyse`, `./vendor/bin/pint --test`)
2. ✅ Lighthouse 95+ Performance
3. ✅ Strukturovaná data validní (Google Rich Results Test)
4. ✅ Mobilní zobrazení OK (375px+)
5. ✅ Klávesnicová navigace funkční
6. ✅ Loading states implementovány (skeleton ve Vue, prázdné stavy v Blade)
7. ✅ Error states ošetřeny (validační chyby, prázdné výsledky)
8. ✅ SEO meta tagy vyplněny
