<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Sitemap — denně v noci (články se publikují průběžně)
Schedule::command('sitemap:generate')->dailyAt('03:00');
