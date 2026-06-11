@extends('layouts.app')

@section('content')
<x-content.guide
    title="Starobní důchod 2026 — kompletní průvodce"
    perex="Kdo má nárok na starobní důchod, z čeho se počítá, jak o něj požádat a na co si dát pozor. Vše podle parametrů platných pro rok 2026."
>
    <h2>Podmínky nároku</h2>
    <p>
        Nárok na starobní důchod vzniká splněním dvou podmínek současně: dosažením
        <strong>důchodového věku</strong> a získáním potřebné <strong>doby pojištění</strong> —
        ta činí <strong>35 let</strong> (včetně náhradních dob), případně 30 let čistě
        odpracovaných bez náhradních dob.
    </p>
    <p>
        Důchodový věk závisí na ročníku narození, u žen narozených do roku 1965 také na počtu
        vychovaných dětí. Pro ročníky 1989 a mladší platí jednotná hranice 67 let.
        Přesný věk zjistíte v <a href="{{ route('kalkulacka.vek') }}">kalkulačce důchodového věku</a>.
    </p>

    <h2>Z čeho se důchod skládá</h2>
    <ul>
        <li><strong>Základní výměra</strong> — stejná pro všechny, v roce 2026 činí <strong>4 900 Kč</strong> měsíčně (10 % průměrné mzdy).</li>
        <li><strong>Procentní výměra</strong> — za každý rok pojištění <strong>1,495 %</strong> výpočtového základu; minimálně 770 Kč.</li>
        <li><strong>Výchovné</strong> — <strong>500 Kč</strong> měsíčně za každé vychované dítě.</li>
    </ul>

    <h2>Jak se počítá výpočtový základ</h2>
    <p>
        Vychází se z příjmů od roku 1986 (tzv. rozhodné období). Dřívější příjmy se přepočítají
        koeficienty mzdového růstu a z průměru vznikne <strong>osobní vyměřovací základ (OVZ)</strong>.
        Ten se následně redukuje:
    </p>
    <table>
        <thead>
            <tr><th>Pásmo OVZ (2026)</th><th>Započítává se</th></tr>
        </thead>
        <tbody>
            <tr><td>do 16 306 Kč</td><td>99 %</td></tr>
            <tr><td>16 306 – 48 833 Kč</td><td>26 %</td></tr>
            <tr><td>48 833 – 130 221 Kč</td><td>22 %</td></tr>
            <tr><td>nad 130 221 Kč</td><td>0 %</td></tr>
        </tbody>
    </table>
    <p>
        Díky redukci je systém solidární — vyšší příjmy se promítají do důchodu jen omezeně.
        Orientační výši svého důchodu spočítáte v <a href="{{ route('kalkulacka.vyse') }}">kalkulačce výše důchodu</a>.
    </p>

    <div class="not-prose bg-slate-900 rounded-2xl px-6 py-5 my-8 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-white font-semibold">Kolik budete mít důchod?</p>
            <p class="text-sm text-slate-400">Orientační výpočet podle parametrů 2026 — zdarma, do 2 minut.</p>
        </div>
        <a href="{{ route('kalkulacka.vyse') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shrink-0">
            Spočítat důchod →
        </a>
    </div>

    <h2>Jak požádat o důchod</h2>
    <ol>
        <li><strong>Zkontrolujte evidenci</strong> — s předstihem (ideálně 1–2 roky) si vyžádejte Informativní osobní list důchodového pojištění na ePortálu ČSSZ a doložte případné chybějící doby.</li>
        <li><strong>Podejte žádost</strong> — nejdříve 4 měsíce před požadovaným datem přiznání, online přes ePortál ČSSZ nebo osobně na OSSZ podle místa bydliště.</li>
        <li><strong>Doložte podklady</strong> — doklad totožnosti, doklady o studiu a vojně, rodné listy dětí (kvůli výchovnému), číslo účtu pro výplatu.</li>
        <li><strong>Vyčkejte na rozhodnutí</strong> — ČSSZ rozhoduje zpravidla do 90 dnů; důchod je vyplacen zpětně od data přiznání.</li>
    </ol>

    <div class="not-prose border-l-4 border-blue-300 bg-blue-50 rounded-r-xl px-5 py-4 my-6">
        <p class="text-sm text-blue-800 leading-relaxed">
            <strong>Tip:</strong> Datum přiznání důchodu si můžete zvolit — nemusí to být přesně den
            dosažení důchodového věku. Pozdější odchod zvyšuje důchod o 1,5 % výpočtového základu
            za každých 90 dní přesluhování.
        </p>
    </div>

    <h2>Práce při důchodu</h2>
    <p>
        Pobírání starobního důchodu lze bez omezení kombinovat s prací i podnikáním.
        Od roku 2024 navíc pracující důchodci neplatí pojistné na důchodové pojištění
        (sleva 6,5 %), výdělek se jim ale už nepromítá do zvýšení důchodu.
    </p>

    <h2>Časté chyby</h2>
    <ul>
        <li>Nezkontrolovaná evidence dob pojištění — chybějící roky výrazně snižují důchod.</li>
        <li>Žádost o předčasný důchod bez propočtu trvalého krácení — viz <a href="{{ route('pruvodce.show', 'predcasny-duchod') }}">průvodce předčasným důchodem</a>.</li>
        <li>Zapomenuté náhradní doby — studium před rokem 2010, vojna, péče o děti či blízké.</li>
        <li>Nevyužité výchovné — nárok je třeba uvést v žádosti.</li>
    </ul>
</x-content.guide>
@endsection
