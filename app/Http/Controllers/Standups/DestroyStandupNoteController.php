<?php

namespace App\Http\Controllers\Standups;

use App\Actions\Standups\DeleteStandupNote;
use App\Http\Controllers\Controller;
use App\Models\Standup;
use App\Models\StandupNote;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DestroyStandupNoteController extends Controller
{
    public function __invoke(Team $team, Standup $standup, StandupNote $note, DeleteStandupNote $action): RedirectResponse
    {
        Gate::authorize('delete', $note);

        $action->handle($note);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Note removed.')]);

        return back();
    }
}
