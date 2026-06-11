@extends('layouts.app')

@section('content')
<x-content.guide
    title="Důchodová reforma — co se mění a koho se týká"
    perex="Vyšší důchodový věk, pomalejší růst nově přiznávaných důchodů a přísnější předčasné důchody. Přehled schválených změn a koho se reálně dotknou."
    badge="Stav k roku 2026"
>
    <h2>Proč reforma vznikla</h2>
    <p>
        Poměr pracujících a důchodců se dlouhodobě zhoršuje — zatímco dnes připadají na jednoho
        důchodce zhruba dva plátci pojistného, kolem roku 2050 to bude přibližně 1,5. Bez úprav
        by deficit důchodového účtu narostl na neudržitelné úrovně. Reforma schválená v roce 2024
        proto postupně upravuje věk odchodu i tempo růstu nových důchodů.
    </p>

    <h2>1. Důchodový věk poroste podle doby dožití</h2>
    <p>
        Pro ročníky narozené <strong>po roce 1965</strong> se důchodový věk posouvá o jeden měsíc
        za každý ročník — navázán je na růst doby dožití. Cílová hranice je <strong>67 let</strong>
        pro ročníky 1989 a mladší.
    </p>
    <table>
        <thead>
            <tr><th>Ročník narození</th><th>Důchodový věk (orientačně)</th></tr>
        </thead>
        <tbody>
            <tr><td>1965</td><td>65 let</td></tr>
            <tr><td>1970</td><td>65 let 8 měsíců</td></tr>
            <tr><td>1975</td><td>66 let</td></tr>
            <tr><td>1980</td><td>66 let 5 měsíců</td></tr>
            <tr><td>1989+</td><td>67 let</td></tr>
        </tbody>
    </table>
    <p>
        Přesný věk pro váš ročník zjistíte v <a href="{{ route('kalkulacka.vek') }}">kalkulačce důchodového věku</a>
        nebo na stránce vašeho <a href="{{ route('duchod.rocnik', 1965) }}">ročníku</a>.
    </p>

    <h2>2. Pomalejší růst nových důchodů</h2>
    <p>
        Procentní sazba za rok pojištění se od roku 2026 postupně snižuje z 1,5 %
        na <strong>1,45 %</strong> výpočtového základu (o 0,005 p. b. ročně). Nově přiznávané
        důchody tak porostou pomaleji než dosud.
    </p>

    <div class="not-prose border-l-4 border-blue-300 bg-blue-50 rounded-r-xl px-5 py-4 my-6">
        <p class="text-sm text-blue-800 leading-relaxed">
            <strong>Důležité:</strong> Změna se týká výhradně nově přiznávaných důchodů.
            Již vyplácené důchody se nesnižují a dál se valorizují podle zákonného vzorce.
        </p>
    </div>

    <h2>3. Přísnější předčasné důchody</h2>
    <ul>
        <li>Odchod nejdříve <strong>3 roky</strong> před řádným věkem (dříve až 5 let).</li>
        <li>Krácení 1,5 % za 90 dní je <strong>trvalé</strong>.</li>
        <li>Do řádného důchodového věku se valorizuje jen základní výměra.</li>
        <li>Podmínka <strong>40 let</strong> doby pojištění.</li>
    </ul>
    <p>Podrobnosti v <a href="{{ route('pruvodce.show', 'predcasny-duchod') }}">průvodci předčasným důchodem</a>.</p>

    <h2>4. Náročné profese</h2>
    <p>
        Zaměstnanci v náročných profesích (kategorizace dle rizikovosti práce) získávají
        možnost odejít do důchodu dříve bez krácení — za každých 10 odpracovaných let
        v náročné profesi až o 15 měsíců dříve. Zaměstnavatelé za ně odvádějí vyšší pojistné.
    </p>

    <h2>5. Co reforma zachovává</h2>
    <ul>
        <li><strong>Výchovné</strong> 500 Kč za dítě zůstává (od 2027 fixní, bez procentní valorizace).</li>
        <li><strong>Valorizační vzorec</strong> — inflace + třetina růstu reálných mezd.</li>
        <li><strong>Minimální důchod</strong> se nově zvyšuje na 20 % průměrné mzdy.</li>
        <li>Sleva na pojistném pro <strong>pracující důchodce</strong> (6,5 %).</li>
    </ul>

    <h2>Koho se reforma nedotkne</h2>
    <p>
        Pokud jste narozeni <strong>před rokem 1966</strong> nebo již důchod pobíráte,
        reforma se vás prakticky nedotýká — důchodový věk i pravidla výpočtu zůstávají
        beze změny.
    </p>

    <div class="not-prose bg-slate-900 rounded-2xl px-6 py-5 my-8 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-white font-semibold">Jak se vás reforma dotkne?</p>
            <p class="text-sm text-slate-400">Spočítejte si důchodový věk i orientační výši důchodu podle aktuálních pravidel.</p>
        </div>
        <a href="{{ route('kalkulacka.vek') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shrink-0">
            Spočítat →
        </a>
    </div>
</x-content.guide>
@endsection
