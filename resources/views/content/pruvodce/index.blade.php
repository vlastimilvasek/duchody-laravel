@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Průvodci</span>
    </nav>

    <div class="max-w-3xl mb-10">
        <h1 class="h1 mb-4">Průvodci důchodem</h1>
        <p class="lead">
            Srozumitelné a kompletní návody českým důchodovým systémem —
            od podmínek nároku po strategii spoření.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl">
        @foreach ($guides as $slug => $guide)
            <a href="{{ route('pruvodce.show', $slug) }}"
               class="group bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
                <h2 class="text-lg font-bold text-slate-900 group-hover:text-emerald-700 transition-colors mb-2">
                    {{ Str::before($guide['title'], ' | ') }}
                </h2>
                <p class="text-sm text-slate-500 leading-relaxed mb-4">{{ $guide['description'] }}</p>
                <span class="inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 group-hover:text-emerald-700">
                    Číst průvodce
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </span>
            </a>
        @endforeach
    </div>
</div>
@endsection
