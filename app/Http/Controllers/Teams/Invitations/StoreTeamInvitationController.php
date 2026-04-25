<?php

namespace App\Http\Controllers\Teams\Invitations;

use App\Actions\Teams\Invitations\StoreTeamInvitation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\Invitations\StoreTeamInvitationRequest;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StoreTeamInvitationController extends Controller
{
    public function __invoke(StoreTeamInvitationRequest $request, Team $team, StoreTeamInvitation $action): RedirectResponse
    {
        Gate::authorize('create', [TeamInvitation::class, $team]);

        $invitee = User::where('email', $request->validated('email'))->firstOrFail();

        $action->handle($team, $request->user(), $invitee);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation sent.')]);

        return back();
    }
}
