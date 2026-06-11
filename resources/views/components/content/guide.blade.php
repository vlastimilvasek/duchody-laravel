@props(['title', 'perex', 'badge' => 'Aktuální pro rok 2026'])

@push('head')
@php
    $schemaGuideArticle = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Article',
        'headline'    => $title,
        'description' => $perex,
        'author'      => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
        'publisher'   => ['@type' => 'Organization', 'name' => 'Důchody.cz'],
    ];
    $schemaGuideBreadcrumb = [
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Domů', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Průvodci', 'item' => route('pruvodce.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $title],
        ],
    ];
@endphp
<script type="application/ld+json">{{ json_encode($schemaGuideArticle, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
<script type="application/ld+json">{{ json_encode($schemaGuideBreadcrumb, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</script>
@endpush

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8" aria-label="Breadcrumb">
        <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Domů</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('pruvodce.index') }}" class="hover:text-emerald-600 transition-colors">Průvodci</a>
        <span aria-hidden="true">/</span>
        <span class="text-slate-900 font-medium" aria-current="page">{{ Str::limit($title, 45) }}</span>
    </nav>

    <article class="max-w-3xl">
        <x-badge variant="emerald" class="mb-4">{{ $badge }}</x-badge>
        <h1 class="h1 mb-6">{{ $title }}</h1>
        <p class="editorial-lead mb-10 pb-8 border-b border-slate-200">{{ $perex }}</p>

        <div class="prose-duchody">
            {{ $slot }}
        </div>

        {{-- Autor --}}
        <div class="mt-12">
            <x-content.author-box />
        </div>

        {{-- Disclaimer --}}
        <div class="mt-8 border-l-4 border-amber-400 bg-amber-50 rounded-r-lg p-4 not-prose">
            <p class="text-sm text-amber-800">
                <strong>Upozornění:</strong> Informace v tomto průvodci jsou obecné a nenahrazují
                individuální posouzení. Přesný výpočet a posouzení nároku provádí výhradně ČSSZ.
                Aktuální k&nbsp;{{ date('j. n. Y') }}.
            </p>
        </div>
    </article>
</div>
