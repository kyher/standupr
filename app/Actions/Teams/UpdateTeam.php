<?php

namespace App\Actions\Teams;

use App\Models\Team;

class UpdateTeam
{
    public function handle(Team $team, string $name): Team
    {
        $team->update(['name' => $name]);

        return $team;
    }
}
