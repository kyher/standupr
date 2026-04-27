<?php

namespace App\Policies;

use App\Models\StandupNote;
use App\Models\User;

class StandupNotePolicy
{
    public function delete(User $user, StandupNote $note): bool
    {
        return $user->id === $note->user_id;
    }
}
