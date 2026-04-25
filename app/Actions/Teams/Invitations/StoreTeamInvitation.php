<?php

namespace App\Actions\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;

class StoreTeamInvitation
{
    public function handle(Team $team, User $invitedBy, User $invitee): TeamInvitation
    {
        return TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $invitedBy->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);
    }
}
