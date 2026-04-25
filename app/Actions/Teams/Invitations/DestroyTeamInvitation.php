<?php

namespace App\Actions\Teams\Invitations;

use App\Models\TeamInvitation;

class DestroyTeamInvitation
{
    public function handle(TeamInvitation $invitation): void
    {
        $invitation->delete();
    }
}
