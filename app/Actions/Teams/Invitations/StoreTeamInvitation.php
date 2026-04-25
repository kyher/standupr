<?php

namespace App\Actions\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class StoreTeamInvitation
{
    public function handle(Team $team, User $invitedBy, User $invitee): TeamInvitation
    {
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

        return TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $invitedBy->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);
    }
}
