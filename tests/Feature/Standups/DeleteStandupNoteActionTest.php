<?php

namespace Tests\Feature\Standups;

use App\Actions\Standups\DeleteStandupNote;
use App\Models\StandupNote;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteStandupNoteActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_the_standup_note(): void
    {
        $note = StandupNote::factory()->create();

        (new DeleteStandupNote)->handle($note);

        $this->assertDatabaseMissing('standup_notes', ['id' => $note->id]);
    }
}
