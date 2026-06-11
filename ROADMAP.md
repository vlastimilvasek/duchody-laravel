# Důchody.cz — Roadmapa

> Živý dokument. Aktualizovat při každém dokončení fáze.

---

## Přehled fází

| Fáze | Obsah | Délka | Status |
|------|-------|-------|--------|
| 0 | Setup & infrastruktura | 1 týden | ✅ (chybí CI, deploy, dark mode toggle) |
| 1 | MVP — Kalkulačky + SEO základ | 5 týdnů | ✅ (chybí dynamické OG obrázky, GSC) |
| 2 | Srovnávače + Magazín | 6 týdnů | ✅ (chybí admin fondů, AdSense/GA4, welcome sekvence 2.+3. e-mail, obsah 13/20 článků) |
| 3 | Data, vizualizace, komunita | 6 týdnů | 🔶 statistiky dashboard hotový; chybí mapa, import ČSSZ, účty, FAQ |
| 4 | AI, premium, B2B | průběžně | 🔲 (PDF export základ hotov ve Fázi 1) |

---

## Fáze 0 — Setup & infrastruktura (týden 1)

### Cíl
Funkční dev prostředí, CI/CD pipeline, design systém připravený.

### Úkoly

#### Projekt
- [ ] Inicializace Laravel 12 projektu (`composer create-project laravel/laravel`)
- [ ] PHP 8.3, `declare(strict_types=1)` standard
- [ ] Laravel Pint + Larastan (PHPStan) konfigurace
- [ ] Vite + Vue 3 plugin + Tailwind CSS setup
- [ ] ESLint + Prettier na Vue komponentách
- [ ] GitHub Actions CI (Pint, PHPStan, Pest)
- [ ] Deploy target: Laravel Forge / Ploi (VPS, PHP-FPM + Nginx), staging environment

#### Design systém
- [ ] Tailwind CSS konfigurace s custom tokeny (`tailwind.config.js`)
- [ ] Blade UI komponenty: Button, Card, Input, Select, Badge, Dialog, Tabs, Skeleton
- [ ] Inter Variable + Instrument Serif — self-hosted v `public/fonts/` (@font-face)
- [ ] Globální CSS variables (barvy, spacing, shadows) v `resources/css/app.css`
- [ ] @vueuse/motion setup (MotionPlugin v app.js)
- [ ] Dark mode toggle (CSS variables approach, ne class-based)

#### Databáze (SQLite)
- [ ] `touch database/database.sqlite`, `DB_CONNECTION=sqlite`
- [ ] Migrace: pension_parameters, wage_coefficients, pension_statistics, pension_funds, articles, saved_calculations
- [ ] Eloquent modely + casts (json sloupce, enum casty)
- [ ] Seedery — parametry 2026, koeficienty 1986–2025
- [ ] Form Requests pro validaci vstupů

#### Pension engine (app/Services/Pension)
- [ ] `app/Services/Pension` setup + Pest testy
- [ ] DTO třídy: `PensionParams`, `PensionResult`, `InsurancePeriod` (+ enum `Gender`)
- [ ] `RetirementAge::calculate()` — plná tabulka MPSV (ročníky 1936–1988+)
- [ ] `WageCoefficients` — načtení z DB / statický PHP pole
- [ ] `PensionCalculator::ovz()` — osobní vyměřovací základ
- [ ] `PensionCalculator::calculationBase()` — redukční hranice
- [ ] `PensionCalculator::calculate()` — celkový výpočet
- [ ] `PensionCalculator::calculateEarly()` — předčasný se srážkami
- [ ] Test coverage 100 % na výpočtech (Pest)

#### Layout komponenty
- [ ] `<x-layout.header>` — logo, navigace, mobilní menu
- [ ] `<x-layout.footer>` — linkový strom, copyright, disclaimer
- [ ] `<x-layout.container>` — max-width wrapper
- [ ] `<x-breadcrumb>` — s strukturovanými daty

---

## Fáze 1 — MVP (týdny 2–6)

