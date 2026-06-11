<!DOCTYPE html>
<html lang="cs" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#059669">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Preload kritických fontů --}}
    <link rel="preload" href="/fonts/InterVariable.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/InstrumentSerif-Regular.woff2" as="font" type="font/woff2" crossorigin>

    {{-- SEO meta --}}
    <x-layout.meta :meta="$meta ?? []" />

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">

    {{-- Vite assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Přídavné hlavičkové sekce (structured data, atd.) --}}
    @stack('head')
</head>
<body class="bg-slate-50 font-sans text-slate-700 antialiased">

    {{-- Skip to content --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[100] focus:bg-emerald-600 focus:text-white focus:px-4 focus:py-2 focus:rounded-lg focus:font-semibold">
        Přejít na obsah
    </a>

    {{-- Navigace --}}
    <x-layout.header />

    {{-- Hlavní obsah --}}
    <main id="main-content">
        @yield('content')
    </main>

    {{-- Patička --}}
    <x-layout.footer />

    {{-- Přídavné skripty --}}
    @stack('scripts')

</body>
</html>
