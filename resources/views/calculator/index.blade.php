@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <h1 class="h1 mb-8">Kalkulačky důchodů</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('kalkulacka.vek') }}" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Kalkulačka důchodového věku</h2>
            <p class="text-slate-500 text-sm">Zjistěte kdy půjdete do důchodu.</p>
        </a>
        <a href="{{ route('kalkulacka.vyse') }}" class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
            <h2 class="text-xl font-bold text-slate-900 mb-2">Výpočet výše důchodu</h2>
            <p class="text-slate-500 text-sm">Orientační výpočet vaší penze.</p>
        </a>
    </div>
</div>
@endsection
