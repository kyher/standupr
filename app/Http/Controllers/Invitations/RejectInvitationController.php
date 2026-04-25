<?php

namespace App\Http\Controllers\Invitations;

use App\Actions\Teams\Invitations\RejectTeamInvitation;
use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RejectInvitationController extends Controller
{
    public function __invoke(TeamInvitation $invitation, RejectTeamInvitation $action): RedirectResponse
    {
        abort_unless($invitation->user_id === Auth::id(), 403);

        $action->handle($invitation);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation declined.')]);

        return back();
    }
}