### Cíl
Živý web s funkčními kalkulačkami a základním SEO. Spuštění na důchody.cz.

### Stránky k implementaci

#### Homepage `/`
- [ ] Hero sekce — headline + CTA + animated stats counter (Vue island)
- [ ] Rychlá kalkulačka (zjednodušená, inline Vue — datum + pohlaví → věk odchodu)
- [ ] Feature cards (4 nástroje) — Blade
- [ ] Statistická sekce — průměrný důchod, valorizace, počet důchodců (Vue counter, visible-once)
- [ ] Sekce "Jak to funguje" — 3 kroky
- [ ] CTA banner — newsletter signup (Blade form → NewsletterController)
- [ ] Strukturovaná data: WebSite, Organization

#### Kalkulačka důchodového věku `/kalkulacka/vek`
- [ ] Vue komponenta: datum narození, pohlaví, počet vychovaných dětí
- [ ] Real-time výsledek (bez submitu — počítá na klientu nebo lightweight endpoint)
- [ ] Výstup: datum odchodu, přesný věk, počet dní zbývá, předčasný důchod (datum)
- [ ] Vizuální timeline (Vue + CSS přechody)
- [ ] Sdíletelný výsledek (URL query params)
- [ ] Strukturovaná data: WebApplication, FAQPage

#### Kalkulačka výše důchodu `/kalkulacka/vyse`
- [ ] Multi-step Vue formulář (průvodce):
  - Krok 1: Základní údaje (datum narození, pohlaví, plánovaný odchod)
  - Krok 2: Příjmy (průměrný plat nebo rok po roku od 1986)
  - Krok 3: Doby pojištění (zaměstnání, OSVČ, náhradní doby)
  - Krok 4: Výsledek
- [ ] Progress bar mezi kroky
- [ ] Výpočet na serveru: `POST /kalkulacka/vyse/spocitat` → Form Request → `PensionCalculator` → JSON
- [ ] Výsledek: základní výměra, procentní výměra, výchovné, celkem
- [ ] Breakdown karty — vizualizace složek důchodu (Chart.js PieChart ve Vue)
- [ ] Srovnání s průměrem (kde se uživatel pohybuje)
- [ ] Export do PDF (barryvdh/laravel-dompdf nebo spatie/laravel-pdf)
- [ ] Disclaimer o orientačním charakteru výpočtu

#### Programatické SEO stránky
- [ ] `/duchod/rocnik/{rok}` — stránka pro každý ročník 1940–1980
  - Route s `whereNumber`, controller validuje rozsah
  - Typický věk odchodu pro daný ročník
  - Průměrný počet let pojištění generace
  - FAQ specifické pro ročník
  - Cache::remember s roční expirací (revalidace)
- [ ] `/statistiky/{kraj}` — průměrné důchody dle krajů (14 stránek)
  - Data z ČSSZ xlsx (importovaná do SQLite seederem/commandem)
  - Srovnání s celostátním průměrem

#### Průvodce (statické stránky)
- [ ] `/pruvodce/starobni-duchod` — komplexní průvodce (3000+ slov)
- [ ] `/pruvodce/predcasny-duchod` — podmínky, výpočet krácení
- [ ] `/pruvodce/penzijni-sporeni` — DPS vs transformované fondy
- [ ] `/pruvodce/duchodova-reforma-2025` — aktuální stav reformy

#### SEO infrastruktura
- [ ] `sitemap.xml` — generovaný scheduler-em (spatie/laravel-sitemap)
- [ ] `robots.txt` (public/robots.txt)
- [ ] `manifest.json` (PWA základy)
- [ ] OpenGraph obrázky — dynamické (route `/og` + spatie/browsershot, cache na disk)
- [ ] Google Search Console verifikace

### Výkon cíle fáze 1
- Lighthouse Performance: 95+ na homepage
- FCP < 1.2s
- LCP < 2.0s
- CLS < 0.05

---

## Fáze 2 — Srovnávače + Magazín (týdny 7–12)

