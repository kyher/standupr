<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Teams\DestroyTeamController;
use App\Http\Controllers\Teams\ShowTeamController;
use App\Http\Controllers\Teams\StoreTeamController;
use App\Http\Controllers\Teams\UpdateTeamController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::post('teams', StoreTeamController::class)->name('teams.store');
    Route::get('teams/{team}', ShowTeamController::class)->name('teams.show');
    Route::patch('teams/{team}', UpdateTeamController::class)->name('teams.update');
    Route::delete('teams/{team}', DestroyTeamController::class)->name('teams.destroy');
});

require __DIR__.'/settings.php';
