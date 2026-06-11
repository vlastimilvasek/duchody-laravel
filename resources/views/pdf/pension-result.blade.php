<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Výpočet důchodu — Důchody.cz</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #334155; line-height: 1.6; padding: 36px; }
        .header { border-bottom: 2px solid #059669; padding-bottom: 14px; margin-bottom: 24px; }
        .logo { font-size: 18px; font-weight: bold; color: #0f172a; }
        .logo span { color: #059669; }
        .subtitle { font-size: 10px; color: #64748b; margin-top: 2px; }
        h1 { font-size: 16px; color: #0f172a; margin-bottom: 16px; }
        h2 { font-size: 12px; color: #0f172a; margin: 20px 0 8px; }
        .total-box { background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 16px; text-align: center; margin-bottom: 20px; }
        .total-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #047857; }
        .total-value { font-size: 26px; font-weight: bold; color: #0f172a; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { text-align: left; padding: 7px 10px; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; }
        td.num { text-align: right; font-weight: bold; color: #0f172a; }
        .row-total td { border-top: 2px solid #0f172a; border-bottom: none; font-weight: bold; color: #0f172a; }
        .disclaimer { margin-top: 28px; background: #fffbeb; border-left: 3px solid #f59e0b; padding: 10px 12px; font-size: 9px; color: #92400e; }
        .footer { margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 9px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">důchody<span>.cz</span></div>
        <div class="subtitle">Orientační výpočet starobního důchodu · vygenerováno {{ $generatedAt->format('j. n. Y H:i') }}</div>
    </div>

    <h1>Výsledek výpočtu důchodu</h1>

    <div class="total-box">
        <div class="total-label">Odhadovaný měsíční důchod</div>
        <div class="total-value">{{ number_format($result->totalMonthly, 0, ',', ' ') }} Kč</div>
    </div>

    <h2>Zadané údaje</h2>
    <table>
        <tr><td>Datum narození</td><td class="num">{{ $birthDate->format('j. n. Y') }}</td></tr>
        <tr><td>Pohlaví</td><td class="num">{{ $gender === \App\Enums\Gender::Male ? 'Muž' : 'Žena' }}</td></tr>
        <tr><td>Počet vychovaných dětí</td><td class="num">{{ $children }}</td></tr>
        <tr><td>Plánované datum odchodu</td><td class="num">{{ $retirementDate->format('j. n. Y') }}</td></tr>
        <tr><td>Řádný důchodový věk</td><td class="num">{{ $normalAge['years'] }} let{{ $normalAge['months'] > 0 ? ' ' . $normalAge['months'] . ' měs.' : '' }}</td></tr>
        <tr><td>Doba pojištění</td><td class="num">{{ $result->insuranceYears }} let</td></tr>
    </table>

    <h2>Složení důchodu</h2>
    <table>
        <thead>
            <tr><th>Složka</th><th style="text-align: right">Měsíčně</th></tr>
        </thead>
        <tbody>
            @foreach ($result->breakdown as $item)
                <tr><td>{{ $item['label'] }}</td><td class="num">{{ number_format($item['amount'], 0, ',', ' ') }} Kč</td></tr>
            @endforeach
            <tr class="row-total"><td>Celkem</td><td class="num">{{ number_format($result->totalMonthly, 0, ',', ' ') }} Kč</td></tr>
        </tbody>
    </table>

    <h2>Mezivýpočty</h2>
    <table>
        <tr><td>Osobní vyměřovací základ (OVZ)</td><td class="num">{{ number_format($result->personalAssessmentBase, 0, ',', ' ') }} Kč</td></tr>
        <tr><td>Výpočtový základ po redukci</td><td class="num">{{ number_format($result->calculationBase, 0, ',', ' ') }} Kč</td></tr>
    </table>

    <div class="disclaimer">
        <strong>Upozornění:</strong> Výpočet je orientační a slouží pouze pro informační účely.
        Nezohledňuje všechny zákonné podmínky a individuální okolnosti (vyloučené doby, náhradní
        doby pojištění, zahraniční pojištění atd.). Přesný výpočet provádí výhradně ČSSZ na základě
        podané žádosti o důchod.
    </div>

    <div class="footer">
        Důchody.cz — bezplatné kalkulačky a srovnávače · Parametry platné pro rok {{ $retirementDate->year >= 2026 ? '2026' : $retirementDate->year }} · zákon 155/1995 Sb.
    </div>
</body>
</html>
