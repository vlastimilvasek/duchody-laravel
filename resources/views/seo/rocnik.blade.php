@extends('layouts.app')

@push('head')
@php
    $schemaFaq = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => array_map(fn (array $item) => [
            '@type'          => 'Question',
            'name'           => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ], $faq),
    ];
    $schemaBreadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domů', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => "Důchod ročník {$rok}"],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaFaq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Důchod ročník {{ $rok }}</span>
    </nav>

    <div class="max-w-3xl mb-10">
        <x-badge variant="emerald" class="mb-4">Dle zákona 155/1995 Sb.</x-badge>
        <h1 class="h1 mb-4">Důchod pro ročník {{ $rok }}</h1>
        <p class="lead">
            Muž narozený v roce {{ $rok }} dosáhne důchodového věku v <strong>{{ $maleLabel }}</strong>.
            U žen tohoto ročníku {{ $rok <= 1965 ? 'záleží na počtu vychovaných dětí' : 'platí stejný věk jako u mužů' }} —
            kompletní přehled najdete v tabulce níže.
        </p>
    </div>

    {{-- ═══ Tabulka důchodového věku ═══ --}}
    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden max-w-3xl mb-12">
        <table class="w-full text-sm">
            <caption class="sr-only">Důchodový věk pro ročník {{ $rok }} podle pohlaví a počtu dětí</caption>
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left">
                    <th scope="col" class="px-6 py-3.5 font-semibold text-slate-600">Kdo</th>
                    <th scope="col" class="px-6 py-3.5 font-semibold text-slate-600">Důchodový věk</th>
                    <th scope="col" class="px-6 py-3.5 font-semibold text-slate-600 text-right">Rok odchodu (cca)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($table as $row)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-slate-900">{{ $row['label'] }}</td>
                        <td class="px-6 py-3.5 tabular-nums text-slate-700">
                            {{ $row['age']['years'] }} let{{ $row['age']['months'] > 0 ? ' ' . $row['age']['months'] . ' měs.' : '' }}
                        </td>
                        <td class="px-6 py-3.5 tabular-nums text-slate-700 text-right">{{ $row['retirementYear'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- ═══ Generace v číslech ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-3xl mb-12">
        <x-data-card
            label="Důchodový věk (muž)"
            value="{{ $male['years'] }} let{{ $male['months'] > 0 ? ' ' . $male['months'] . ' m.' : '' }}"
        />
        <x-data-card
            label="Průměrná doba pojištění generace"
            value="{{ $insuranceYears }} let"
            trend="orientačně dle ČSSZ"
        />
        <x-data-card
            label="Minimální doba pojištění"
            value="35 let"
            trend="podmínka nároku na důchod"
        />
    </div>

    {{-- ═══ CTA kalkulačka ═══ --}}
    <div class="max-w-3xl bg-slate-900 rounded-2xl p-6 sm:p-8 mb-14">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex-1">
                <p class="text-white text-lg font-bold mb-1">Přesné datum pro vás</p>
                <p class="text-sm text-slate-400">
                    Tabulka platí pro narozené 1.&nbsp;7.&nbsp;{{ $rok }}. Zadejte přesné datum narození
                    a zjistěte den, kdy můžete odejít do důchodu.
                </p>
            </div>
            <a href="{{ route('kalkulacka.vek') }}"
               class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-6 py-3 rounded-xl transition-colors whitespace-nowrap shrink-0">
                Spočítat přesně →
            </a>
        </div>
    </div>

    {{-- ═══ FAQ ═══ --}}
    <div class="max-w-3xl mb-14">
        <h2 class="h2 mb-8">Časté otázky — ročník {{ $rok }}</h2>
        <div class="space-y-4">
            @foreach ($faq as $item)
                <details class="group bg-white border border-slate-200 rounded-2xl overflow-hidden">
                    <summary class="flex items-center justify-between gap-4 px-6 py-4 cursor-pointer font-semibold text-slate-900 hover:text-emerald-700 transition-colors list-none [&::-webkit-details-marker]:hidden">
                        {{ $item['q'] }}
                        <svg class="h-4 w-4 text-slate-400 shrink-0 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <p class="px-6 pb-5 text-sm text-slate-500 leading-relaxed">{{ $item['a'] }}</p>
                </details>
            @endforeach
        </div>
    </div>

    {{-- ═══ Související ročníky ═══ --}}
    <div class="max-w-3xl">
        <h2 class="h3 mb-5">Další ročníky</h2>
        <div class="flex flex-wrap gap-2">
            @foreach ($related as $r)
                <a href="{{ route('duchod.rocnik', $r) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-700 transition-all tabular-nums">
                    {{ $r }}
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
