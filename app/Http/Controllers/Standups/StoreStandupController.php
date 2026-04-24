<?php

namespace App\Http\Controllers\Standups;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StoreStandupController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        Gate::authorize('view', $team);

        $standup = $team->standups()->firstOrCreate(['date' => today()]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Stand-up created.')]);

        return redirect()->route('standups.show', [$team, $standup]);
    }
}
