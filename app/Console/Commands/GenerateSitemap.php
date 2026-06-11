<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Http\Controllers\Content\PruvodceController;
use App\Http\Controllers\Seo\RocnikController;
use App\Models\Article;
use App\Models\PensionFund;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

final class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Vygeneruje public/sitemap.xml ze statických i programatických URL';

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        // ─── Statické stránky ───
        $static = [
            ['/', 1.0, Url::CHANGE_FREQUENCY_WEEKLY],
            ['/kalkulacka', 0.9, Url::CHANGE_FREQUENCY_WEEKLY],
            ['/kalkulacka/vek', 0.9, Url::CHANGE_FREQUENCY_WEEKLY],
            ['/kalkulacka/vyse', 0.9, Url::CHANGE_FREQUENCY_WEEKLY],
            ['/fondy', 0.8, Url::CHANGE_FREQUENCY_WEEKLY],
            ['/dip', 0.7, Url::CHANGE_FREQUENCY_MONTHLY],
            ['/statistiky', 0.7, Url::CHANGE_FREQUENCY_MONTHLY],
            ['/magazin', 0.8, Url::CHANGE_FREQUENCY_DAILY],
            ['/pruvodce', 0.8, Url::CHANGE_FREQUENCY_MONTHLY],
        ];

        foreach ($static as [$url, $priority, $freq]) {
            $sitemap->add(Url::create($url)->setPriority($priority)->setChangeFrequency($freq));
        }

        // ─── Průvodci ───
        foreach (array_keys(PruvodceController::GUIDES) as $slug) {
            $sitemap->add(
                Url::create("/pruvodce/{$slug}")
                    ->setPriority(0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY),
            );
        }

        // ─── Programatické — ročníky ───
        for ($rok = RocnikController::MIN_YEAR; $rok <= RocnikController::MAX_YEAR; $rok++) {
            $sitemap->add(
                Url::create("/duchod/rocnik/{$rok}")
                    ->setPriority(0.6)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY),
            );
        }

        // ─── Programatické — kraje ───
        foreach (config('regions') as $kraj) {
            $sitemap->add(
                Url::create("/statistiky/{$kraj['slug']}")
                    ->setPriority(0.5)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY),
            );
        }

        // ─── Fondy z DB ───
        PensionFund::query()->each(function (PensionFund $fund) use ($sitemap): void {
            $sitemap->add(
                Url::create("/fondy/{$fund->slug}")
                    ->setLastModificationDate($fund->updated_at ?? now())
                    ->setPriority(0.6)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY),
            );
        });

        // ─── Články z DB ───
        Article::published()->each(function (Article $article) use ($sitemap): void {
            $sitemap->add(
                Url::create("/magazin/{$article->slug}")
                    ->setLastModificationDate($article->updated_at ?? now())
                    ->setPriority(0.7)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY),
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap vygenerována: public/sitemap.xml');

        return self::SUCCESS;
    }
}
