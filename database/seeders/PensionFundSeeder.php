<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PensionFund;
use Illuminate\Database\Seeder;

final class PensionFundSeeder extends Seeder
{
    /**
     * Účastnické fondy DPS — orientační data pro vývoj.
     * V produkci nahradit reálnými daty z APS ČR / výročních zpráv společností.
     */
    public function run(): void
    {
        $funds = [
            // Allianz
            ['allianz-dynamicky', 'Allianz dynamický účastnický fond', 'Allianz penzijní společnost', 'dynamicky', 1.00, 15.00, 12.50, 8.20, 7.10, 18200, 95000],
            ['allianz-vyvazeny', 'Allianz vyvážený účastnický fond', 'Allianz penzijní společnost', 'vyvazeny', 0.80, 15.00, 7.80, 5.10, 4.30, 24100, 180000],
            ['allianz-konzervativni', 'Allianz povinný konzervativní fond', 'Allianz penzijní společnost', 'konzervativni', 0.40, 0.00, 3.20, 2.80, 2.10, 9000, 120000],
            // Česká spořitelna
            ['cs-dynamicky', 'ČS dynamický účastnický fond', 'Česká spořitelna – penzijní společnost', 'dynamicky', 1.00, 15.00, 13.10, 9.00, 7.80, 32400, 210000],
            ['cs-vyvazeny', 'ČS vyvážený účastnický fond', 'Česká spořitelna – penzijní společnost', 'vyvazeny', 0.80, 15.00, 8.40, 5.60, 4.60, 41200, 350000],
            ['cs-konzervativni', 'ČS povinný konzervativní fond', 'Česká spořitelna – penzijní společnost', 'konzervativni', 0.40, 0.00, 3.50, 3.00, 2.20, 15300, 200000],
            // ČSOB
            ['csob-dynamicky', 'ČSOB dynamický účastnický fond', 'ČSOB Penzijní společnost', 'dynamicky', 1.00, 15.00, 11.80, 8.50, 7.20, 21500, 150000],
            ['csob-vyvazeny', 'ČSOB vyvážený účastnický fond', 'ČSOB Penzijní společnost', 'vyvazeny', 0.80, 15.00, 7.20, 5.00, 4.10, 26800, 240000],
            ['csob-konzervativni', 'ČSOB povinný konzervativní fond', 'ČSOB Penzijní společnost', 'konzervativni', 0.40, 0.00, 3.10, 2.70, 2.00, 11200, 160000],
            // Komerční banka
            ['kb-dynamicky', 'KB dynamický účastnický fond', 'KB Penzijní společnost', 'dynamicky', 1.00, 15.00, 12.00, 8.00, 6.90, 17400, 130000],
            ['kb-vyvazeny', 'KB vyvážený účastnický fond', 'KB Penzijní společnost', 'vyvazeny', 0.80, 15.00, 7.50, 4.90, 4.00, 19600, 170000],
            // NN
            ['nn-dynamicky', 'NN dynamický účastnický fond', 'NN Penzijní společnost', 'dynamicky', 1.00, 15.00, 13.60, 9.40, 8.10, 14100, 90000],
            ['nn-vyvazeny', 'NN vyvážený účastnický fond', 'NN Penzijní společnost', 'vyvazeny', 0.80, 15.00, 8.00, 5.40, 4.50, 12300, 110000],
            // Generali
            ['generali-dynamicky', 'Generali dynamický účastnický fond', 'Generali penzijní společnost', 'dynamicky', 1.00, 15.00, 11.20, 7.60, 6.50, 9500, 70000],
            // UNIQA
            ['uniqa-dynamicky', 'UNIQA dynamický účastnický fond', 'UNIQA penzijní společnost', 'dynamicky', 1.00, 15.00, 12.80, 8.80, 7.50, 11000, 85000],
            ['uniqa-konzervativni', 'UNIQA povinný konzervativní fond', 'UNIQA penzijní společnost', 'konzervativni', 0.40, 0.00, 3.00, 2.60, 1.90, 5100, 65000],
            // Conseq
            ['conseq-globalni-akciovy', 'Conseq globální akciový účastnický fond', 'Conseq penzijní společnost', 'dynamicky', 1.00, 15.00, 15.20, 10.50, 9.30, 8200, 60000],
            ['conseq-dluhopisovy', 'Conseq dluhopisový účastnický fond', 'Conseq penzijní společnost', 'konzervativni', 0.40, 0.00, 3.80, 3.20, 2.40, 4300, 45000],
        ];

        foreach ($funds as [$slug, $name, $company, $type, $feeMgmt, $feePerf, $r1, $r3, $r5, $assets, $participants]) {
            PensionFund::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'               => $name,
                    'company'            => $company,
                    'fund_type'          => $type,
                    'fee_management'     => $feeMgmt,
                    'fee_performance'    => $feePerf,
                    'return_1y'          => $r1,
                    'return_3y'          => $r3,
                    'return_5y'          => $r5,
                    'total_assets_mil'   => $assets,
                    'participants_count' => $participants,
                    'affiliate_url'      => null,
                    'partner_id'         => null,
                ],
            );
        }
    }
}
