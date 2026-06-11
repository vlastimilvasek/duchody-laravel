<footer class="bg-slate-900 text-slate-300" role="contentinfo">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">

            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-0.5 mb-4">
                    <span class="text-xl font-bold text-white">důchody</span>
                    <span class="text-xl font-bold text-emerald-400">.cz</span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">
                    Přesnější než státní weby. Bezplatné kalkulačky, srovnávače a aktuální informace o důchodech v ČR.
                </p>
                <p class="text-xs text-slate-500">
                    © {{ date('Y') }} Důchody.cz<br>
                    Všechna práva vyhrazena.
                </p>
            </div>

            {{-- Kalkulačky --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Kalkulačky</h3>
                <ul class="space-y-2 text-sm" role="list">
                    <li><a href="{{ route('kalkulacka.vek') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Kdy půjdu do důchodu</a></li>
                    <li><a href="{{ route('kalkulacka.vyse') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Výše starobního důchodu</a></li>
                    <li><a href="{{ route('kalkulacka.vyse') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Předčasný důchod</a></li>
                    <li><a href="{{ route('fondy.index') }}#kalkulacka-sporeni" class="text-slate-400 hover:text-emerald-400 transition-colors">Penzijní spoření</a></li>
                    <li><a href="{{ route('dip') }}#kalkulacka" class="text-slate-400 hover:text-emerald-400 transition-colors">DIP kalkulačka</a></li>
                </ul>
            </div>

            {{-- Průvodci --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Průvodci</h3>
                <ul class="space-y-2 text-sm" role="list">
                    <li><a href="{{ route('pruvodce.show', 'starobni-duchod') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Starobní důchod 2026</a></li>
                    <li><a href="{{ route('pruvodce.show', 'predcasny-duchod') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Předčasný důchod</a></li>
                    <li><a href="{{ route('pruvodce.show', 'penzijni-sporeni') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Penzijní spoření</a></li>
                    <li><a href="{{ route('pruvodce.show', 'duchodova-reforma-2025') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Důchodová reforma</a></li>
                    <li><a href="{{ route('magazin.index') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Celý magazín →</a></li>
                </ul>
            </div>

            {{-- Srovnávače & info --}}
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Srovnávače</h3>
                <ul class="space-y-2 text-sm" role="list">
                    <li><a href="{{ route('fondy.index') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Penzijní fondy</a></li>
                    <li><a href="{{ route('dip') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">DIP produkty</a></li>
                    <li><a href="{{ route('statistiky.index') }}" class="text-slate-400 hover:text-emerald-400 transition-colors">Statistiky důchodů</a></li>
                    <li><a href="#" class="text-slate-400 hover:text-emerald-400 transition-colors">Mapa důchodů ČR</a></li>
                </ul>
            </div>
        </div>

        {{-- Disclaimer + spodní lišta --}}
        <div class="mt-12 pt-8 border-t border-slate-800">
            <p class="text-xs text-slate-500 leading-relaxed mb-4 max-w-3xl">
                <strong class="text-slate-400">Důležité upozornění:</strong>
                Výpočty na tomto webu jsou orientační a slouží pouze pro informační účely. Nezohledňují všechny zákonné podmínky a individuální okolnosti. Přesný výpočet provede výhradně ČSSZ na základě podané žádosti. Informace jsou aktuální k datu poslední aktualizace.
            </p>
            <div class="flex flex-wrap gap-4 text-xs text-slate-500">
                <a href="#" class="hover:text-slate-400 transition-colors">Ochrana soukromí</a>
                <a href="#" class="hover:text-slate-400 transition-colors">Podmínky použití</a>
                <a href="#" class="hover:text-slate-400 transition-colors">Cookies</a>
                <a href="#" class="hover:text-slate-400 transition-colors">Kontakt</a>
                <span>Zdroje dat: ČSSZ, MPSV, ČSÚ</span>
            </div>
        </div>
    </div>
</footer>
