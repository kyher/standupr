<?php

namespace App\Actions\Teams;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;

class StoreTeam
{
    public function handle(User $user, string $name): Team
    {
        $team = Team::create(['name' => $name]);

        $adminRole = Role::where('name', RoleEnum::Admin->value)->firstOrFail();

        $team->users()->attach($user, ['role_id' => $adminRole->id]);

        return $team;
    }
}
