@props([
    'label' => '',
    'value' => '',
    'trend' => null,
    'icon'  => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-slate-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow']) }}>
    @if($icon)
        <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center mb-4">
            {!! $icon !!}
        </div>
    @endif
    <p class="text-sm text-slate-500 mb-1">{{ $label }}</p>
    <p class="text-3xl font-bold text-slate-900 tabular-nums tracking-tight">{{ $value }}</p>
    @if($trend)
        <p class="text-sm text-emerald-600 mt-1 font-medium">↑ {{ $trend }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
