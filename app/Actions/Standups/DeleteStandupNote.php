<?php

namespace App\Actions\Standups;

use App\Models\StandupNote;

class DeleteStandupNote
{
    public function handle(StandupNote $note): void
    {
        $note->delete();
    }
}
