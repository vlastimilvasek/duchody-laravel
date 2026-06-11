<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

final class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'slug'     => 'valorizace-2026',
                'title'    => 'Valorizace důchodů 2026: Průměrný důchod vzroste o 358 Kč',
                'perex'    => 'Od ledna 2026 se důchody zvyšují v průměru o 358 Kč měsíčně. Základní výměra roste na 4 900 Kč. Podívejte se, kolik si polepšíte právě vy.',
                'category' => 'aktuality',
                'tags'     => ['valorizace', '2026', 'starobní důchod'],
                'content'  => <<<'MD'
## Co se mění od ledna 2026

Od 1. ledna 2026 se všechny vyplácené důchody zvyšují. **Základní výměra** roste
o 240 Kč na **4 900 Kč** měsíčně a **procentní výměra** se zvyšuje o 0,6 %.
Průměrný starobní důchod tak vzroste přibližně o **358 Kč na 21 400 Kč** měsíčně.

[info]Valorizace probíhá automaticky — o nic nemusíte žádat. Zvýšenou částku
dostanete poprvé v lednové výplatě důchodu.[/info]

## Jak se valorizace počítá

Zákonný vzorec valorizace vychází ze dvou ukazatelů:

- **růst spotřebitelských cen** (inflace) — započítává se celý,
- **růst reálných mezd** — započítává se jedna třetina.

Základní výměra je vždy stanovena jako 10 % průměrné mzdy. Procentní výměra
se zvyšuje tak, aby celkový růst odpovídal valorizačnímu vzorci.

## Kolik si polepšíte vy

Konkrétní částka závisí na výši vaší procentní výměry. Orientačně:

| Důchod před valorizací | Důchod po valorizaci | Navýšení |
|------------------------|----------------------|----------|
| 15 000 Kč | 15 301 Kč | +301 Kč |
| 18 000 Kč | 18 319 Kč | +319 Kč |
| 21 400 Kč | 21 758 Kč | +358 Kč |
| 25 000 Kč | 25 380 Kč | +380 Kč |

[kalkulacka]

## Mimořádná valorizace

Pokud inflace od poslední valorizace přesáhne 5 %, zákon ukládá provést
mimořádnou valorizaci. Vzhledem k aktuálnímu vývoji cen se pro rok 2026
mimořádná valorizace nepředpokládá.

