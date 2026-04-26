<?php

namespace App\Http\Controllers\Teams;

use App\Http\Controllers\Controller;
use App\Http\Resources\StandupResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamController extends Controller
{
    public function __invoke(Team $team): Response
    {
        Gate::authorize('view', $team);

        $team = Auth::user()->teams()->where('teams.id', $team->id)->first();

        $todayStandup = $team->standups()->whereDate('date', today())->first();

        $previousStandups = $team->standups()
            ->whereDate('date', '<', today())
            ->orderByDesc('date')
            ->get();

        return Inertia::render('teams/Show', [
            'team' => TeamResource::make($team),
            'today_standup' => $todayStandup ? StandupResource::make($todayStandup) : null,
            'previous_standups' => StandupResource::collection($previousStandups),
        ]);
    }
}
