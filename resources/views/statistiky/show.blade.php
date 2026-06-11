@extends('layouts.app')

@push('head')
@php
    $schemaBreadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domů', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Statistiky', 'item' => route('statistiky.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $region['name']],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('statistiky.index') }}" class="hover:text-emerald-600 transition-colors">Statistiky</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">{{ $region['name'] }}</span>
    </nav>

    <div class="max-w-3xl mb-10">
        <x-badge variant="emerald" class="mb-4">Data ČSSZ {{ $latest->period->year }}</x-badge>
        <h1 class="h1 mb-4">Průměrný důchod — {{ $region['name'] }}</h1>
        <p class="lead">
            Aktuální výše důchodů v kraji, srovnání s celostátním průměrem
            a vývoj za posledních {{ count($trend['years']) }} let.
        </p>
    </div>

    {{-- ═══ Důchody dle typu ═══ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <x-data-card
            label="Starobní důchod"
            value="{{ number_format($latest->average_amount, 0, ',', ' ') }} Kč"
            trend="{{ number_format($latest->count, 0, ',', ' ') }} důchodců v kraji"
        />
        <x-data-card
            label="Invalidní důchod"
            value="{{ $invalidni ? number_format($invalidni->average_amount, 0, ',', ' ') . ' Kč' : '—' }}"
        />
        <x-data-card
            label="Vdovský důchod"
            value="{{ $vdovsky ? number_format($vdovsky->average_amount, 0, ',', ' ') . ' Kč' : '—' }}"
            trend="včetně souběhu se starobním"
        />
    </div>

    {{-- ═══ Srovnání s ČR ═══ --}}
    @if ($comparison)
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 mb-10">
            <h2 class="h3 mb-6">Srovnání s celostátním průměrem</h2>
            <div class="flex flex-col sm:flex-row sm:items-center gap-6 sm:gap-12">
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">{{ $region['name'] }}</p>
                    <p class="text-3xl font-bold text-slate-900 tabular-nums">{{ number_format($latest->average_amount, 0, ',', ' ') }} Kč</p>
                </div>
                <div class="hidden sm:block h-12 w-px bg-slate-200"></div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Průměr ČR</p>
                    <p class="text-3xl font-bold text-slate-400 tabular-nums">{{ number_format($comparison['national'], 0, ',', ' ') }} Kč</p>
                </div>
                <div class="hidden sm:block h-12 w-px bg-slate-200"></div>
                <div>
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-semibold mb-1">Rozdíl</p>
                    <p class="text-3xl font-bold tabular-nums {{ $comparison['diffKc'] >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $comparison['diffKc'] >= 0 ? '+' : '' }}{{ number_format($comparison['diffKc'], 0, ',', ' ') }} Kč
                        <span class="text-base font-semibold">({{ $comparison['diffPct'] >= 0 ? '+' : '' }}{{ number_format($comparison['diffPct'], 1, ',', ' ') }} %)</span>
                    </p>
                </div>
            </div>
            <p class="mt-5 pt-4 border-t border-slate-100 text-sm text-slate-500">
                @if ($comparison['diffKc'] >= 0)
                    Důchodci v kraji {{ $region['name'] }} pobírají v průměru o {{ number_format(abs($comparison['diffKc']), 0, ',', ' ') }} Kč
                    měsíčně <strong class="text-emerald-700">více</strong> než je celostátní průměr —
                    ročně to představuje asi {{ number_format(abs($comparison['diffKc']) * 12, 0, ',', ' ') }} Kč.
                @else
                    Důchodci v kraji {{ $region['name'] }} pobírají v průměru o {{ number_format(abs($comparison['diffKc']), 0, ',', ' ') }} Kč
                    měsíčně <strong class="text-red-600">méně</strong> než je celostátní průměr —
                    ročně to představuje asi {{ number_format(abs($comparison['diffKc']) * 12, 0, ',', ' ') }} Kč.
                @endif
            </p>
        </div>
    @endif

    {{-- ═══ Vývoj (Vue island mini LineChart) ═══ --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 sm:p-8 mb-10">
        <h2 class="h3 mb-1">Vývoj průměrného starobního důchodu</h2>
        <p class="text-sm text-slate-400 mb-6">{{ $trend['years'][0] }}–{{ end($trend['years']) }}, Kč/měsíc</p>
        @php
            $trendProps = json_encode($trend, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        @endphp
        <div id="region-trend-chart" data-props="{{ $trendProps }}"></div>
    </div>

    {{-- ═══ Ostatní kraje ═══ --}}
    <div class="mb-10">
        <h2 class="h3 mb-5">Další kraje</h2>
        <div class="flex flex-wrap gap-2">
            @foreach ($otherRegions as $other)
                <a href="{{ route('statistiky.show', $other['slug']) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium bg-white border border-slate-200 text-slate-600 hover:border-emerald-300 hover:text-emerald-700 transition-all">
                    {{ $other['name'] }}
                </a>
            @endforeach
        </div>
    </div>

    <a href="{{ route('statistiky.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
        ← Celostátní statistiky a všechny kraje
    </a>

    {{-- Zdroj --}}
    <div class="mt-10 border-l-4 border-slate-300 bg-slate-50 rounded-r-lg p-4">
        <p class="text-sm text-slate-600">
            <strong>Zdroj dat:</strong> ČSSZ. Údaje jsou aktuální k&nbsp;{{ date('j. n. Y') }}.
        </p>
    </div>
</div>
@endsection