[warning]Pozor na zaměňování valorizace s přepočtem důchodu. Valorizace zvyšuje
již přiznané důchody, nemění však výpočtový základ nově přiznávaných důchodů.[/warning]
MD,
            ],
            [
                'slug'     => 'duchodova-reforma-prehled',
                'title'    => 'Důchodová reforma: Co se mění a koho se týká',
                'perex'    => 'Postupné zvyšování důchodového věku, pomalejší růst nových důchodů a změny předčasných důchodů. Přehled všeho, co reforma přináší.',
                'category' => 'reforma',
                'tags'     => ['reforma', 'důchodový věk', 'legislativa'],
                'content'  => <<<'MD'
## Hlavní pilíře reformy

Důchodová reforma schválená v roce 2024 přináší postupné změny, které se dotknou
především ročníků narozených po roce 1965.

### 1. Důchodový věk poroste nad 65 let

Pro ročníky narozené **po roce 1965** se důchodový věk postupně zvyšuje podle
růstu doby dožití — o jeden měsíc za každý ročník, s cílovou hranicí 67 let
pro ročníky 1989 a mladší.

### 2. Nižší procentní výměra u nových důchodů

Od roku 2026 se procentní sazba za rok pojištění postupně snižuje z 1,5 %
na 1,45 % výpočtového základu. Tempo růstu nových důchodů se tak zpomalí.

[info]Změna se týká pouze nově přiznávaných důchodů. Již vyplácené důchody
se nesnižují.[/info]

### 3. Přísnější předčasné důchody

Předčasný důchod je nově možný maximálně **3 roky** před řádným důchodovým
věkem a krácení je trvalé. Výjimku mají náročné profese.

### 4. Výchovné zůstává

Bonus 500 Kč měsíčně za každé vychované dítě reforma zachovává, od roku 2027
se však bude valorizovat pouze základní částka.

## Koho se reforma nedotkne

Pokud jste narozeni **před rokem 1966** nebo již pobíráte důchod, reforma se
vás prakticky nedotýká. Důchodový věk i pravidla výpočtu zůstávají stejná.

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'penzijni-sporeni-statni-prispevek-2026',
                'title'    => 'Penzijní spoření 2026: Jak získat maximální státní příspěvek',
                'perex'    => 'Státní příspěvek až 340 Kč měsíčně dostanete při úložce 1 700 Kč. Spolu s daňovým odpočtem můžete od státu získat přes 7 000 Kč ročně.',
                'category' => 'investice',
                'tags'     => ['penzijní spoření', 'DPS', 'státní příspěvek'],
                'content'  => <<<'MD'
## Nová pravidla státního příspěvku

Od července 2024 platí nová pásma státního příspěvku u doplňkového penzijního
spoření (DPS). Příspěvek získáte při měsíční úložce **od 500 Kč**, maximum
čerpáte při úložce **1 700 Kč a více**.

| Vaše úložka | Státní příspěvek | Efektivní zhodnocení |
|-------------|------------------|----------------------|
| 500 Kč | 100 Kč | +20 % |
| 1 000 Kč | 200 Kč | +20 % |
| 1 500 Kč | 300 Kč | +20 % |
| 1 700 Kč | 340 Kč | +20 % |

## Daňový odpočet

Vklady **nad 1 700 Kč** měsíčně si můžete odečíst od základu daně —
až 48 000 Kč ročně. Při 15% sazbě daně tak ušetříte dalších **7 200 Kč ročně**.

[info]Maximální státní podpora: 4 080 Kč na příspěvcích + 7 200 Kč na daních
= **11 280 Kč ročně**. K tomu případný příspěvek zaměstnavatele.[/info]

## Vyplatí se dynamický fond?

Čím delší máte horizont do důchodu, tím více dává smysl dynamická strategie.
Historicky dynamické fondy překonávají konzervativní o 3–5 procentních bodů
ročně, krátkodobě však mohou kolísat.

[warning]Pět let před plánovaným čerpáním zvažte postupný přesun do
konzervativnější strategie — chráníte tím naspořené prostředky před
propadem trhů těsně před výběrem.[/warning]

Porovnejte výnosy a poplatky všech fondů v našem
[srovnávači penzijních fondů](/fondy).
MD,
            ],
            [
                'slug'     => 'informativni-vypis-cssz-navod',
                'title'    => 'Informativní list důchodového pojištění: Návod krok za krokem',
                'perex'    => 'Zkontrolujte si odpracované roky dřív, než bude pozdě. Chybějící doby pojištění můžete doložit zpětně — ukážeme vám jak.',
                'category' => 'rady',
                'tags'     => ['ČSSZ', 'doba pojištění', 'návod'],
                'content'  => <<<'MD'
## Proč si výpis zkontrolovat

Informativní osobní list důchodového pojištění (IOLDP) obsahuje všechny doby
pojištění a vyměřovací základy, které o vás ČSSZ eviduje. **Chyby v evidenci
jsou překvapivě časté** — zejména u zaměstnání z 90. let.

[warning]Každý chybějící rok pojištění vás může připravit o stovky korun
měsíčně. Při průměrné mzdě představuje jeden rok pojištění zhruba
440 Kč měsíčního důchodu.[/warning]

## Jak výpis získat

### Online přes ePortál ČSSZ (doporučujeme)

1. Přihlaste se na [eportal.cssz.cz](https://eportal.cssz.cz) pomocí
   bankovní identity, datové schránky nebo eObčanky.
2. Zvolte službu **„Informativní osobní list důchodového pojištění"**.
3. Výpis se vygeneruje okamžitě ve formátu PDF.

### Písemně

Žádost můžete poslat poštou na ústředí ČSSZ. Výpis obdržíte do 90 dnů,
službu lze využít jednou ročně zdarma.

## Co ve výpisu kontrolovat

- **Doby pojištění** — souhlasí roky u všech zaměstnavatelů?
- **Vyměřovací základy** — odpovídají vašim hrubým mzdám?
- **Náhradní doby** — studium, vojna, rodičovská, evidence na ÚP.

## Jak doložit chybějící doby

Chybějící zaměstnání doložíte zápočtovým listem, pracovní smlouvou,
mzdovými listy nebo potvrzením zaměstnavatele. Pokud firma zanikla,
obraťte se na příslušný archiv.

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'prumerny-duchod-kraje-2026',
                'title'    => 'Průměrný důchod podle krajů: Kde berou nejvíc a kde nejmíň',
                'perex'    => 'Mezi Prahou a Karlovarským krajem je rozdíl přes 2 600 Kč měsíčně. Podívejte se na kompletní data za všechny kraje.',
                'category' => 'data',
                'tags'     => ['statistiky', 'kraje', 'průměrný důchod'],
                'content'  => <<<'MD'
## Praha vede, pohraničí zaostává

Průměrný starobní důchod v ČR činí v roce 2026 **21 400 Kč**. Regionální
rozdíly však zůstávají výrazné — odrážejí především rozdílné mzdové hladiny
v produktivním věku dnešních důchodců.

### Kraje s nejvyšším průměrným důchodem

1. **Hlavní město Praha** — 22 890 Kč
2. **Středočeský kraj** — 21 850 Kč
3. **Jihomoravský kraj** — 21 390 Kč

### Kraje s nejnižším průměrným důchodem

1. **Karlovarský kraj** — 20 280 Kč
2. **Ústecký kraj** — 20 460 Kč
3. **Zlínský kraj** — 20 680 Kč

[info]Rozdíl mezi nejvyšším a nejnižším krajským průměrem je 2 610 Kč
měsíčně, tedy přes 31 000 Kč ročně.[/info]

## Proč rozdíly vznikají

Výše důchodu se odvíjí od příjmů v celé kariéře. Vyšší mzdy v Praze
a okolí se tak s desetiletým zpožděním promítají do vyšších důchodů.
Roli hraje i struktura ekonomiky — průmyslové regiony s historicky
vysokými mzdami (Moravskoslezský kraj) si drží nadprůměrné důchody
navzdory nižším současným mzdám.

Podrobná data všech krajů včetně vývoje najdete v našich
[statistikách důchodů](/statistiky).
MD,
            ],
            [
                'slug'     => 'predcasny-duchod-kdy-se-vyplati',
                'title'    => 'Předčasný důchod: Kdy se vyplatí a kolik vás stojí',
                'perex'    => 'Za každých 90 dní předčasnosti přijdete o 1,5 % výpočtového základu — trvale. Spočítali jsme, kdy předčasný odchod dává smysl.',
                'category' => 'rady',
                'tags'     => ['předčasný důchod', 'krácení', 'strategie'],
                'content'  => <<<'MD'
## Pravidla předčasného důchodu

Do předčasného důchodu můžete odejít nejdříve **3 roky** před dosažením
řádného důchodového věku. Podmínkou je získání alespoň **40 let** doby
pojištění.

## Kolik předčasnost stojí

Krácení činí **1,5 % výpočtového základu za každých započatých 90 dní**
před řádným termínem. Při 45 a více letech pojištění je krácení poloviční
(0,75 %).

| Předčasnost | Krácení | Ztráta při důchodu 21 400 Kč |
|-------------|---------|-------------------------------|
| 6 měsíců | 3,0 % | cca −510 Kč/měs |
| 1 rok | 6,0 % | cca −1 030 Kč/měs |
| 2 roky | 12,0 % | cca −2 050 Kč/měs |
| 3 roky | 18,0 % | cca −3 080 Kč/měs |

[warning]Krácení je **trvalé** — snížená procentní výměra vám zůstane
po celou dobu pobírání důchodu. Předčasné důchody se navíc do dosažení
řádného důchodového věku nevalorizují v plné výši.[/warning]

## Kdy předčasný důchod dává smysl

- Dlouhodobá nezaměstnanost těsně před důchodem bez šance na uplatnění.
- Zdravotní omezení, které nedosahuje na invalidní důchod.
- Máte 45+ let pojištění (poloviční krácení).

## Alternativa: předdůchod

Pokud máte naspořeno v penzijním spoření, zvažte **předdůchod** — čerpáte
vlastní úspory, státní důchod se vám nekrátí a stát za vás platí zdravotní
pojištění.

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'vychovne-500-kc-za-dite',
                'title'    => 'Výchovné: 500 Kč k důchodu za každé vychované dítě',
                'perex'    => 'Výchovné automaticky zvyšuje důchod především ženám. Jak funguje, kdo na něj má nárok a co se změní od roku 2027.',
                'category' => 'aktuality',
                'tags'     => ['výchovné', 'děti', 'ženy'],
                'content'  => <<<'MD'
## Co je výchovné

Výchovné je bonus **500 Kč měsíčně za každé vychované dítě**, který se
připočítává k procentní výměře starobního důchodu. Zavedeno bylo v roce 2023
a vzniklo jako kompenzace nižších důchodů žen.

## Kdo má nárok

Nárok má osoba, která **osobně pečovala** o dítě v největším rozsahu —
typicky matka. U každého dítěte může výchovné čerpat pouze jedna osoba.

[info]U nově přiznávaných důchodů se výchovné přiznává automaticky na
základě údajů v žádosti o důchod. Nemusíte o něj zvlášť žádat.[/info]

## Příklady

- Matka dvou dětí: **+1 000 Kč** měsíčně
- Matka tří dětí: **+1 500 Kč** měsíčně

## Změny od roku 2027

Důchodová reforma výchovné zachovává, mění však jeho valorizaci — částka
500 Kč se nadále nebude zvyšovat procentní valorizací, ale zůstane fixní
s možností vládního navýšení.

[warning]Výchovné nezvyšuje pozůstalostní důchody a nezapočítává se do
vyměřovacího základu pro jejich výpočet.[/warning]
MD,
            ],
            [
                'slug'     => 'minimalni-duchod-2026',
                'title'    => 'Minimální důchod 2026: Kolik činí a kdo na něj dosáhne',
                'perex'    => 'Od roku 2026 platí nová konstrukce minimálního důchodu — 20 % průměrné mzdy. Komu stát důchod dorovná a jak o dorovnání požádat.',
                'category' => 'rady',
                'tags'     => ['minimální důchod', 'nízké důchody'],
                'content'  => <<<'MD'
## Nová konstrukce od roku 2026

Důchodová reforma zavádí minimální důchod ve výši **20 % průměrné mzdy** —
pro rok 2026 to znamená přibližně **9 770 Kč** měsíčně. Skládá se ze základní
výměry (4 900 Kč) a dorovnání procentní výměry.

## Kdo na minimální důchod dosáhne

Minimální důchod náleží každému, kdo splnil podmínky nároku na starobní
důchod — tedy dosáhl důchodového věku a získal alespoň **35 let** doby
pojištění. Týká se především:

- osob s velmi nízkými celoživotními příjmy,
- dlouhodobě pečujících osob,
- OSVČ, které dlouhodobě odváděly minimální zálohy.

[info]Dorovnání na minimální důchod probíhá automaticky při výpočtu —
o nic zvlášť žádat nemusíte.[/info]

## Pozor na předčasný důchod

U předčasných důchodů se minimální výše negarantuje v plném rozsahu —
krácení za předčasnost se uplatní i pod hranicí minimálního důchodu.

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'prace-v-duchodu-pravidla',
                'title'    => 'Práce v důchodu: Pravidla, daně a sleva na pojistném',
                'perex'    => 'Starobní důchod a práci lze kombinovat bez omezení. Pracující důchodci navíc od roku 2024 neplatí důchodové pojištění — to je 6,5 % k dobru.',
                'category' => 'rady',
                'tags'     => ['práce v důchodu', 'daně', 'pracující důchodci'],
                'content'  => <<<'MD'
## Žádná omezení výdělku

Po dosažení řádného důchodového věku můžete pobírat důchod a zároveň
**neomezeně pracovat** — na smlouvu, DPP, DPČ i jako OSVČ. Výdělek nemá
na výplatu důchodu žádný vliv.

## Sleva na pojistném 6,5 %

Od roku 2024 pracující starobní důchodci **neplatí pojistné na důchodové
pojištění** (6,5 % z hrubé mzdy). Při mzdě 30 000 Kč hrubého tak měsíčně
ušetříte 1 950 Kč.

[info]Slevu uplatňuje zaměstnavatel automaticky — stačí mu doložit
přiznání starobního důchodu.[/info]

## Daně a sleva na poplatníka

Pracující důchodce má nárok na **základní slevu na poplatníka**
(30 840 Kč ročně) stejně jako ostatní zaměstnanci. Důchod do výše
36násobku minimální mzdy ročně je od daně osvobozen.

## Práce při předčasném důchodu

Jiná situace platí u předčasného důchodu: do dosažení řádného důchodového
věku **nesmíte** vykonávat činnost zakládající účast na pojištění.
Povolené jsou jen omezené výdělky — DPP do 11 500 Kč měsíčně (2026)
nebo zaměstnání malého rozsahu do 4 999 Kč.

[warning]Porušení pravidel u předčasného důchodu znamená zastavení výplaty
důchodu a povinnost vrátit neoprávněně vyplacené částky.[/warning]
MD,
            ],
            [
                'slug'     => 'invalidni-duchod-stupne',
                'title'    => 'Invalidní důchod: Tři stupně, posudky a výpočet',
                'perex'    => 'Invalidní důchod má tři stupně podle poklesu pracovní schopnosti. Jak probíhá posouzení, kolik důchod činí a co se stane v důchodovém věku.',
                'category' => 'rady',
                'tags'     => ['invalidní důchod', 'posudkový lékař'],
                'content'  => <<<'MD'
## Tři stupně invalidity

Stupeň invalidity určuje posudkový lékař ČSSZ podle poklesu pracovní schopnosti:

| Stupeň | Pokles pracovní schopnosti | Koeficient výpočtu |
|--------|---------------------------|--------------------|
| I. stupeň | 35–49 % | 0,5 % za rok pojištění |
| II. stupeň | 50–69 % | 0,75 % za rok pojištění |
| III. stupeň | 70 % a více | 1,5 % za rok pojištění |

## Podmínky nároku

Kromě zdravotního stavu musíte získat potřebnou **dobu pojištění** —
u osob nad 28 let je to 5 let z posledních 10 let. U invalidity následkem
pracovního úrazu se doba pojištění nevyžaduje.

[info]Do doby pojištění se počítá i tzv. dopočtená doba — období od vzniku
invalidity do dosažení důchodového věku. Proto i mladí lidé mohou dostat
plnohodnotný invalidní důchod.[/info]

## Průměrné výše (2026)

- III. stupeň: cca **13 300 Kč**
- II. stupeň: cca **10 000 Kč**
- I. stupeň: cca **8 800 Kč**

## Co se stane v 65 letech

Dosažením 65 let se invalidní důchod automaticky **mění na starobní**
ve stejné výši. Můžete ale požádat o klasický výpočet starobního důchodu —
ČSSZ přizná vyšší z obou částek.

[warning]Na invalidní důchod I. a II. stupně se nevztahuje minimální důchod.
Při nízkých příjmech proto zvažte souběh s dávkami státní sociální podpory.[/warning]
MD,
            ],
            [
                'slug'     => 'vdovsky-vdovecky-duchod',
                'title'    => 'Vdovský a vdovecký důchod: Podmínky, výše a souběh',
                'perex'    => 'Pozůstalostní důchod náleží rok po úmrtí partnera, za určitých podmínek déle. Jak se počítá a jak funguje souběh s vlastním důchodem.',
                'category' => 'rady',
                'tags'     => ['vdovský důchod', 'pozůstalostní důchody'],
                'content'  => <<<'MD'
## Komu důchod náleží

Vdovský (vdovecký) důchod náleží po zemřelém manželovi či manželce —
**nikoli po druhovi nebo partnerovi**. Podmínkou je, že zemřelý pobíral
důchod, nebo splnil podmínky nároku na něj.

## Jak dlouho se vyplácí

Standardně **1 rok** od úmrtí. Déle jen pokud pozůstalý:

- pečuje o nezaopatřené dítě nebo závislou osobu,
- je invalidní ve III. stupni,
- dosáhl věku alespoň o 4 roky nižšího než důchodový věk muže stejného ročníku.

[info]Nárok obnovíte, pokud některou podmínku splníte do 2 let od zániku
předchozího nároku.[/info]

## Výše a souběh s vlastním důchodem

Vdovský důchod činí **50 % procentní výměry** důchodu zemřelého plus
základní výměra. Při souběhu s vlastním důchodem dostanete:

- vyšší důchod **v plné výši**,
- z nižšího důchodu **polovinu procentní výměry**,
- základní výměru jen jednou.

Průměrný vdovský důchod v souběhu činí v roce 2026 přibližně **14 760 Kč**.

[warning]Nový sňatek znamená zánik nároku na vdovský důchod — bez kompenzace.[/warning]

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'duchod-osvc-zalohy',
                'title'    => 'Důchod OSVČ: Proč bývá nízký a jak ho zvýšit',
                'perex'    => 'Podnikatelé s minimálními zálohami míří k důchodu kolem 14 000 Kč. Ukážeme, jak vyměřovací základ ovlivňuje důchod a co s tím.',
                'category' => 'data',
                'tags'     => ['OSVČ', 'zálohy', 'vyměřovací základ'],
                'content'  => <<<'MD'
## Proč mají OSVČ nízké důchody

Důchod se počítá z **vyměřovacího základu** — u OSVČ je to polovina
daňového základu (od 2024 se postupně zvyšuje na 55 %). Kdo platí
minimální zálohy, tomu se do důchodu počítá jen minimální vyměřovací
základ — v roce 2026 zhruba **15 000 Kč měsíčně**, bez ohledu na
skutečné příjmy.

| Měsíční vyměřovací základ | Orientační důchod (40 let pojištění) |
|---------------------------|--------------------------------------|
| 15 000 Kč (minimum) | cca 14 200 Kč |
| 25 000 Kč | cca 16 100 Kč |
| 48 800 Kč (průměrná mzda) | cca 20 800 Kč |

[warning]Paušální daň v 1. pásmu odpovídá jen mírně nadminimálnímu
vyměřovacímu základu. Pohodlnost paušálu se ve stáří promítne do
nízkého důchodu.[/warning]

## Jak si OSVČ zvýší důchod

1. **Vyšší zálohy** — dobrovolně si můžete určit vyšší vyměřovací základ. Každých 5 000 Kč základu navíc znamená zhruba +950 Kč důchodu měsíčně (při 40 letech pojištění).
2. **Penzijní spoření** — 1 700 Kč měsíčně se státním příspěvkem 340 Kč.
3. **DIP** — daňový odpočet až 48 000 Kč ročně se u OSVČ počítá z daně z příjmů stejně jako u zaměstnanců.

[info]Zvýšení záloh „zpětně" nefunguje — vyměřovací základ lze navýšit
jen do podání přehledu za daný rok. Čím dříve začnete, tím lépe.[/info]

[kalkulacka]
MD,
            ],
            [
                'slug'     => 'prispevek-zamestnavatele-penzijko',
                'title'    => 'Příspěvek zaměstnavatele na penzijko: Benefit, který se vyplatí oběma',
                'perex'    => 'Až 50 000 Kč ročně od zaměstnavatele bez daní a odvodů. Jak benefit funguje, proč je výhodnější než zvýšení mzdy a jak o něj požádat.',
                'category' => 'investice',
                'tags'     => ['příspěvek zaměstnavatele', 'benefity', 'DPS'],
                'content'  => <<<'MD'
## Jak benefit funguje

Zaměstnavatel může přispívat na vaše doplňkové penzijní spoření nebo DIP
až **50 000 Kč ročně** — z příspěvku se neplatí daň z příjmů ani sociální
a zdravotní pojištění. Ani na vaší, ani na jeho straně.

## Proč je lepší než zvýšení mzdy

Z 1 000 Kč hrubého navýšení mzdy vám po odvodech zbude zhruba 770 Kč
a zaměstnavatele stojí 1 338 Kč. Příspěvek 1 000 Kč na penzijko:

| | Zvýšení mzdy o 1 000 Kč | Příspěvek 1 000 Kč |
|---|------------------------|---------------------|
| Náklad zaměstnavatele | 1 338 Kč | 1 000 Kč |
| Vy reálně získáte | cca 770 Kč | 1 000 Kč |
| Efektivita | 58 % | 100 % |

[info]Příspěvek zaměstnavatele se nepočítá do limitu pro státní příspěvek —
ten dostáváte ze svých vlastních úložek. Oba benefity se sčítají.[/info]

## Na co si dát pozor

- Příspěvek zaměstnavatele podléhá při **jednorázovém výběru** 15% dani.
- Při výplatě penzí na 10+ let je výběr od daně osvobozen.
- Benefit nabízí zhruba dvě třetiny větších firem — pokud ho váš
  zaměstnavatel nenabízí, zeptejte se. Pro firmu je levnější než bonus.

Vyberte si fond s nízkými poplatky a dobrou výkonností v našem
[srovnávači penzijních fondů](/fondy).
MD,
            ],
        ];

        foreach ($articles as $i => $data) {
            Article::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title'            => $data['title'],
                    'perex'            => $data['perex'],
                    'content_markdown' => $data['content'],
                    'category'         => $data['category'],
                    'tags'             => $data['tags'],
                    'published_at'     => now()->subDays(3 + $i * 9),
                    'seo_title'        => $data['title'] . ' | Důchody.cz',
                    'seo_description'  => mb_substr($data['perex'], 0, 160),
                ],
            );
        }
    }
}
