<?php

namespace App\Http\Controllers\Standups;

use App\Http\Controllers\Controller;
use App\Http\Requests\Standups\StoreStandupNoteRequest;
use App\Models\Standup;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StoreStandupNoteController extends Controller
{
    public function __invoke(Team $team, Standup $standup, StoreStandupNoteRequest $request): RedirectResponse
    {
        Gate::authorize('view', $team);

        $standup->notes()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note added.')]);

        return back();
    }
}
