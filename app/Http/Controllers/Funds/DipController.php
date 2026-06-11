<?php

declare(strict_types=1);

namespace App\Http\Controllers\Funds;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class DipController extends Controller
{
    public function index(): View
    {
        $providers = config('dip.providers');

        $meta = [
            'title'       => 'DIP — Dlouhodobý investiční produkt 2026: srovnání a daňová úspora | Důchody.cz',
            'description' => 'Co je DIP, kdo ho nabízí a kolik ušetříte na daních. Srovnání poskytovatelů Dlouhodobého investičního produktu a kalkulačka daňové úspory.',
            'canonical'   => route('dip'),
        ];

        return view('funds.dip', compact('providers', 'meta'));
    }
}
