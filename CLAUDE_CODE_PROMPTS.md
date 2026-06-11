# Claude Code — Quick Start Prompt

> Zkopírujte tento text jako první zprávu do Claude Code po otevření prázdné složky.

---

## Prompt pro zahájení Fáze 0

```
Vytváříme portál Důchody.cz — #1 web o důchodech v ČR.

Přečti si tyto soubory v pořadí a poté inicializuj celý projekt:
1. MASTER_PROMPT.md — obecný kontext, tech stack, principy
2. DESIGN_SYSTEM.md — barvy, typografie, komponenty
3. ROADMAP.md — co stavíme a v jakém pořadí
4. PENSION_ENGINE.md — výpočetní logika (core business)
5. SEO_CONTENT.md — SEO architektura

Začni s Fází 0:

1. Inicializuj Laravel 12 projekt (PHP 8.3) ve stávající složce:
   - composer create-project laravel/laravel .
   - Nastav DB_CONNECTION=sqlite, vytvoř database/database.sqlite
   - QUEUE_CONNECTION=database, CACHE_STORE=database, SESSION_DRIVER=database

2. Nainstaluj a nastav frontend toolchain:
   - Vite + @vitejs/plugin-vue, Vue 3
   - Tailwind CSS + konfigurace dle DESIGN_SYSTEM.md (přesně podle specifikace)
   - @vueuse/motion, chart.js + vue-chartjs, lucide-vue-next
   - Self-hosted fonty (Inter Variable, Instrument Serif) do public/fonts + @font-face

3. Nainstaluj dev nástroje:
   - Laravel Pint, Larastan (phpstan), Pest
   - ESLint + Prettier pro Vue

4. Vytvoř výpočetní vrstvu app/Services/Pension s Pest testy:
   - app/Services/Pension/PensionCalculator.php
   - app/Services/Pension/RetirementAge.php
   - app/Services/Pension/Data/Parameters.php
   - app/Services/Pension/Data/WageCoefficients.php
   - app/Services/Pension/DTO/{PensionParams,PensionResult}.php
   - app/Enums/Gender.php
   - Implementuj RetirementAge::calculate() kompletně
   - Implementuj PensionCalculator::calculate() kompletně
   - Napiš testy dle PENSION_ENGINE.md (tests/Unit/Pension)

5. Vytvoř základní layout:
   - resources/views/layouts/app.blade.php (fonty, meta partial)
   - resources/views/components/layout/header.blade.php — logo + nav
   - resources/views/components/layout/footer.blade.php
   - resources/css/app.css s CSS variables dle DESIGN_SYSTEM.md
   - resources/js/app.js — mount point pro Vue islands

6. Vytvoř databázové schéma:
   - Migrace pro pension_parameters, wage_coefficients, pension_statistics,
     pension_funds, articles, saved_calculations (dle MASTER_PROMPT.md)
   - Eloquent modely s casts (json sloupce, enum casty)
   - Seedery pro pension_parameters 2026 a wage_coefficients 1986–2025
   - php artisan migrate --seed

Piš kód na TOP úrovni — žádné TODO komentáře, žádné placeholdery.
Každý soubor musí být produkčně připravený, declare(strict_types=1), typed.
Typografie, spacing a barvy musí přesně odpovídat DESIGN_SYSTEM.md.
```

---

## Prompt pro Fázi 1 — Homepage

```
Přečti MASTER_PROMPT.md a DESIGN_SYSTEM.md pro kontext.

Postav homepage portálu Důchody.cz (route '/' → HomeController → resources/views/home.blade.php).

Referenční estetika: Linear.app × Stripe × Wise — čistý, moderní, finanční produkt.
Barvy: slate-900 texty, emerald-600 akcent, slate-50 pozadí.

Sekce v pořadí:

1. HERO (Blade)
   - Headline (h1, font-size: clamp(42px, 5vw, 64px), font-weight: 700, tracking: -0.04em):
     "Váš důchod." na prvním řádku
     "Přehledně a přesně." na druhém, poslední slovo v emerald-600
   - Subline (18px, slate-500, max-width 520px):
     "Přesná kalkulačka, srovnání fondů a aktuální přehled — vše o důchodech v ČR na jednom místě."
   - CTA tlačítko: "Spočítat důchod" (emerald-600, velký, s ArrowRight ikonou)
   - Sekundární link: "Srovnat penzijní fondy →" (ghost, emerald-600)
   - Pozadí: slate-50 s jemným grid patternem (CSS linear-gradient, opacity 0.4)
   - Pravá strana: animated stats cards (Vue island, 3 karty s @vueuse/motion) zobrazující:
     * Průměrný důchod: 21 400 Kč (s animated counter)
     * Valorizace 2026: +358 Kč
     * Počet důchodců: 3,1 mil.

2. QUICK CALCULATOR (inline, Vue island)
   - Nadpis sekce: "Kdy půjdu do důchodu?"
   - 3 inputy: datum narození, pohlaví (select), počet vychovaných dětí
   - Real-time výsledek (bez submitu) — datum odchodu + počet let/měsíců/dní zbývá
   - Link "Podrobný výpočet výše důchodu →"

3. NÁSTROJE (4 karty v gridu, Blade)
   - Kalkulačka výše důchodu
   - Srovnávač penzijních fondů
   - Interaktivní mapa důchodů
   - Průvodce reformou 2025

4. STATISTIKY (Vue island, animovaná čísla při scroll)
   - "3 100 000 důchodců v ČR"
   - "21 400 Kč průměrný starobní důchod"
   - "4 900 Kč základní výměra 2026"
   - @vueuse/motion visible-once (jen poprvé)

5. CTA SEKCE (Blade form)
   - "Přihlaste se k newsletteru"
   - Email input + tlačítko → POST na NewsletterController

Požadavky:
- Server-side Blade rendering; Vue islands jen pro hero stats, quick calculator, statistiky
- Meta tagy přes layout partial (title, description, canonical, OG)
- Strukturovaná data (WebSite + Organization schema, JSON-LD v Blade)
- Lighthouse 95+ Performance
- Plně responzivní od 375px
- Respektovat prefers-reduced-motion ve Vue animacích
```

