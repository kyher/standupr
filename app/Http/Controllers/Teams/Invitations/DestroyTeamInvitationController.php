<?php

namespace App\Http\Controllers\Teams\Invitations;

use App\Actions\Teams\Invitations\DestroyTeamInvitation;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class DestroyTeamInvitationController extends Controller
{
    public function __invoke(Team $team, TeamInvitation $invitation, DestroyTeamInvitation $action): RedirectResponse
    {
        Gate::authorize('delete', $invitation);

        $action->handle($invitation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation cancelled.')]);

        return back();
    }
}
