<?php

namespace App\Policies;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;

class TeamInvitationPolicy
{
    public function create(User $user, Team $team): bool
    {
        return $user->teams()
            ->where('teams.id', $team->id)
            ->wherePivot('role_id', Role::where('name', RoleEnum::Admin->value)->value('id'))
            ->exists();
    }

    public function delete(User $user, TeamInvitation $invitation): bool
    {
        return $user->teams()
            ->where('teams.id', $invitation->team_id)
            ->wherePivot('role_id', Role::where('name', RoleEnum::Admin->value)->value('id'))
            ->exists();
    }
}
