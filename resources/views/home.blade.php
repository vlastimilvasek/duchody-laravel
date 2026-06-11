@extends('layouts.app')

@push('head')
@php
    $schemaWebsite = [
        '@context'  => 'https://schema.org',
        '@type'     => 'WebSite',
        'name'      => 'Důchody.cz',
        'url'       => config('app.url'),
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => config('app.url') . '/hledej?q={search_term_string}',
            'query-input' => 'required name=search_term_string',
        ],
    ];
    $schemaOrg = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'Důchody.cz',
        'url'      => config('app.url'),
        'logo'     => asset('images/logo.png'),
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaWebsite, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaOrg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')

{{-- ════════════════════════════════════════════════════════
     1. HERO
     ════════════════════════════════════════════════════════ --}}
<section class="relative bg-slate-50 overflow-hidden">

    {{-- Jemná mřížka --}}
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:48px_48px] opacity-50 pointer-events-none" aria-hidden="true"></div>
    {{-- Emerald glow --}}
    <div class="absolute -top-32 -right-32 w-[640px] h-[640px] bg-emerald-100/60 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            {{-- ─── Levý sloupec: text + CTA ─── --}}
            <div>
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-8">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0" aria-hidden="true"></span>
                    Aktuální pro rok 2026
                </div>

                {{-- Headline --}}
                <h1 class="hero-headline mb-6">
                    Váš důchod.<br>
                    Přehledně a&nbsp;<span class="text-emerald-600">přesně.</span>
                </h1>

                <p class="lead mb-10 max-w-lg">
                    Přesná kalkulačka, srovnání fondů a aktuální přehled — vše o důchodech v&nbsp;ČR na jednom místě.
                </p>

                {{-- CTA tlačítka --}}
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('kalkulacka.vyse') }}"
                       class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-base px-7 py-3.5 rounded-xl shadow-sm hover:shadow-md transition-all duration-150">
                        Spočítat výši důchodu
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                    <a href="{{ route('fondy.index') }}"
                       class="inline-flex items-center justify-center gap-1.5 border border-slate-300 text-slate-700 hover:border-emerald-300 hover:text-emerald-700 font-medium text-base px-7 py-3.5 rounded-xl bg-white/80 transition-all duration-150">
                        Srovnat penzijní fondy
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                {{-- Trust badges --}}
                <div class="mt-10 flex flex-wrap gap-5 items-center">
                    <span class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Zdarma, bez registrace
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Parametry 2026 aktualizovány
                    </span>
                    <span class="flex items-center gap-1.5 text-xs text-slate-500 font-medium">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19V7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2z"/>
                        </svg>
                        Zákon 155/1995 Sb.
                    </span>
                </div>
            </div>

            {{-- ─── Pravý sloupec: Vue island HeroStats ─── --}}
            <div id="hero-stats" class="lg:pl-4" aria-label="Klíčové statistiky důchodového systému"></div>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════
     2. QUICK CALCULATOR
     ════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-10">
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-3">
                    Rychlá kalkulačka
                </p>
                <h2 class="h2 mb-4">Kdy půjdu do důchodu?</h2>
                <p class="lead">Zadejte datum narození a zjistěte přesný termín — okamžitě, bez registrace.</p>
            </div>

            {{-- Vue island --}}
            <div id="quick-calculator" aria-label="Kalkulačka důchodového věku"></div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════
     3. NÁSTROJE
     ════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-slate-50 border-y border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-14">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-3">
                Naše nástroje
            </p>
            <h2 class="h2 mb-4">Vše pro vaše rozhodnutí</h2>
            <p class="lead max-w-xl mx-auto">
                Kalkulačky, srovnávače a statistiky — přehledně a zdarma pro každého.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- Karta 1: Kalkulačka věku --}}
            <a href="{{ route('kalkulacka.vek') }}"
               class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-xl flex items-center justify-center mb-5 transition-colors duration-200">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2 group-hover:text-emerald-700 transition-colors">
                    Kdy půjdu do důchodu?
                </h3>
                <p class="text-sm text-slate-500 mb-4 leading-relaxed">
                    Přesné datum odchodu dle MPSV tabulek. Stačí datum narození, pohlaví a počet dětí.
                </p>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Zjistit datum
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>

            {{-- Karta 2: Výpočet výše důchodu --}}
            <a href="{{ route('kalkulacka.vyse') }}"
               class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-xl flex items-center justify-center mb-5 transition-colors duration-200">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19V7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2 group-hover:text-emerald-700 transition-colors">
                    Kolik dostanu důchodu?
                </h3>
                <p class="text-sm text-slate-500 mb-4 leading-relaxed">
                    Výpočet základní i procentní výměry dle aktuálních parametrů 2026 a vašich příjmů.
                </p>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Spočítat výši
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>

            {{-- Karta 3: Srovnání fondů --}}
            <a href="{{ route('fondy.index') }}"
               class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-xl flex items-center justify-center mb-5 transition-colors duration-200">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2 group-hover:text-emerald-700 transition-colors">
                    Srovnání penzijních fondů
                </h3>
                <p class="text-sm text-slate-500 mb-4 leading-relaxed">
                    Výnosy, poplatky a hodnocení všech fondů v ČR přehledně na jednom místě.
                </p>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Porovnat fondy
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>

            {{-- Karta 4: Statistiky --}}
            <a href="{{ route('statistiky.index') }}"
               class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200">
                <div class="w-10 h-10 bg-emerald-50 group-hover:bg-emerald-100 rounded-xl flex items-center justify-center mb-5 transition-colors duration-200">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-slate-900 mb-2 group-hover:text-emerald-700 transition-colors">
                    Statistiky a grafy
                </h3>
                <p class="text-sm text-slate-500 mb-4 leading-relaxed">
                    Vývoj průměrného důchodu, věk odchodu a demografické trendy v interaktivních grafech.
                </p>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Prozkoumat data
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>

        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════
     3b. JAK TO FUNGUJE — 3 kroky
     ════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-3">Jak to funguje</p>
            <h2 class="h2 mb-4">Tři kroky k jasnu o vašem důchodu</h2>
            <p class="lead max-w-xl mx-auto">Žádná registrace, žádné čekání. Výsledek máte do dvou minut.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            {{-- Krok 1 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto bg-emerald-600 text-white rounded-2xl flex items-center justify-center text-lg font-bold mb-5">1</div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Zadejte základní údaje</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Datum narození, pohlaví a příjmy. U výpočtu výše stačí průměrný plat —
                    přesnost zvýšíte příjmy rok po roku z výpisu ČSSZ.
                </p>
            </div>

            {{-- Krok 2 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto bg-emerald-600 text-white rounded-2xl flex items-center justify-center text-lg font-bold mb-5">2</div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Získejte výsledek</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Datum odchodu i odhad výše důchodu podle aktuálních parametrů 2026 —
                    včetně rozpadu na základní výměru, procentní výměru a výchovné.
                </p>
            </div>

            {{-- Krok 3 --}}
            <div class="relative text-center">
                <div class="w-12 h-12 mx-auto bg-emerald-600 text-white rounded-2xl flex items-center justify-center text-lg font-bold mb-5">3</div>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">Naplánujte si víc</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Stáhněte si PDF, sdílejte výsledek a porovnejte penzijní fondy —
                    i pár stovek měsíčně navíc udělá v důchodu velký rozdíl.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════
     4. STATISTIKY — tmavá sekce s Vue island
     ════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-28 bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Vue island StatsCounter --}}
        <div id="stats-counter" aria-label="Statistiky důchodového systému v ČR"></div>
    </div>
</section>

{{-- ════════════════════════════════════════════════════════
     5. CTA — newsletter
     ════════════════════════════════════════════════════════ --}}
<section class="py-20 sm:py-24 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-xl mx-auto text-center">

            <div class="inline-flex items-center gap-2 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold px-4 py-1.5 rounded-full mb-8">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Newsletter
            </div>

            <h2 class="h2 mb-4">Zůstaňte v obraze</h2>
            <p class="lead mb-10">
                Valorizace, změny zákonů a nové kalkulačky — vždy jako první. Bez spamu.
            </p>

            {{-- Flash zprávy --}}
            @if (session('newsletter_success'))
                <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl px-5 py-4 text-left">
                    <svg class="h-5 w-5 text-emerald-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('newsletter_success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-5 py-4 text-left">
                    {{ $errors->first('email') }}
                </div>
            @endif

            {{-- Formulář --}}
            <form method="POST" action="{{ route('newsletter.store') }}" novalidate>
                @csrf
                <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <label for="newsletter-email" class="sr-only">Váš e-mail</label>
                    <input
                        id="newsletter-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="váš@email.cz"
                        class="flex-1 min-w-0 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 text-sm placeholder:text-slate-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                    />
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm hover:shadow-md transition-all duration-150 whitespace-nowrap"
                    >
                        Odebírat
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </button>
                </div>
            </form>

            <p class="mt-4 text-xs text-slate-400">
                Žádný spam. Odhlášení jedním klikem kdykoli.
            </p>

            {{-- Disclaimer --}}
            <div class="mt-12 pt-8 border-t border-slate-200">
                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                    Výpočty jsou orientační a slouží pouze pro informační účely. Přesný výpočet provádí výhradně ČSSZ.
                    Informace jsou aktuální k&nbsp;{{ date('j. n. Y') }}.
                </p>
            </div>

        </div>
    </div>
</section>

@endsection
