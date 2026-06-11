@extends('layouts.app')

@push('head')
@php
    $schemaApp = [
        '@context'            => 'https://schema.org',
        '@type'               => 'WebApplication',
        'name'                => 'Kalkulačka výše důchodu',
        'applicationCategory' => 'FinanceApplication',
        'description'         => 'Bezplatná online kalkulačka pro výpočet výše starobního důchodu v ČR',
        'url'                 => route('kalkulacka.vyse'),
        'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'CZK'],
        'inLanguage'          => 'cs',
    ];
    $schemaFaq = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [
            [
                '@type'          => 'Question',
                'name'           => 'Jak se počítá výše starobního důchodu?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'Důchod se skládá ze základní výměry (4 900 Kč v roce 2026), procentní výměry (1,495 % výpočtového základu za každý rok pojištění) a výchovného (500 Kč za vychované dítě).',
                ],
            ],
            [
                '@type'          => 'Question',
                'name'           => 'Co je osobní vyměřovací základ (OVZ)?',
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => 'OVZ je průměrný měsíční příjem za rozhodné období (od roku 1986), přepočtený koeficienty mzdového růstu. Z něj se přes redukční hranice počítá výpočtový základ.',
                ],
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaApp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8 print:hidden" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('kalkulacka.index') }}" class="hover:text-emerald-600 transition-colors">Kalkulačky</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Výše důchodu</span>
    </nav>

    <div class="max-w-3xl">
        <x-badge variant="emerald" class="mb-4">Aktuální parametry 2026</x-badge>
        <h1 class="h1 mb-4">Výpočet výše důchodu</h1>
        <p class="lead mb-10">
            Orientační výpočet starobního důchodu ve čtyřech krocích. Výsledek zahrnuje
            základní výměru, procentní výměru i výchovné — podle parametrů platných pro rok 2026.
        </p>
    </div>

    {{-- Vue island — multi-step kalkulačka --}}
    <div id="pension-calculator" class="max-w-3xl">
        {{-- Fallback bez JavaScriptu --}}
        <noscript>
            <div class="border-l-4 border-amber-400 bg-amber-50 rounded-r-lg p-4">
                <p class="text-sm text-amber-800">
                    Kalkulačka vyžaduje zapnutý JavaScript. Důchod si můžete orientačně spočítat
                    také na <a href="https://www.cssz.cz" class="underline font-semibold">webu ČSSZ</a>.
                </p>
            </div>
        </noscript>
    </div>

    {{-- Disclaimer (povinný — PENSION_ENGINE.md) --}}
    <div class="mt-8 max-w-3xl">
        <div class="border-l-4 border-amber-400 bg-amber-50 rounded-r-lg p-4">
            <p class="text-sm text-amber-800">
                <strong>Upozornění:</strong> Výpočet je orientační a slouží pouze pro informační účely.
                Nezohledňuje všechny zákonné podmínky a individuální okolnosti (vyloučené doby, náhradní
                doby pojištění, zahraniční pojištění, atd.). Přesný výpočet provádí výhradně ČSSZ na
                základě podané žádosti o důchod. Informace jsou aktuální k&nbsp;{{ date('j. n. Y') }}.
            </p>
        </div>
    </div>

    {{-- SEO obsah pod kalkulačkou --}}
    <div class="mt-16 max-w-3xl">
        <h2 class="h2 mb-6">Jak se důchod počítá?</h2>
        <div class="prose-duchody">
            <p>
                Starobní důchod se v České republice skládá ze tří složek. <strong>Základní výměra</strong>
                je pro všechny stejná — v roce 2026 činí 4 900 Kč měsíčně. <strong>Procentní výměra</strong>
                závisí na vašich příjmech a délce pojištění: za každý rok pojištění získáte 1,495 %
                výpočtového základu. <strong>Výchovné</strong> přidává 500 Kč měsíčně za každé vychované dítě.
            </p>
            <p>
                Výpočtový základ vychází z osobního vyměřovacího základu (OVZ) — průměrného měsíčního
                příjmu od roku 1986, přepočteného koeficienty mzdového růstu a zredukovaného přes
                redukční hranice (v roce 2026: do 16 306 Kč se počítá 99 %, do 48 833 Kč dalších 26 %
                a do 130 221 Kč dalších 22 %).
            </p>
        </div>
    </div>
</div>
@endsection