---

## Prompt pro Fázi 1 — Kalkulačka výše důchodu

```
Postav kompletní kalkulačku výše důchodu na /kalkulacka/vyse.

Architektura:
- Blade stránka renderuje shell + disclaimer + strukturovaná data
- Vue komponenta (resources/js/components/calculators/PensionCalculator.vue) řeší multi-step UX
- Výpočet běží na SERVERU: POST /kalkulacka/vyse/spocitat
  → Form Request validace → app/Services/Pension/PensionCalculator → JSON odpověď

MULTI-STEP formulář (4 kroky) s progress barem (Vue):

KROK 1 — Základní údaje
- Datum narození (HTML date input)
- Pohlaví (radio — Muž / Žena)
- Počet vychovaných dětí (0–6+, slider nebo číslo)
- Plánovaný datum odchodu (defaultně = důchodový věk, editovatelný)
→ Live preview: "Váš důchodový věk: 65 let a 4 měsíce (12. 6. 2030)"
  (počítá lightweight endpoint nebo klientská kopie RetirementAge logiky)

KROK 2 — Příjmy
- Toggle: "Zadat průměrný plat" vs "Zadat rok po roku"
- Průměrný plat: jeden slider (10 000–100 000 Kč)
- Rok po roku: tabulka 1986–2025, import z textového pole
- Hint: "Příjmy najdete v Informativním výpisu konta ČSSZ"

KROK 3 — Doba pojištění
- Celková doba pojištění (roky, slider 0–50)
- Zahrnutí náhradních dob (checkboxy): mateřská/rodičovská, evidence ÚP, studium před 2010
- Pokud přesluhuje: přidat roky po důchodovém věku

KROK 4 — VÝSLEDEK
- Velký animated number: "Váš odhadovaný důchod"
- Breakdown karta:
  * Základní výměra: 4 900 Kč
  * Procentní výměra: X Kč (Y let × Z %)
  * Výchovné: X Kč (N dětí)
  * CELKEM: X Kč/měsíc
- Chart.js PieChart (vue-chartjs) — vizuální podíl složek
- Srovnání s průměrem: bar kde jsi
- Akce: "Uložit výsledek" | "Sdílet" | "Stáhnout PDF"
- CTA: "Jak zvýšit svůj důchod? →" (vede na penzijní fondy)

Validace: Form Request (app/Http/Requests) pro endpoint výpočtu.
URL state: parametry v URL query (sdílitelné).
Uložení: POST na SavedCalculation (model, saved_calculations tabulka).
Disclaimer box pod výsledkem (text z PENSION_ENGINE.md).
```

---

## Prompt pro srovnávač fondů

```
Postav srovnávač penzijních fondů na /fondy.

Data ze SQLite tabulky pension_funds (Eloquent model PensionFund),
předaná do Vue komponenty jako props/JSON z controlleru.

LAYOUT:
- Blade stránka: hlavička, filtry shell, affiliate disclaimer, strukturovaná data
- Vue komponenta FundsTable.vue: interaktivní tabulka (řazení, filtry, výběr)

TABULKA SLOUPCE:
1. Název fondu + společnost (sortable)
2. Typ fondu (badge s barvou: konzervativní=blue, vyvážený=amber, dynamický=emerald)
3. Výnos 1 rok (%, sortable, zelená/červená dle hodnoty)
4. Výnos 3 roky (%, sortable)
5. Výnos 5 let (%, sortable)
6. Roční poplatek (%, sortable)
7. Majetek (mld. Kč)
8. Akce: "Detail" | "Srovnat"

FUNKCE (Vue):
- Řazení kliknutím na záhlaví
- Multi-select srovnání (max 3 fondy) → side-by-side modal
- Sticky header při scrollu
- Mobile: horizontal scroll s fixed první sloupec
- Empty state při nulovém výsledku filtru

DETAIL FONDU /fondy/{slug}:
- Breadcrumb (s strukturovanými daty)
- Hero: název, typ, klíčové metriky (3 karty, Blade)
- Chart.js LineChart (vue-chartjs) — výnosová křivka vs inflace (poslední 5 let)
- Tabulka poplatků (detailní)
- Hodnocení: průhledná metodika
- CTA affiliate: <x-affiliate.fund-cta :fund="$fund"> (s disclaimer o affiliate)
- Strukturovaná data: FinancialProduct (JSON-LD v Blade)

Affiliate disclaimer (zákonná povinnost, musí být viditelný):
"Upozornění: Tento web může získat provizi za sjednání produktu prostřednictvím výše uvedených odkazů.
Tato skutečnost neovlivňuje naše hodnocení ani pořadí fondů."
```
