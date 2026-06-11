@props(['meta' => []])

<title>{{ $meta['title'] ?? 'Důchody.cz — #1 portál o důchodech v ČR' }}</title>
<meta name="description" content="{{ $meta['description'] ?? 'Kalkulačka důchodu, srovnání penzijních fondů a aktuální informace o důchodech v České republice.' }}">
<link rel="canonical" href="{{ $meta['canonical'] ?? url()->current() }}">
<meta name="robots" content="{{ $meta['robots'] ?? 'index, follow' }}">

{{-- OpenGraph --}}
<meta property="og:title" content="{{ $meta['ogTitle'] ?? ($meta['title'] ?? 'Důchody.cz') }}">
<meta property="og:description" content="{{ $meta['ogDescription'] ?? ($meta['description'] ?? '') }}">
<meta property="og:type" content="{{ $meta['ogType'] ?? 'website' }}">
<meta property="og:locale" content="cs_CZ">
<meta property="og:site_name" content="Důchody.cz">
<meta property="og:image" content="{{ $meta['ogImage'] ?? asset('images/og-default.png') }}">

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] ?? 'Důchody.cz' }}">
<meta name="twitter:description" content="{{ $meta['description'] ?? '' }}">
<meta name="twitter:image" content="{{ $meta['ogImage'] ?? asset('images/og-default.png') }}">
