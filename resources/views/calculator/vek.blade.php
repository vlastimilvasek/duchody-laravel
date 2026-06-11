@extends('layouts.app')

@push('head')
@php
    $schemaApp = [
        '@context'            => 'https://schema.org',
        '@type'               => 'WebApplication',
        'name'                => 'Kalkulačka důchodového věku',
        'applicationCategory' => 'FinanceApplication',
        'description'         => 'Bezplatná online kalkulačka pro zjištění důchodového věku v ČR',
        'url'                 => route('kalkulacka.vek'),
        'offers'              => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'CZK'],
        'inLanguage'          => 'cs',
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaApp, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('kalkulacka.index') }}" class="hover:text-emerald-600 transition-colors">Kalkulačky</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Důchodový věk</span>
    </nav>

    <div class="max-w-2xl">
        <x-badge variant="emerald" class="mb-4">Aktuální pro rok 2026</x-badge>
        <h1 class="h1 mb-4">Kdy půjdu do důchodu?</h1>
        <p class="lead mb-10">Zadejte datum narození, pohlaví a počet vychovaných dětí — výsledek zobrazíme okamžitě.</p>
    </div>

    {{-- Vue island — kalkulačka bude přidána ve Fázi 1 --}}
    <div id="pension-age-calculator" class="max-w-2xl bg-white border border-slate-200 rounded-xl p-8 shadow-sm">
        <form method="POST" action="{{ route('kalkulacka.vek.calculate') }}" class="space-y-6">
            @csrf

            <div class="space-y-2">
                <label for="birth_date" class="block text-sm font-medium text-slate-700">
                    Datum narození <span class="text-red-500" aria-hidden="true">*</span>
                </label>
                <input type="date"
                       id="birth_date"
                       name="birth_date"
                       required
                       class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-colors"
                       aria-describedby="birth_date_hint" />
                <p id="birth_date_hint" class="text-xs text-slate-500">Datum ve formátu den.měsíc.rok</p>
            </div>

            <fieldset class="space-y-2">
                <legend class="text-sm font-medium text-slate-700">Pohlaví <span class="text-red-500" aria-hidden="true">*</span></legend>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="M" class="text-emerald-600" required />
                        <span class="text-slate-700">Muž</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="gender" value="F" class="text-emerald-600" />
                        <span class="text-slate-700">Žena</span>
                    </label>
                </div>
            </fieldset>

            <div class="space-y-2">
                <label for="children" class="block text-sm font-medium text-slate-700">
                    Počet vychovaných dětí
                </label>
                <select id="children" name="children"
                        class="w-full border border-slate-200 rounded-lg px-3 py-2.5 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none transition-colors">
                    <option value="0">0 — bezdětný/á</option>
                    <option value="1">1 dítě</option>
                    <option value="2">2 děti</option>
                    <option value="3">3 děti</option>
                    <option value="4">4 a více dětí</option>
                </select>
                <p class="text-xs text-slate-500">Vliv na důchodový věk žen narozených před rokem 1966.</p>
            </div>

            <x-button type="submit" class="w-full justify-center">
                Zjistit důchodový věk
            </x-button>
        </form>
    </div>

    {{-- Disclaimer --}}
    <div class="mt-8 max-w-2xl">
        <div class="border-l-4 border-amber-400 bg-amber-50 rounded-r-lg p-4">
            <p class="text-sm text-amber-800">
                <strong>Upozornění:</strong> Výpočet je orientační dle zákona 155/1995 Sb. Přesné datum odchodu závisí na splnění podmínek pojištění. Ověřte na ČSSZ.
            </p>
        </div>
    </div>
</div>
@endsection
