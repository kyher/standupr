<?php

namespace Tests\Feature\Teams;

use App\Actions\Teams\DeleteTeam;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_the_team(): void
    {
        $team = Team::factory()->create();

        (new DeleteTeam)->handle($team);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}
