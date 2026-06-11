@extends('layouts.app')

@push('head')
@php
    $schemaBlog = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Blog',
        'name'        => 'Magazín Důchody.cz',
        'description' => 'Aktuality, rady a data ze světa důchodů a penzijního spoření.',
        'url'         => route('magazin.index'),
        'publisher'   => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaBlog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">Magazín</span>
    </nav>

    {{-- Hlavička --}}
    <div class="max-w-3xl mb-10">
        <h1 class="h1 mb-4">Magazín</h1>
        <p class="lead">
            Aktuální informace o důchodech, valorizaci a penzijním spoření.
            Srozumitelně a bez balastu.
        </p>
    </div>

    {{-- ═══ Filtry + hledání ═══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-10">
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filtr podle kategorie">
            <a href="{{ route('magazin.index') }}"
               class="px-4 py-2 rounded-full text-sm font-medium border transition-all {{ $activeCategory === null ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }}">
                Vše
            </a>
            @foreach ($categories as $key => $label)
                @continue(! isset($categoryCounts[$key]))
                <a href="{{ route('magazin.index', ['kategorie' => $key]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium border transition-all {{ $activeCategory === $key ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-400' }}">
                    {{ $label }}
                    <span class="text-xs opacity-60">({{ $categoryCounts[$key] }})</span>
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('magazin.index') }}" class="relative sm:ml-auto" role="search">
            @if ($activeCategory)
                <input type="hidden" name="kategorie" value="{{ $activeCategory }}">
            @endif
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
                type="search" name="q" value="{{ $search }}"
                placeholder="Hledat v magazínu…" aria-label="Hledat v magazínu"
                class="w-full sm:w-60 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-sm text-slate-900 bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
            />
        </form>
    </div>

    {{-- ═══ Listing ═══ --}}
    @if ($articles->isEmpty())
        <div class="border border-slate-200 rounded-2xl bg-white py-16 text-center">
            <svg class="h-10 w-10 text-slate-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-slate-600 font-medium mb-1">Nenašli jsme žádné články</p>
            <p class="text-sm text-slate-400 mb-5">Zkuste jinou kategorii nebo upravte hledaný výraz.</p>
            <a href="{{ route('magazin.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 transition-colors">
                Zobrazit všechny články
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($articles as $article)
                <article class="group bg-white border border-slate-200 rounded-2xl shadow-sm hover:shadow-md hover:border-emerald-200 transition-all flex flex-col">
                    <div class="p-6 flex flex-col flex-1">
                        <div class="mb-3">
                            <x-badge variant="slate">{{ $categories[$article->category] ?? $article->category }}</x-badge>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 leading-snug mb-2">
                            <a href="{{ route('magazin.show', $article->slug) }}" class="group-hover:text-emerald-700 transition-colors">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-sm text-slate-500 leading-relaxed mb-5 flex-1">{{ Str::limit($article->perex, 140) }}</p>
                        <div class="flex items-center justify-between text-xs text-slate-400 pt-4 border-t border-slate-100">
                            <time datetime="{{ $article->published_at?->toIso8601String() }}">
                                {{ $article->published_at?->format('j. n. Y') }}
                            </time>
                            <span>{{ $article->readingTimeMinutes() }} min čtení</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">{{ $articles->links() }}</div>
    @endif
</div>
@endsection
