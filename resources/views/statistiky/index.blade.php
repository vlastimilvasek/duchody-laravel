@extends('layouts.app')

@push('head')
@php
    $schemaDataset = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Dataset',
        'name'        => 'Statistiky důchodů v České republice',
        'description' => 'Průměrná výše starobního důchodu, počet důchodců a regionální srovnání. Zdroj: ČSSZ.',
        'url'         => route('statistiky.index'),
        'creator'     => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
        'license'     => 'https://creativecommons.org/licenses/by/4.0/',
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaDataset, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Statistiky</span>
    </nav>

    {{-- Hlavička --}}
    <div class="max-w-3xl mb-10">
        <x-badge variant="emerald" class="mb-4">Data ČSSZ · aktualizováno {{ date('n/Y') }}</x-badge>
        <h1 class="h1 mb-4">Statistiky důchodů v ČR</h1>
        <p class="lead">
            Průměrná výše důchodů, vývoj od roku 2010, rozložení mezi důchodci
            a srovnání se zeměmi EU — vše na jednom místě.
        </p>
    </div>

    {{-- ═══ Klíčové ukazatele (Blade metric cards) ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <x-data-card
            label="Průměrný starobní důchod"
            value="{{ $latest ? number_format($latest->average_amount, 0, ',', ' ') . ' Kč' : '—' }}"
            trend="+358 Kč od ledna 2026"
        />
        <x-data-card
            label="Počet starobních důchodců"
            value="{{ $latest ? number_format($latest->count / 1000000, 2, ',', ' ') . ' mil.' : '—' }}"
        />
        <x-data-card
            label="Průměrný invalidní důchod"
            value="{{ $invalidni ? number_format($invalidni->average_amount, 0, ',', ' ') . ' Kč' : '—' }}"
        />
        <x-data-card
            label="Průměrný vdovský důchod"
            value="{{ $vdovsky ? number_format($vdovsky->average_amount, 0, ',', ' ') . ' Kč' : '—' }}"
            trend="včetně souběhu se starobním"
        />
    </div>

    {{-- ═══ Grafy (Vue island) ═══ --}}
    @php
        $dashboardProps = json_encode($dashboard, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @endphp
    <div id="stats-dashboard" data-props="{{ $dashboardProps }}">
        <noscript>
            <p class="text-sm text-slate-500 border border-slate-200 rounded-xl bg-white p-6">
                Interaktivní grafy vyžadují zapnutý JavaScript. Klíčová data najdete
                v kartách výše a v přehledu krajů níže.
            </p>
        </noscript>
    </div>

    {{-- ═══ Kraje ═══ --}}
    <div class="mt-16">
        <div class="flex items-end justify-between flex-wrap gap-4 mb-8">
            <div>
                <h2 class="h2 mb-2">Důchody podle krajů</h2>
                <p class="text-slate-500">Průměrný starobní důchod a rozdíl oproti celostátnímu průměru.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach ($regionCards as $card)
                <a href="{{ route('statistiky.show', $card['slug']) }}"
                   class="group bg-white border border-slate-200 rounded-2xl p-5 hover:border-emerald-200 hover:shadow-md transition-all">
                    <p class="text-sm font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors mb-2">
                        {{ $card['name'] }}
                    </p>
                    <p class="text-2xl font-bold text-slate-900 tabular-nums">
                        {{ $card['average'] !== null ? number_format($card['average'], 0, ',', ' ') . ' Kč' : '—' }}
                    </p>
                    @if ($card['diff'] !== null)
                        <p class="mt-1 text-xs tabular-nums {{ $card['diff'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $card['diff'] >= 0 ? '+' : '' }}{{ number_format($card['diff'], 0, ',', ' ') }} Kč vs. průměr ČR
                        </p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    {{-- Zdroj dat --}}
    <div class="mt-12 border-l-4 border-slate-300 bg-slate-50 rounded-r-lg p-4">
        <p class="text-sm text-slate-600">
            <strong>Zdroj dat:</strong> Česká správa sociálního zabezpečení (ČSSZ) a Český statistický
            úřad (ČSÚ). Srovnání s EU vychází z dat OECD a Eurostatu (náhradový poměr). Údaje jsou
            aktuální k&nbsp;{{ date('j. n. Y') }}.
        </p>
    </div>
</div>
@endsection
