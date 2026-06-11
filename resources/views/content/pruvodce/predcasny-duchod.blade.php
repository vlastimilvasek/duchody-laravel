@extends('layouts.app')

@section('content')
<x-content.guide
    title="Předčasný důchod 2026 — podmínky a krácení"
    perex="Do předčasného důchodu můžete odejít až 3 roky před řádným termínem. Krácení je ale trvalé. Spočítejte si, kolik vás předčasnost bude stát a kdy dává smysl."
>
    <h2>Podmínky předčasného důchodu</h2>
    <ul>
        <li>Odchod nejdříve <strong>3 roky</strong> před dosažením řádného důchodového věku.</li>
        <li>Získaných alespoň <strong>40 let doby pojištění</strong> (přísnější než u řádného důchodu).</li>
        <li>Po přiznání předčasného důchodu nelze až do řádného důchodového věku vykonávat výdělečnou činnost zakládající účast na pojištění.</li>
    </ul>

    <h2>Jak se počítá krácení</h2>
    <p>
        Procentní výměra se snižuje o <strong>1,5 % výpočtového základu za každých
        započatých 90 dní</strong> předčasnosti. Při 45 a více letech pojištění je krácení
        poloviční — 0,75 %. Krácení je <strong>trvalé</strong> a platí po celou dobu pobírání důchodu.
    </p>
    <table>
        <thead>
            <tr><th>Předčasnost</th><th>Krácení výp. základu</th><th>Orientační dopad*</th></tr>
        </thead>
        <tbody>
            <tr><td>90 dní</td><td>−1,5 %</td><td>cca −260 Kč/měs</td></tr>
            <tr><td>1 rok</td><td>−6,0 %</td><td>cca −1 030 Kč/měs</td></tr>
            <tr><td>2 roky</td><td>−12,0 %</td><td>cca −2 050 Kč/měs</td></tr>
            <tr><td>3 roky</td><td>−18,0 %</td><td>cca −3 080 Kč/měs</td></tr>
        </tbody>
    </table>
    <p><em>* při výpočtovém základu odpovídajícím průměrnému důchodu 21 400 Kč</em></p>

    <div class="not-prose border-l-4 border-amber-400 bg-amber-50 rounded-r-xl px-5 py-4 my-6">
        <p class="text-sm text-amber-800 leading-relaxed">
            <strong>Pozor:</strong> Předčasné důchody se do dosažení řádného důchodového věku
            valorizují omezeně — zvyšuje se pouze základní výměra, nikoli procentní.
            Reálná ztráta je tak v prvních letech ještě vyšší než samotné krácení.
        </p>
    </div>

    <h2>Kdy předčasný důchod dává smysl</h2>
    <ul>
        <li>Dlouhodobá nezaměstnanost před důchodem bez reálné šance na nové uplatnění.</li>
        <li>Zdravotní stav neumožňující práci, který ale nedosáhne na invalidní důchod.</li>
        <li>Máte 45+ let pojištění — krácení je poloviční.</li>
        <li>Statisticky: čím kratší předčasnost, tím spíše se vyplatí počkat.</li>
    </ul>

    <h2>Alternativy předčasného důchodu</h2>
    <h3>Předdůchod</h3>
    <p>
        Pokud máte dostatečné úspory v <a href="{{ route('pruvodce.show', 'penzijni-sporeni') }}">penzijním spoření</a>
        (alespoň 30 % průměrné mzdy měsíčně na min. 2 roky čerpání), můžete čerpat
        <strong>předdůchod</strong> — vlastní naspořené peníze. Státní důchod se vám nekrátí,
        stát za vás hradí zdravotní pojištění a doba se nepočítá jako vyloučená.
    </p>
    <h3>Podpora v nezaměstnanosti</h3>
    <p>
        Osoby nad 55 let mají nárok na podporu po dobu 11 měsíců. Evidence na úřadu práce
        se navíc částečně počítá do doby pojištění.
    </p>

    <div class="not-prose bg-slate-900 rounded-2xl px-6 py-5 my-8 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-white font-semibold">Spočítejte si dopad předčasného odchodu</p>
            <p class="text-sm text-slate-400">V kalkulačce zadejte dřívější datum odchodu — krácení započteme automaticky.</p>
        </div>
        <a href="{{ route('kalkulacka.vyse') }}" class="inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl transition-colors whitespace-nowrap shrink-0">
            Spočítat →
        </a>
    </div>

    <h2>Jak požádat</h2>
    <p>
        Postup je stejný jako u řádného důchodu — žádost na OSSZ nebo přes ePortál ČSSZ,
        nejdříve 4 měsíce před požadovaným datem. V žádosti výslovně uveďte, že žádáte
        o <strong>předčasný starobní důchod</strong>, a zvolte datum přiznání.
        Datum si dobře promyslete: každých započatých 90 dní znamená další krácení.
    </p>
</x-content.guide>
@endsection
