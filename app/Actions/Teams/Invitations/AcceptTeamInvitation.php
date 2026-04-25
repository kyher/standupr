<?php

namespace App\Actions\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\TeamInvitation;

class AcceptTeamInvitation
{
    public function handle(TeamInvitation $invitation): void
    {
        $memberRole = Role::where('name', RoleEnum::Member->value)->firstOrFail();

        $invitation->team->users()->attach($invitation->user_id, ['role_id' => $memberRole->id]);

        $invitation->update(['status' => InvitationStatus::Accepted]);
    }
}
