<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\DeleteTeam;
use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DestroyTeamController extends Controller
{
    public function __invoke(Team $team, DeleteTeam $action): RedirectResponse
    {
        Gate::authorize('delete', $team);

        $action->handle($team);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Team deleted.')]);

        return redirect()->route('dashboard');
    }
}
