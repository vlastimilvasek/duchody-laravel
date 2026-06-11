@extends('layouts.app')

@push('head')
@php
    $schemaList = [
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        'name'            => 'Srovnání penzijních fondů v ČR',
        'numberOfItems'   => $funds->count(),
        'itemListElement' => $funds->values()->map(fn ($fund, $i) => [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'url'      => route('fondy.show', $fund->slug),
            'name'     => $fund->name,
        ])->all(),
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Penzijní fondy</span>
    </nav>

    {{-- Hlavička --}}
    <div class="max-w-3xl mb-10">
        <x-badge variant="emerald" class="mb-4">{{ $funds->count() }} fondů · aktualizováno {{ date('n/Y') }}</x-badge>
        <h1 class="h1 mb-4">Srovnání penzijních fondů</h1>
        <p class="lead">
            Výnosy, poplatky a velikost všech účastnických fondů doplňkového penzijního
            spoření v ČR. Seřaďte si tabulku podle toho, co vás zajímá, a porovnejte
            až tři fondy vedle sebe.
        </p>
    </div>

    {{-- Vue island — interaktivní tabulka --}}
    @php
        $fundsProps = json_encode(['funds' => $fundsForTable], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    @endphp
    <div id="funds-table" data-props="{{ $fundsProps }}">
        <noscript>
            <div class="border border-slate-200 rounded-2xl bg-white overflow-x-auto">
                <table class="w-full text-sm" aria-label="Srovnání penzijních fondů">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50 text-left">
                            <th class="px-4 py-3 font-semibold text-slate-600">Fond</th>
                            <th class="px-4 py-3 font-semibold text-slate-600">Výnos 1 rok</th>
                            <th class="px-4 py-3 font-semibold text-slate-600">Výnos 5 let</th>
                            <th class="px-4 py-3 font-semibold text-slate-600">Poplatek</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($funds as $fund)
                            <tr class="border-b border-slate-100">
                                <td class="px-4 py-3">
                                    <a href="{{ route('fondy.show', $fund->slug) }}" class="font-semibold text-slate-900">{{ $fund->name }}</a>
                                    <p class="text-xs text-slate-400">{{ $fund->company }}</p>
                                </td>
                                <td class="px-4 py-3 tabular-nums">{{ $fund->return_1y !== null ? number_format($fund->return_1y, 1, ',', ' ') . ' %' : '—' }}</td>
                                <td class="px-4 py-3 tabular-nums">{{ $fund->return_5y !== null ? number_format($fund->return_5y, 1, ',', ' ') . ' %' : '—' }}</td>
                                <td class="px-4 py-3 tabular-nums">{{ number_format($fund->fee_management, 2, ',', ' ') }} %</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </noscript>
    </div>

    {{-- Affiliate disclaimer — zákonná povinnost --}}
    <div class="mt-8 border-l-4 border-slate-300 bg-slate-50 rounded-r-lg p-4">
        <p class="text-sm text-slate-600">
            <strong>Upozornění:</strong> Tento web může získat provizi za sjednání produktu
            prostřednictvím výše uvedených odkazů. Tato skutečnost neovlivňuje naše hodnocení
            ani pořadí fondů.
        </p>
    </div>

    {{-- ═══ Kalkulačka spoření ═══ --}}
    <div id="kalkulacka-sporeni" class="mt-16 scroll-mt-24">
        <div class="text-center mb-8">
            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest mb-3">Kalkulačka</p>
            <h2 class="h2 mb-3">Kolik naspoříte?</h2>
            <p class="lead max-w-xl mx-auto">Zvolte měsíční úložku, fond a dobu spoření — uvidíte odhad včetně státních příspěvků.</p>
        </div>
        <div id="savings-calculator" data-props="{{ $fundsProps }}" class="max-w-4xl mx-auto"></div>
    </div>

    {{-- Metodika --}}
    <div class="mt-12 max-w-3xl">
        <h2 class="h3 mb-4">Odkud bereme data?</h2>
        <p class="text-sm text-slate-500 leading-relaxed">
            Výnosy fondů vycházejí z veřejně dostupných dat penzijních společností a Asociace
            penzijních společností ČR. Výnosy za 3 a 5 let uvádíme jako průměrné roční (p.a.).
            Poplatek za správu je roční úplata z hodnoty majetku; u dynamických a vyvážených
            fondů se navíc účtuje úplata z výnosu (zákonný strop 15&nbsp;%). Minulé výnosy
            nejsou zárukou výnosů budoucích.
        </p>
    </div>
</div>
@endsection
