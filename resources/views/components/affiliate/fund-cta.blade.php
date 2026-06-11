@props(['fund'])

<div class="bg-slate-900 rounded-2xl p-6 sm:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-8">
        <div class="flex-1">
            <p class="text-white text-lg font-bold mb-1">Zajímá vás {{ $fund->name }}?</p>
            <p class="text-sm text-slate-400">
                Sjednání online zabere přibližně 10 minut. Od roku 2024 lze získat státní příspěvek
                až 340 Kč měsíčně.
            </p>
        </div>
        @if ($fund->affiliate_url)
            <a href="{{ $fund->affiliate_url }}"
               target="_blank"
               rel="nofollow sponsored noopener"
               class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-6 py-3 rounded-xl shadow-sm transition-colors whitespace-nowrap shrink-0">
                Sjednat online
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        @else
            <span class="inline-flex items-center justify-center border border-slate-700 text-slate-400 font-medium text-sm px-6 py-3 rounded-xl whitespace-nowrap shrink-0">
                Online sjednání připravujeme
            </span>
        @endif
    </div>

    {{-- Zákonem vyžadovaný affiliate disclaimer --}}
    <p class="mt-5 pt-4 border-t border-slate-800 text-xs text-slate-500">
        <strong class="text-slate-400">Upozornění:</strong> Tento web může získat provizi za sjednání
        produktu prostřednictvím výše uvedených odkazů. Tato skutečnost neovlivňuje naše hodnocení
        ani pořadí fondů.
    </p>
</div>
