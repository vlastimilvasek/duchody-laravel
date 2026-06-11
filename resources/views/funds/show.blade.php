@extends('layouts.app')

@push('head')
@php
    $schemaProduct = [
        '@context'    => 'https://schema.org',
        '@type'       => 'FinancialProduct',
        'name'        => $fund->name,
        'description' => "Účastnický fond doplňkového penzijního spoření společnosti {$fund->company}.",
        'url'         => route('fondy.show', $fund->slug),
        'provider'    => [
            '@type' => 'Organization',
            'name'  => $fund->company,
        ],
        'feesAndCommissionsSpecification' => "Úplata za správu {$fund->fee_management} % ročně"
            . ($fund->fee_performance > 0 ? ", úplata z výnosu {$fund->fee_performance} %" : ''),
        'annualPercentageRate' => [
            '@type' => 'QuantitativeValue',
            'value' => $fund->return_5y,
            'unitText' => 'PERCENT_PER_ANNUM',
        ],
    ];
    $schemaBreadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domů', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Penzijní fondy', 'item' => route('fondy.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $fund->name],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaProduct, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@php
    $typeMeta = [
        'konzervativni' => ['label' => 'Konzervativní', 'variant' => 'blue'],
        'vyvazeny'      => ['label' => 'Vyvážený', 'variant' => 'amber'],
        'dynamicky'     => ['label' => 'Dynamický', 'variant' => 'emerald'],
    ][$fund->fund_type] ?? ['label' => $fund->fund_type, 'variant' => 'slate'];
@endphp

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('fondy.index') }}" class="hover:text-emerald-600 transition-colors">Penzijní fondy</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">{{ Str::limit($fund->name, 40) }}</span>
    </nav>

    {{-- ═══ HERO ═══ --}}
    <div class="max-w-3xl mb-10">
        <x-badge :variant="$typeMeta['variant']" class="mb-4">{{ $typeMeta['label'] }} fond</x-badge>
        <h1 class="h1 mb-2">{{ $fund->name }}</h1>
        <p class="text-slate-500 text-lg">{{ $fund->company }}</p>
    </div>

    {{-- Klíčové metriky --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <x-data-card
            label="Výnos za 5 let (p.a.)"
            value="{{ $fund->return_5y !== null ? number_format($fund->return_5y, 1, ',', ' ') . ' %' : '—' }}"
            trend="{{ $fund->return_1y !== null ? 'poslední rok ' . number_format($fund->return_1y, 1, ',', ' ') . ' %' : null }}"
        />
        <x-data-card
            label="Roční poplatek za správu"
            value="{{ number_format($fund->fee_management, 2, ',', ' ') }} %"
        />
        <x-data-card
            label="Spravovaný majetek"
            value="{{ $fund->total_assets_mil !== null ? number_format($fund->total_assets_mil / 1000, 1, ',', ' ') . ' mld. Kč' : '—' }}"
            trend="{{ $fund->participants_count !== null ? number_format($fund->participants_count, 0, ',', ' ') . ' účastníků' : null }}"
        />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">

            {{-- ═══ GRAF: výnos vs inflace ═══ --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8" aria-label="Výnosová křivka">
                <h2 class="h3 mb-1">Výnos vs. inflace</h2>
                <p class="text-sm text-slate-400 mb-6">Roční zhodnocení v porovnání s inflací ČR, posledních 5 let</p>

                @php
                    $chartProps = json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                @endphp
                <div id="fund-chart" data-props="{{ $chartProps }}"></div>

                <p class="mt-4 text-xs text-slate-400">
                    Roční hodnoty jsou odvozeny z průměrných výnosů fondu za 1, 3 a 5 let.
                    Inflace dle ČSÚ (průměrný roční index spotřebitelských cen).
                </p>
            </section>

            {{-- ═══ POPLATKY ═══ --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8" aria-label="Poplatky">
                <h2 class="h3 mb-6">Poplatky fondu</h2>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <th scope="row" class="text-left font-medium text-slate-600 py-3.5 pr-4">
                                Úplata za správu (z majetku)
                                <p class="text-xs font-normal text-slate-400 mt-0.5">Roční poplatek z hodnoty vašich prostředků</p>
                            </th>
                            <td class="text-right font-semibold text-slate-900 tabular-nums py-3.5">{{ number_format($fund->fee_management, 2, ',', ' ') }} % ročně</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left font-medium text-slate-600 py-3.5 pr-4">
                                Úplata z výnosu
                                <p class="text-xs font-normal text-slate-400 mt-0.5">Podíl ze zhodnocení nad dosavadní maximum (zákonný strop 15 %)</p>
                            </th>
                            <td class="text-right font-semibold text-slate-900 tabular-nums py-3.5">
                                {{ $fund->fee_performance > 0 ? number_format($fund->fee_performance, 0, ',', ' ') . ' %' : 'neúčtuje se' }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left font-medium text-slate-600 py-3.5 pr-4">
                                Vstupní poplatek
                            </th>
                            <td class="text-right font-semibold text-slate-900 py-3.5">0 Kč</td>
                        </tr>
                        <tr>
                            <th scope="row" class="text-left font-medium text-slate-600 py-3.5 pr-4">
                                Změna strategie / převod k jiné společnosti
                                <p class="text-xs font-normal text-slate-400 mt-0.5">Zdarma po 5 letech spoření, jinak max 800 Kč</p>
                            </th>
                            <td class="text-right font-semibold text-slate-900 py-3.5">dle zákona</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            {{-- ═══ HODNOCENÍ ═══ --}}
            <section class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8" aria-label="Hodnocení fondu">
                <div class="flex items-start justify-between gap-6 flex-wrap">
                    <div>
                        <h2 class="h3 mb-2">Naše hodnocení</h2>
                        <div class="flex items-center gap-1" role="img" aria-label="{{ $rating['stars'] }} z 5 hvězd">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="h-6 w-6 {{ $i <= $rating['stars'] ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.077 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ $rating['rank'] }}.&nbsp;<span class="text-base font-medium text-slate-400">z {{ $rating['totalInCategory'] }}</span></p>
                        <p class="text-xs text-slate-400">v kategorii {{ Str::lower($typeMeta['label']) }}</p>
                    </div>
                </div>

                <div class="mt-6 pt-5 border-t border-slate-100">
                    <h3 class="text-sm font-semibold text-slate-900 mb-2">Jak hodnotíme — metodika</h3>
                    <p class="text-sm text-slate-500 leading-relaxed">
                        Skóre fondu počítáme jako vážený průměrný výnos
                        (<strong>50&nbsp;%</strong> pětiletý + <strong>30&nbsp;%</strong> tříletý +
                        <strong>20&nbsp;%</strong> roční výnos) snížený o <strong>dvojnásobek</strong> ročního
                        poplatku za správu. Fondy porovnáváme výhradně v rámci stejné kategorie —
                        konzervativní fond tedy nesoutěží s dynamickým. Výsledné pořadí převádíme
                        na 1–5 hvězd. Skóre tohoto fondu: <strong class="tabular-nums">{{ number_format($rating['score'], 2, ',', ' ') }}</strong>.
                    </p>
                </div>
            </section>
        </div>

        {{-- ═══ Pravý sloupec ═══ --}}
        <div class="space-y-6">
            <div class="bg-white border border-slate-200 rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-slate-900 mb-4">Přehled fondu</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Typ fondu</dt>
                        <dd class="font-medium text-slate-900">{{ $typeMeta['label'] }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Výnos 1 rok</dt>
                        <dd class="font-medium tabular-nums {{ ($fund->return_1y ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $fund->return_1y !== null ? number_format($fund->return_1y, 2, ',', ' ') . ' %' : '—' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Výnos 3 roky (p.a.)</dt>
                        <dd class="font-medium text-slate-900 tabular-nums">{{ $fund->return_3y !== null ? number_format($fund->return_3y, 2, ',', ' ') . ' %' : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Výnos 5 let (p.a.)</dt>
                        <dd class="font-medium text-slate-900 tabular-nums">{{ $fund->return_5y !== null ? number_format($fund->return_5y, 2, ',', ' ') . ' %' : '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Počet účastníků</dt>
                        <dd class="font-medium text-slate-900 tabular-nums">{{ $fund->participants_count !== null ? number_format($fund->participants_count, 0, ',', ' ') : '—' }}</dd>
                    </div>
                </dl>
            </div>

            <a href="{{ route('fondy.index') }}" class="block bg-white border border-slate-200 hover:border-emerald-200 rounded-2xl p-5 transition-colors group">
                <p class="text-sm font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors mb-1">← Zpět na srovnání</p>
                <p class="text-xs text-slate-400">Porovnejte tento fond s ostatními</p>
            </a>
        </div>
    </div>

    {{-- ═══ AFFILIATE CTA ═══ --}}
    <div class="mt-12">
        <x-affiliate.fund-cta :fund="$fund" />
    </div>

    {{-- Obecný disclaimer --}}
    <div class="mt-8 border-l-4 border-amber-400 bg-amber-50 rounded-r-lg p-4">
        <p class="text-sm text-amber-800">
            <strong>Upozornění:</strong> Minulé výnosy nejsou zárukou výnosů budoucích. Informace
            na této stránce nejsou investičním doporučením. Údaje jsou aktuální k&nbsp;{{ date('j. n. Y') }}.
        </p>
    </div>
</div>
@endsection