### Cíl
Monetizační nástroje spuštěny, affiliate partneři napojeni, obsah proudí.

### Srovnávač penzijních fondů `/fondy`

- [ ] Tabulka všech fondů (Vue komponenta `FundsTable.vue` — řazení, filtry)
  - Data ze SQLite (`PensionFund` model) předaná do Vue přes props/JSON
  - Filtry: typ fondu, penzijní společnost
  - Řazení: výnos 1R, 3R, 5R, poplatek
  - Sticky header, mobilní scroll
- [ ] Detail fondu `/fondy/{slug}`
  - Výnosový graf (Chart.js LineChart ve Vue, časová osa)
  - Porovnání s inflací a průměrem trhu
  - Call-to-action → partner (affiliate link, Blade `<x-affiliate.fund-cta>`)
  - Strukturovaná data: FinancialProduct
- [ ] Komparační nástroj — vyber 2–3 fondy, porovnej side-by-side (Vue)
- [ ] Kalkulačka "kolik naspoříte" — měsíční vklad + fond + léta → výsledek (Vue)
- [ ] Badge "Nejlepší výnos 2025", "Nejnižší poplatek"
- [ ] Admin pro update dat fondů (Laravel Nova / Filament nebo jednoduchý CRUD controller)

### Srovnávač DIP produktů `/dip`

- [ ] Přehled Dlouhodobého investičního produktu — co to je
- [ ] Tabulka poskytovatelů DIP (brokeři, banky)
- [ ] Kalkulačka daňové úspory přes DIP (Vue)
- [ ] Affiliate linky na partnery

### Magazín `/magazin`

- [ ] Kategorie: Aktuality, Reforma, Investice, Rady, Data
- [ ] Listing stránka — cards, filtry dle kategorie, search (Laravel Scout)
- [ ] Detail článku — Markdown z DB renderovaný přes league/commonmark
  - Inline kalkulačka (Vue island přes shortcode → Blade komponenta)
  - Info box, warning box, number callout (Blade komponenty)
  - Sdílení (Web Share API, JS)
  - Čas čtení
  - Related articles
- [ ] Autor box (credibility)
- [ ] Strukturovaná data: Article, BreadcrumbList

### Obsah pro launch
- [ ] 20 zakládajících článků (mix průvodců a aktualit)
- [ ] Aktualizace valorizace každý leden (šablona + reminder)

### Newsletter
- [ ] Signup formulář (homepage + inline v článcích)
- [ ] Laravel Mail + database queue integrace
- [ ] Double opt-in flow (signed URL)
- [ ] Welcome email sekvence (3 emaily, Mailable + scheduled)
- [ ] Měsíční newsletter šablona (Markdown mail)

### Monetizace — napojení
- [ ] Google AdSense integrace (Vue island, lazy loaded, CLS-safe)
- [ ] Affiliate link tracking systém (UTM helper + event v GA4)
- [ ] Affiliate disclaimery (zákonná povinnost)

---

## Fáze 3 — Data, vizualizace, komunita (týdny 13–18)

### Interaktivní mapa důchodů `/mapa`

- [ ] Česká mapa SVG (vlastní Vue komponenta s inline SVG path-y okresů)
- [ ] Choropleth — průměrný důchod dle okresu (77 okresů)
- [ ] Hover tooltip — název okresu, průměr, rozdíl od průměru ČR
- [ ] Toggle: starobní / invalidní / vdovský důchod
- [ ] Mobilní verze (tap místo hover)
- [ ] Exportovatelný obrázek

### Statistiky dashboard `/statistiky`

- [ ] Přehledová stránka — klíčové ukazatele (metric cards, Blade)
- [ ] Vývoj průměrného důchodu 2010–2026 (Chart.js LineChart)
- [ ] Počet důchodců dle roku (AreaChart)
- [ ] Rozložení výše důchodů (Histogram)
- [ ] Porovnání ČR vs EU průměr (BarChart)
- [ ] Data aktualizace — automatický import z ČSSZ xlsx (Artisan command + GitHub Action / scheduler)

