<?php

declare(strict_types=1);

/*
 * Poskytovatelé Dlouhodobého investičního produktu (DIP).
 * Orientační data pro vývoj — v produkci aktualizovat dle nabídek poskytovatelů.
 */
return [
    'providers' => [
        ['name' => 'Portu', 'type' => 'Investiční platforma', 'products' => 'ETF portfolia na míru', 'fee' => '0,24–1,0 % ročně', 'min_deposit' => 'od 1 Kč', 'affiliate_url' => null],
        ['name' => 'XTB', 'type' => 'Broker', 'products' => 'Akcie, ETF (samosprávné)', 'fee' => '0 % (do limitu objemu)', 'min_deposit' => 'bez minima', 'affiliate_url' => null],
        ['name' => 'Patria Finance', 'type' => 'Broker', 'products' => 'Akcie, ETF, fondy', 'fee' => 'dle sazebníku', 'min_deposit' => 'bez minima', 'affiliate_url' => null],
        ['name' => 'Fio banka', 'type' => 'Banka / broker', 'products' => 'Akcie, ETF (e-Broker)', 'fee' => 'dle sazebníku', 'min_deposit' => 'bez minima', 'affiliate_url' => null],
        ['name' => 'Conseq', 'type' => 'Investiční společnost', 'products' => 'Podílové fondy', 'fee' => 'dle fondu', 'min_deposit' => 'od 500 Kč/měs', 'affiliate_url' => null],
        ['name' => 'Amundi', 'type' => 'Investiční společnost', 'products' => 'Podílové fondy', 'fee' => 'dle fondu', 'min_deposit' => 'od 500 Kč/měs', 'affiliate_url' => null],
        ['name' => 'Česká spořitelna', 'type' => 'Banka', 'products' => 'Fondy, spořicí složka', 'fee' => 'dle produktu', 'min_deposit' => 'od 300 Kč/měs', 'affiliate_url' => null],
        ['name' => 'ČSOB', 'type' => 'Banka', 'products' => 'Fondy, spořicí složka', 'fee' => 'dle produktu', 'min_deposit' => 'od 500 Kč/měs', 'affiliate_url' => null],
        ['name' => 'Komerční banka', 'type' => 'Banka', 'products' => 'Fondy (Amundi)', 'fee' => 'dle produktu', 'min_deposit' => 'od 300 Kč/měs', 'affiliate_url' => null],
        ['name' => 'Raiffeisenbank', 'type' => 'Banka', 'products' => 'Fondy, investiční účet', 'fee' => 'dle produktu', 'min_deposit' => 'od 500 Kč/měs', 'affiliate_url' => null],
    ],
];
