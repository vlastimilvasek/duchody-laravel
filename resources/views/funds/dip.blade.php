@extends('layouts.app')

@push('head')
@php
    $schemaFaq = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'Co je Dlouhodobý investiční produkt (DIP)?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'DIP je státem podporovaný režim investování na stáří zavedený v roce 2024. Umožňuje investovat do akcií, ETF nebo fondů a zároveň odečítat vklady od základu daně — až 48 000 Kč ročně.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Kolik na DIP ušetřím na daních?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Při 15% sazbě daně až 7 200 Kč ročně, při 23% sazbě až 11 040 Kč ročně. Limit odpočtu 48 000 Kč je společný pro DIP, penzijní spoření a životní pojištění.',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Kdy mohu peníze z DIP vybrat?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Bez sankce nejdříve v 60 letech věku a zároveň po 10 letech od založení (pravidlo 60+120). Při dřívějším výběru musíte dodanit uplatněné daňové odpočty.',
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">DIP</span>
    </nav>

    {{-- Hlavička --}}
    <div class="max-w-3xl mb-12">
        <x-badge variant="emerald" class="mb-4">Daňová úspora až 11 040 Kč ročně</x-badge>
        <h1 class="h1 mb-4">Dlouhodobý investiční produkt (DIP)</h1>
        <p class="lead">
            Investujte na stáří do akcií a ETF — a odečtěte si až 48 000 Kč ročně
            od základu daně. Srovnání poskytovatelů a kalkulačka úspory.
        </p>
    </div>

    {{-- ═══ Co je DIP ═══ --}}
    <div class="max-w-3xl mb-12">
        <div class="prose-duchody">
            <h2>Co je DIP a jak funguje</h2>
            <p>
                DIP je <strong>režim</strong>, nikoli konkrétní produkt — od roku 2024 jím stát
                podporuje investování na stáří. U poskytovatele (broker, banka, investiční
                společnost) si otevřete účet vedený jako DIP a v něm investujete do akcií,
                ETF, fondů nebo spořicích produktů. Na rozdíl od penzijního spoření
                <strong>sami rozhodujete, do čeho investujete</strong>, a poplatky bývají
                výrazně nižší.
            </p>
            <h3>Podmínky</h3>
            <ul>
                <li><strong>Výběr nejdříve v 60 letech</strong> a zároveň po 10 letech od založení („60 + 120 měsíců").</li>
                <li>Při dřívějším zrušení dodaníte uplatněné odpočty za posledních 10 let.</li>
                <li>Limit odpočtu <strong>48 000 Kč ročně</strong> je společný s penzijním spořením a životním pojištěním.</li>
                <li>Přispívat může i zaměstnavatel — do 50 000 Kč ročně osvobozeno od daně i odvodů.</li>
            </ul>
            <h3>DIP vs. penzijní spoření</h3>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden mt-4">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left">
                        <th scope="col" class="px-5 py-3 font-semibold text-slate-600"></th>
                        <th scope="col" class="px-5 py-3 font-semibold text-slate-600">DIP</th>
                        <th scope="col" class="px-5 py-3 font-semibold text-slate-600">Penzijní spoření (DPS)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr><th scope="row" class="px-5 py-3 text-left font-medium text-slate-500">Státní příspěvek</th><td class="px-5 py-3 text-slate-700">ne</td><td class="px-5 py-3 text-slate-700">až 340 Kč/měs</td></tr>
                    <tr><th scope="row" class="px-5 py-3 text-left font-medium text-slate-500">Daňový odpočet</th><td class="px-5 py-3 text-slate-700">až 48 000 Kč/rok</td><td class="px-5 py-3 text-slate-700">až 48 000 Kč/rok (nad 1 700 Kč/měs)</td></tr>
                    <tr><th scope="row" class="px-5 py-3 text-left font-medium text-slate-500">Výběr investic</th><td class="px-5 py-3 text-emerald-700 font-medium">libovolný (akcie, ETF…)</td><td class="px-5 py-3 text-slate-700">jen účastnické fondy</td></tr>
                    <tr><th scope="row" class="px-5 py-3 text-left font-medium text-slate-500">Poplatky</th><td class="px-5 py-3 text-emerald-700 font-medium">často pod 0,5 %</td><td class="px-5 py-3 text-slate-700">do 1 % + 15 % z výnosu</td></tr>
                    <tr><th scope="row" class="px-5 py-3 text-left font-medium text-slate-500">Výběr peněz</th><td class="px-5 py-3 text-slate-700">60 let + 10 let trvání</td><td class="px-5 py-3 text-slate-700">60 let + 5/10 let spoření</td></tr>
                </tbody>
            </table>
        </div>
        <p class="mt-3 text-sm text-slate-500">
            Ideální kombinace pro většinu lidí: penzijní spoření do 1 700 Kč měsíčně
            (maximální státní příspěvek) a další investice přes DIP.
        </p>
    </div>

    {{-- ═══ Kalkulačka ═══ --}}
    <div id="kalkulacka" class="mb-14 scroll-mt-24">
        <div class="text-center mb-8">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-3">Kalkulačka</p>
            <h2 class="h2 mb-3">Kolik ušetříte na daních?</h2>
        </div>
        <div id="dip-tax-calculator" class="max-w-4xl mx-auto"></div>
    </div>

    {{-- ═══ Poskytovatelé ═══ --}}
    <div class="mb-8">
        <h2 class="h2 mb-2">Kdo DIP nabízí</h2>
        <p class="text-slate-500 mb-8">Přehled hlavních poskytovatelů — brokeři, banky a investiční společnosti.</p>

        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50 text-left">
                        <th scope="col" class="px-5 py-3.5 font-semibold text-slate-600">Poskytovatel</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold text-slate-600">Typ</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold text-slate-600">Investice</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold text-slate-600">Poplatky</th>
                        <th scope="col" class="px-5 py-3.5 font-semibold text-slate-600 text-right">Minimální vklad</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($providers as $provider)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3.5 font-semibold text-slate-900">{{ $provider['name'] }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $provider['type'] }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $provider['products'] }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $provider['fee'] }}</td>
                            <td class="px-5 py-3.5 text-slate-600 text-right">{{ $provider['min_deposit'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Affiliate disclaimer --}}
    <div class="border-l-4 border-slate-300 bg-slate-50 rounded-r-lg p-4 mb-12">
        <p class="text-sm text-slate-600">
            <strong>Upozornění:</strong> Tento web může získat provizi za sjednání produktu
            prostřednictvím výše uvedených odkazů. Tato skutečnost neovlivňuje naše hodnocení
            ani pořadí poskytovatelů.
        </p>
    </div>

    {{-- Cross-link --}}
    <a href="{{ route('fondy.index') }}"
       class="flex items-center justify-between gap-4 bg-slate-900 hover:bg-slate-800 rounded-2xl px-6 py-5 transition-colors group max-w-3xl">
        <div>
            <p class="text-white font-semibold mb-0.5">Preferujete státní příspěvek?</p>
            <p class="text-sm text-slate-400">Porovnejte penzijní fondy s příspěvkem až 340 Kč měsíčně.</p>
        </div>
        <svg class="h-5 w-5 text-emerald-400 shrink-0 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
        </svg>
    </a>
</div>
@endsection