### Uživatelské účty (volitelné přihlášení)

- [ ] Laravel Breeze — email/password (volitelně Socialite pro Google OAuth)
- [ ] Uložené výpočty (`saved_calculations` tabulka, model)
- [ ] Osobní dashboard — "Moje důchodové plánování"
- [ ] Notifikace na valorizaci (Laravel Notifications — email)

### Komunita & FAQ

- [ ] FAQ sekce se strukturovanými daty (20+ otázek)
- [ ] Komentáře u článků (vlastní Eloquent model nebo Giscus)
- [ ] Sekce "Ptejte se" — formulář → tým odpovídá → FAQ

---

## Fáze 4 — AI, Premium, B2B (průběžně od měsíce 7)

### AI důchodový asistent `/asistent`

- [ ] Chat interface (Vue komponenta, podobný ChatGPT UI)
- [ ] System prompt obsahující celou znalostní bázi o českých důchodech
- [ ] Backend proxy přes Laravel (`/api/asistent` controller, žádné API klíče na klientu)
- [ ] Schopnosti: výpočty, vysvětlování pojmů, srovnávání možností
- [ ] Rate limiting (Laravel `throttle` middleware) pro neregistrované
- [ ] OpenAI GPT-4o nebo Claude API
- [ ] Kontextové použití na kalkulačce ("Vysvětlit výsledek")

### Premium funkce

- [ ] Podrobný PDF export kalkulačky (barryvdh/laravel-dompdf nebo spatie/laravel-pdf)
  - Personalizovaná zpráva s doporučeními
  - Cena: 99–199 Kč (Stripe Checkout), nebo zdarma s registrací
- [ ] Pokročilá kalkulačka pro OSVČ (paušální daň, různé roky)
- [ ] Porovnání scénářů (odejít nyní vs za 2 roky vs za 5 let)

### B2B produkty

- [ ] Embeddovatelný widget (kalkulačka pro HR portály)
  - Iframe verze (`/embed/kalkulacka`) s custom barvami
  - API endpoint pro výsledky (Laravel Sanctum token auth)
  - SaaS pricing: 990 Kč/měsíc
- [ ] White-label řešení pro pojišťovny / banky
- [ ] API pro výpočet důchodového věku

### Expanze

- [ ] Slovenská lokalizace `/sk` — slovenský důchodový systém (Laravel localization)
- [ ] English summary pro expaty v ČR

---

## Technický dluh a údržba

### Pravidelné úkoly

| Frekvence | Úkol |
|-----------|------|
| Leden každý rok | Update pension_parameters (základní výměra, redukční hranice, koeficient) |
| Leden každý rok | Update wage_coefficients pro nový rok |
| Čtvrtletně | Update pension_statistics z ČSSZ xlsx (Artisan import command) |
| Čtvrtletně | Update pension_funds data z APS |
| Průběžně | Aktualizace obsahu po legislativních změnách |

### Monitoring

- [ ] Core Web Vitals v produkci (GA4 / Search Console)
- [ ] Google Search Console — pozice, kliknutí, indexace
- [ ] Uptime monitoring (Better Uptime / UptimeRobot)
- [ ] Error tracking (Sentry — laravel/sentry)
- [ ] Laravel Pulse / Telescope pro výkon a fronty
- [ ] Kalkulačkové testy při update parametrů (Pest)

---

## KPIs — klíčové metriky úspěchu

### 3 měsíce po launchi
- Organický traffic: 10 000 návštěv/měsíc
- Indexovaných URL: 200+
- Newsletter odběratelů: 500+

### 6 měsíců po launchi
- Organický traffic: 50 000 návštěv/měsíc
- Top 3 pozice pro "kdy půjdu do důchodu", "výpočet důchodu"
- Měsíční příjem (affiliate + ads): 50 000+ Kč

### 12 měsíců po launchi
- Organický traffic: 150 000 návštěv/měsíc
- Newsletter odběratelů: 10 000+
- Měsíční příjem: 200 000+ Kč
