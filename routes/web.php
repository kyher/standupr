<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\StoreTeamController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::post('teams', StoreTeamController::class)->name('teams.store');
});

require __DIR__ . '/settings.php';
