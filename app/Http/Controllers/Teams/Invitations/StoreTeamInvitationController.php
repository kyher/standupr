<?php

namespace App\Http\Controllers\Teams\Invitations;

use App\Actions\Teams\Invitations\StoreTeamInvitation;
use App\Enums\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teams\Invitations\StoreTeamInvitationRequest;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class StoreTeamInvitationController extends Controller
{
    public function __invoke(StoreTeamInvitationRequest $request, Team $team, StoreTeamInvitation $action): RedirectResponse
    {
        Gate::authorize('create', [TeamInvitation::class, $team]);

        $invitee = User::where('email', $request->validated('email'))->firstOrFail();

        if ($team->users()->where('users.id', $invitee->id)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('This user is already a member of the team.'),
            ]);
        }

        if ($team->invitations()->where('user_id', $invitee->id)->where('status', InvitationStatus::Pending)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('This user already has a pending invitation.'),
            ]);
        }

        $action->handle($team, $request->user(), $invitee);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Invitation sent.')]);

        return back();
    }
}
