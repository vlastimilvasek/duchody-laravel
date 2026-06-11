@extends('layouts.app')

@section('content')
<x-content.guide
    title="Penzijní spoření 2026 — DPS vs. transformované fondy"
    perex="Státní příspěvek až 340 Kč měsíčně, daňový odpočet a příspěvky zaměstnavatele. Jak funguje doplňkové penzijní spoření a proč zvážit přechod ze starého transformovaného fondu."
>
    <h2>Dva systémy vedle sebe</h2>
    <p>
        V Česku existují dva typy soukromého penzijního spoření:
    </p>
    <ul>
        <li><strong>Transformované fondy</strong> — staré „penzijko" (penzijní připojištění) uzavírané do listopadu 2012. Garantují nezáporné zhodnocení, reálně ale vynášejí pod inflací.</li>
        <li><strong>Doplňkové penzijní spoření (DPS)</strong> — účastnické fondy s volbou strategie (konzervativní, vyvážená, dynamická) a vyšším výnosovým potenciálem.</li>
    </ul>

    <div class="not-prose border-l-4 border-blue-300 bg-blue-50 rounded-r-xl px-5 py-4 my-6">
        <p class="text-sm text-blue-800 leading-relaxed">
            <strong>Srovnání:</strong> Dynamické účastnické fondy vynesly za posledních 5 let
            průměrně 6–9 % ročně, transformované fondy 1–2 %. Na horizontu 20 let jde
            o rozdíl ve stovkách tisíc korun. Konkrétní čísla najdete ve
            <a href="{{ route('fondy.index') }}" class="underline font-semibold">srovnání fondů</a>.
        </p>
    </div>

    <h2>Státní příspěvek (od července 2024)</h2>
    <table>
        <thead>
            <tr><th>Měsíční úložka</th><th>Státní příspěvek</th></tr>
        </thead>
        <tbody>
            <tr><td>do 499 Kč</td><td>0 Kč</td></tr>
            <tr><td>500 Kč</td><td>100 Kč</td></tr>
            <tr><td>1 000 Kč</td><td>200 Kč</td></tr>
            <tr><td>1 700 Kč a více</td><td><strong>340 Kč (maximum)</strong></td></tr>
        </tbody>
    </table>
    <p>
        Příspěvek je 20 % z úložky mezi 500 a 1 700 Kč. K tomu lze vklady
        <strong>nad 1 700 Kč</strong> měsíčně odečíst od základu daně — až 48 000 Kč ročně,
        což při 15% dani znamená úsporu <strong>7 200 Kč ročně</strong>.
    </p>

    <h2>Jak vybrat strategii</h2>
    <ul>
        <li><strong>20+ let do důchodu</strong> — dynamický fond. Krátkodobé propady na dlouhém horizontu nevadí, rozdíl ve výnosech je zásadní.</li>
        <li><strong>10–20 let</strong> — dynamický nebo vyvážený fond podle tolerance rizika.</li>
        <li><strong>do 10 let</strong> — postupný přesun do vyvážené strategie.</li>
        <li><strong>do 5 let</strong> — konzervativní strategie, ochrana naspořeného před propadem trhů těsně před čerpáním.</li>
    </ul>

    <h2>Přechod z transformovaného fondu</h2>
    <p>
        Přechod do DPS je <strong>zdarma a bez ztráty</strong> dosavadních státních příspěvků.
        Jediné, o co přijdete, je garance nezáporného zhodnocení a u nejstarších smluv
        možnost výsluhové penze (výběr poloviny po 15 letech). Pro většinu střadatelů
        s horizontem nad 10 let se přechod vyplatí.
    </p>

    <div class="not-prose border-l-4 border-amber-400 bg-amber-50 rounded-r-xl px-5 py-4 my-6">
        <p class="text-sm text-amber-800 leading-relaxed">
            <strong>Pozor:</strong> Z transformovaného fondu do DPS lze přejít pouze v rámci
            stejné penzijní společnosti. Chcete-li jinou společnost, je nutné přejít do DPS
            a teprve potom (po 5 letech zdarma, dříve za max. 800 Kč) změnit společnost.
        </p>
    </div>

    <h2>Čerpání naspořených peněz</h2>
    <ul>
        <li><strong>Starobní penze</strong> — od 60 let při spoření alespoň 5 let (smlouvy od 2024: od 60 let a 10 letech spoření).</li>
        <li><strong>Jednorázové vyrovnání</strong> — výběr celé částky najednou; výnosy podléhají 15% dani, příspěvky zaměstnavatele také.</li>
        <li><strong>Penze na dobu určitou</strong> — při výplatě 10+ let je výběr osvobozen od daně z výnosů.</li>
        <li><strong>Předdůchod</strong> — čerpání před důchodovým věkem bez krácení státního důchodu (viz <a href="{{ route('pruvodce.show', 'predcasny-duchod') }}">průvodce předčasným důchodem</a>).</li>
    </ul>

    <div class="not-prose bg-slate-900 rounded-2xl px-6 py-5 my-8 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-white font-semibold">Který fond vybrat?</p>
            <p class="text-sm text-slate-400">Porovnejte výnosy a poplatky všech penzijních fondů v ČR.</p>
        </div>
        <a href="{{ route('fondy.index') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shrink-0">
            Srovnat fondy →
        </a>
    </div>
</x-content.guide>
@endsection
