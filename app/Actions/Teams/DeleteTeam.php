<?php

namespace App\Actions\Teams;

use App\Models\Team;

class DeleteTeam
{
    public function handle(Team $team): void
    {
        $team->delete();
    }
}
