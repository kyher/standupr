<?php

namespace App\Actions\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Models\TeamInvitation;

class RejectTeamInvitation
{
    public function handle(TeamInvitation $invitation): void
    {
        $invitation->update(['status' => InvitationStatus::Rejected]);
    }
}
