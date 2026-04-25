<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Invitations\AcceptInvitationController;
use App\Http\Controllers\Invitations\IndexInvitationController;
use App\Http\Controllers\Invitations\RejectInvitationController;
use App\Http\Controllers\Standups\ShowStandupController;
use App\Http\Controllers\Standups\StoreStandupController;
use App\Http\Controllers\Standups\StoreStandupNoteController;
use App\Http\Controllers\Teams\DestroyTeamController;
use App\Http\Controllers\Teams\Invitations\DestroyTeamInvitationController;
use App\Http\Controllers\Teams\Invitations\StoreTeamInvitationController;
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

    Route::post('teams/{team}/invitations', StoreTeamInvitationController::class)->name('team-invitations.store');
    Route::delete('teams/{team}/invitations/{invitation}', DestroyTeamInvitationController::class)->name('team-invitations.destroy');

    Route::get('invitations', IndexInvitationController::class)->name('invitations.index');
    Route::post('invitations/{invitation}/accept', AcceptInvitationController::class)->name('invitations.accept');
    Route::post('invitations/{invitation}/reject', RejectInvitationController::class)->name('invitations.reject');

    Route::post('teams/{team}/standups', StoreStandupController::class)->name('standups.store');
    Route::get('teams/{team}/standups/{standup}', ShowStandupController::class)->name('standups.show');
    Route::post('teams/{team}/standups/{standup}/notes', StoreStandupNoteController::class)->name('standup-notes.store');
});

require __DIR__.'/settings.php';
