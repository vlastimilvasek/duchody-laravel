<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Calculator\PensionAgeController;
use App\Http\Controllers\Calculator\PensionAmountController;
use App\Http\Controllers\Content\MagazinController;
use App\Http\Controllers\Content\PruvodceController;
use App\Http\Controllers\Funds\DipController;
use App\Http\Controllers\Funds\FundsController;
use App\Http\Controllers\Seo\RocnikController;
use App\Http\Controllers\Seo\StatistikyController;
use Illuminate\Support\Facades\Route;

// ─── Homepage ───
Route::get('/', HomeController::class)->name('home');

// ─── Newsletter ───
Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/newsletter/potvrzeni/{subscriber}', [NewsletterController::class, 'confirm'])
    ->middleware('signed')
    ->name('newsletter.confirm');
Route::get('/newsletter/odhlaseni/{subscriber}', [NewsletterController::class, 'unsubscribe'])
    ->middleware('signed')
    ->name('newsletter.unsubscribe');

// ─── Kalkulačky ───
Route::prefix('kalkulacka')->name('kalkulacka.')->group(function () {
    Route::get('/', fn () => view('calculator.index'))->name('index');
    Route::get('/vek', [PensionAgeController::class, 'index'])->name('vek');
    Route::post('/vek/spocitat', [PensionAgeController::class, 'calculate'])->name('vek.calculate');
    Route::get('/vyse', [PensionAmountController::class, 'index'])->name('vyse');
    Route::post('/vyse/spocitat', [PensionAmountController::class, 'calculate'])->name('vyse.calculate');
    Route::post('/vyse/ulozit', [PensionAmountController::class, 'save'])->name('vyse.save');
    Route::post('/vyse/pdf', [PensionAmountController::class, 'pdf'])->name('vyse.pdf');
});

// ─── DIP ───
Route::get('/dip', [DipController::class, 'index'])->name('dip');

// ─── Penzijní fondy ───
Route::prefix('fondy')->name('fondy.')->group(function () {
    Route::get('/', [FundsController::class, 'index'])->name('index');
    Route::get('/{slug}', [FundsController::class, 'show'])->name('show');
});

// ─── Statistiky ───
Route::prefix('statistiky')->name('statistiky.')->group(function () {
    Route::get('/', [StatistikyController::class, 'index'])->name('index');
    Route::get('/{kraj}', [StatistikyController::class, 'show'])->name('show');
});

// ─── Průvodci ───
Route::prefix('pruvodce')->name('pruvodce.')->group(function () {
    Route::get('/', [PruvodceController::class, 'index'])->name('index');
    Route::get('/{slug}', [PruvodceController::class, 'show'])->name('show');
});

// ─── Magazín ───
Route::prefix('magazin')->name('magazin.')->group(function () {
    Route::get('/', [MagazinController::class, 'index'])->name('index');
    Route::get('/{slug}', [MagazinController::class, 'show'])->name('show');
});

// ─── Programatické SEO — ročníky ───
Route::get('/duchod/rocnik/{rok}', [RocnikController::class, 'show'])
    ->whereNumber('rok')
    ->name('duchod.rocnik');
