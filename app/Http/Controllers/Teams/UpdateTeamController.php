<?php

namespace App\Http\Controllers\Teams;

use App\Actions\Teams\UpdateTeam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\UpdateTeamRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UpdateTeamController extends Controller
{
    public function __invoke(UpdateTeamRequest $request, Team $team, UpdateTeam $action): RedirectResponse
    {
        Gate::authorize('update', $team);

        $action->handle($team, $request->validated('name'));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Team updated.')]);

        return back();
    }
}
