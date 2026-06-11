@props([
    'variant' => 'default',
    'dot'     => false,
])

@php
    $styles = match($variant) {
        'emerald', 'success' => 'bg-emerald-100 text-emerald-800',
        'amber', 'warning'   => 'bg-amber-100 text-amber-800',
        'red', 'danger'      => 'bg-red-100 text-red-700',
        'blue', 'info'       => 'bg-blue-100 text-blue-800',
        'slate', 'default'   => 'bg-slate-100 text-slate-700',
        default              => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium $styles"]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full bg-current opacity-70" aria-hidden="true"></span>
    @endif
    {{ $slot }}
</span>
