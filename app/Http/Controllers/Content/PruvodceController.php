<?php

declare(strict_types=1);

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class PruvodceController extends Controller
{
    /**
     * Registr průvodců: slug => [view, title, description].
     *
     * @var array<string, array{view: string, title: string, description: string}>
     */
    public const GUIDES = [
        'starobni-duchod' => [
            'view'        => 'content.pruvodce.starobni-duchod',
            'title'       => 'Starobní důchod 2026 — Kompletní průvodce | Důchody.cz',
            'description' => 'Vše o starobním důchodu: podmínky nároku, výpočet, jak požádat, termíny výplaty. Aktuální parametry pro rok 2026.',
        ],
        'predcasny-duchod' => [
            'view'        => 'content.pruvodce.predcasny-duchod',
            'title'       => 'Předčasný důchod 2026 — Podmínky a krácení | Důchody.cz',
            'description' => 'Kdy můžete odejít do předčasného důchodu, jak se počítá trvalé krácení a kdy se předčasný odchod vyplatí.',
        ],
        'penzijni-sporeni' => [
            'view'        => 'content.pruvodce.penzijni-sporeni',
            'title'       => 'Penzijní spoření 2026 — DPS vs. transformované fondy | Důchody.cz',
            'description' => 'Doplňkové penzijní spoření: státní příspěvky, daňové výhody, rozdíly mezi DPS a transformovanými fondy a jak vybrat strategii.',
        ],
        'duchodova-reforma-2025' => [
            'view'        => 'content.pruvodce.duchodova-reforma-2025',
            'title'       => 'Důchodová reforma — Co se mění a koho se týká | Důchody.cz',
            'description' => 'Přehled důchodové reformy: vyšší důchodový věk, pomalejší růst nových důchodů, změny předčasných důchodů a co zůstává.',
        ],
    ];

    public function index(): View
    {
        $meta = [
            'title'       => 'Průvodci důchodem — Srozumitelné návody | Důchody.cz',
            'description' => 'Komplexní průvodci českým důchodovým systémem: starobní a předčasný důchod, penzijní spoření a důchodová reforma.',
            'canonical'   => route('pruvodce.index'),
        ];

        $guides = self::GUIDES;

        return view('content.pruvodce.index', compact('guides', 'meta'));
    }

    public function show(string $slug): View
    {
        $guide = self::GUIDES[$slug] ?? null;
        abort_if($guide === null, 404);

        $meta = [
            'title'       => $guide['title'],
            'description' => $guide['description'],
            'canonical'   => route('pruvodce.show', $slug),
        ];

        return view($guide['view'], compact('meta', 'slug'));
    }
}
