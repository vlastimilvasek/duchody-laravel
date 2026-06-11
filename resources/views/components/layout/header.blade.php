<header class="sticky top-0 z-50 w-full border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between" aria-label="Hlavní navigace">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-0.5 shrink-0" aria-label="Důchody.cz — domů">
            <span class="text-xl font-bold tracking-tight text-slate-900">důchody</span>
            <span class="text-xl font-bold tracking-tight text-emerald-600">.cz</span>
        </a>

        {{-- Desktop navigace --}}
        <ul class="hidden md:flex items-center gap-1 text-sm font-medium" role="list">
            <li>
                <a href="{{ route('kalkulacka.index') }}"
                   class="px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors {{ request()->routeIs('kalkulacka.*') ? 'text-emerald-600 bg-emerald-50' : '' }}">
                    Kalkulačky
                </a>
            </li>
            <li>
                <a href="{{ route('fondy.index') }}"
                   class="px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors {{ request()->routeIs('fondy.*') ? 'text-emerald-600 bg-emerald-50' : '' }}">
                    Penzijní fondy
                </a>
            </li>
            <li>
                <a href="{{ route('statistiky.index') }}"
                   class="px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors {{ request()->routeIs('statistiky.*') ? 'text-emerald-600 bg-emerald-50' : '' }}">
                    Statistiky
                </a>
            </li>
            <li>
                <a href="{{ route('magazin.index') }}"
                   class="px-3 py-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition-colors {{ request()->routeIs('magazin.*') ? 'text-emerald-600 bg-emerald-50' : '' }}">
                    Magazín
                </a>
            </li>
        </ul>

        {{-- Desktop CTA --}}
        <div class="hidden md:flex items-center gap-3">
            <a href="{{ route('kalkulacka.vek') }}"
               class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19V7a2 2 0 012-2h12a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2z"/>
                </svg>
                Spočítat důchod
            </a>
        </div>

        {{-- Mobilní menu tlačítko --}}
        <button id="mobile-menu-toggle"
                type="button"
                class="md:hidden p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors"
                aria-controls="mobile-menu"
                aria-expanded="false"
                aria-label="Otevřít menu">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </nav>

    {{-- Mobilní menu --}}
    <div id="mobile-menu"
         class="hidden md:hidden border-t border-slate-200 bg-white"
         aria-hidden="true">
        <nav class="max-w-7xl mx-auto px-4 py-4 space-y-1" aria-label="Mobilní navigace">
            <a href="{{ route('kalkulacka.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                Kalkulačky
            </a>
            <a href="{{ route('fondy.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                Penzijní fondy
            </a>
            <a href="{{ route('statistiky.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                Statistiky
            </a>
            <a href="{{ route('magazin.index') }}" class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-slate-700 hover:bg-slate-50 font-medium">
                Magazín
            </a>
            <div class="pt-2 border-t border-slate-100">
                <a href="{{ route('kalkulacka.vek') }}" class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-3 rounded-lg transition-colors w-full">
                    Spočítat důchod
                </a>
            </div>
        </nav>
    </div>
</header>
