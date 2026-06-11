<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $meta = [
            'title'       => 'Důchody.cz — Kalkulačka důchodu a informace o penzích v ČR',
            'description' => 'Spočítejte si kdy a kolik dostanete v důchodu. Bezplatná online kalkulačka, srovnání penzijních fondů a aktuální informace o důchodech v ČR.',
            'canonical'   => route('home'),
            'ogType'      => 'website',
        ];

        return view('home', compact('meta'));
    }
}
