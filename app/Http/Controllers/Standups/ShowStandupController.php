<?php

namespace App\Http\Controllers\Standups;

use App\Http\Controllers\Controller;
use App\Http\Resources\StandupNoteResource;
use App\Http\Resources\StandupResource;
use App\Models\Standup;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ShowStandupController extends Controller
{
    public function __invoke(Team $team, Standup $standup): Response
    {
        Gate::authorize('view', $team);

        $standup->load(['notes.user']);

        return Inertia::render('standups/Show', [
            'team' => ['id' => $team->id, 'name' => $team->name],
            'standup' => StandupResource::make($standup),
            'notes' => StandupNoteResource::collection($standup->notes),
        ]);
    }
}
