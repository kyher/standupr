<?php

namespace App\Http\Controllers\Invitations;

use App\Actions\Teams\Invitations\AcceptTeamInvitation;
use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AcceptInvitationController extends Controller
{
    public function __invoke(TeamInvitation $invitation, AcceptTeamInvitation $action): RedirectResponse
    {
        abort_unless($invitation->user_id === Auth::id(), 403);

        $action->handle($invitation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('You have joined :team.', ['team' => $invitation->team->name])]);

        return redirect()->route('teams.show', $invitation->team_id);
    }
}
