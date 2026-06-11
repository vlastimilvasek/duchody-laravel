@props([
    'variant' => 'primary',
    'href'    => null,
    'type'    => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-semibold rounded-lg transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

    $styles = match($variant) {
        'primary'   => 'bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 shadow-sm hover:shadow-md',
        'secondary' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-300 px-6 py-3',
        'ghost'     => 'text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 px-4 py-2',
        'hero'      => 'bg-emerald-600 hover:bg-emerald-700 text-white text-lg px-8 py-4 rounded-xl shadow-sm hover:shadow-md',
        'danger'    => 'bg-red-500 hover:bg-red-600 text-white px-6 py-3 shadow-sm',
        default     => 'bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 shadow-sm',
    };

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    @if(!$href) type="{{ $type }}" @endif
    {{ $attributes->merge(['class' => "$base $styles"]) }}>
    {{ $slot }}
</{{ $tag }}>
