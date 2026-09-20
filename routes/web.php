<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\TrackingController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/track', [TrackingController::class, 'search'])
    ->middleware('throttle:tracking')
    ->name('tracking.search');

Route::get('/tracking/{shipment:tracking_code}', [TrackingController::class, 'show'])
    ->middleware('throttle:tracking')
    ->name('tracking.show');

Route::get('/locale/{locale}', function (string $locale) {
    if (in_array($locale, SetLocale::AVAILABLE, true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

Route::redirect('dashboard', '/admin')->middleware(['auth'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
