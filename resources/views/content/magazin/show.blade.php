@extends('layouts.app')

@push('head')
@php
    $schemaArticle = [
        '@context'      => 'https://schema.org',
        '@type'         => 'Article',
        'headline'      => $article->title,
        'description'   => $article->perex,
        'datePublished' => $article->published_at?->toIso8601String(),
        'dateModified'  => $article->updated_at?->toIso8601String(),
        'author'        => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
        'publisher'     => [
            '@type' => 'Organization',
            'name'  => 'Důchody.cz',
            'logo'  => ['@type' => 'ImageObject', 'url' => asset('images/logo.png')],
        ],
    ];
    $schemaBreadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domů', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Magazín', 'item' => route('magazin.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $article->title],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaArticle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('magazin.index') }}" class="hover:text-emerald-600 transition-colors">Magazín</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">{{ Str::limit($article->title, 50) }}</span>
    </nav>

    <article class="max-w-3xl">
        <div class="mb-4">
            <a href="{{ route('magazin.index', ['kategorie' => $article->category]) }}">
                <x-badge variant="slate">{{ $categoryLabel }}</x-badge>
            </a>
        </div>

        <h1 class="h1 mb-6">{{ $article->title }}</h1>

        <p class="editorial-lead mb-8">{{ $article->perex }}</p>

        <div class="flex items-center gap-4 text-sm text-slate-400 mb-10 pb-8 border-b border-slate-200">
            <time datetime="{{ $article->published_at?->toIso8601String() }}">
                {{ $article->published_at?->format('j. n. Y') }}
            </time>
            <span aria-hidden="true">·</span>
            <span>{{ $article->readingTimeMinutes() }} min čtení</span>
            <button
                type="button"
                data-share
                data-share-title="{{ $article->title }}"
                class="ml-auto inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-emerald-700 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
                <span data-share-label>Sdílet</span>
            </button>
        </div>

        {{-- Markdown obsah (league/commonmark + shortcody) --}}
        <div class="prose-duchody">
            {!! $contentHtml !!}
        </div>

        {{-- Tagy --}}
        @if ($article->tags)
            <div class="mt-10 flex flex-wrap gap-2">
                @foreach ($article->tags as $tag)
                    <span class="text-xs font-medium text-slate-500 bg-slate-100 px-3 py-1.5 rounded-full">#{{ $tag }}</span>
                @endforeach
            </div>
        @endif

        {{-- Autor --}}
        <div class="mt-10">
            <x-content.author-box />
        </div>
    </article>

    {{-- ═══ Související články ═══ --}}
    @if ($related->isNotEmpty())
        <div class="max-w-5xl mt-16 pt-12 border-t border-slate-200">
            <h2 class="h3 mb-6">Mohlo by vás zajímat</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($related as $rel)
                    <article class="group bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
                        <h3 class="text-base font-bold text-slate-900 leading-snug mb-2">
                            <a href="{{ route('magazin.show', $rel->slug) }}" class="group-hover:text-emerald-700 transition-colors">
                                {{ $rel->title }}
                            </a>
                        </h3>
                        <p class="text-sm text-slate-500 mb-4">{{ Str::limit($rel->perex, 90) }}</p>
                        <div class="text-xs text-slate-400">
                            {{ $rel->published_at?->format('j. n. Y') }} · {{ $rel->readingTimeMinutes() }} min
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
