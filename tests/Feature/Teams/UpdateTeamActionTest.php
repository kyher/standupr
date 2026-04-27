<?php

namespace Tests\Feature\Teams;

use App\Actions\Teams\UpdateTeam;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_the_team_name(): void
    {
        $team = Team::factory()->create(['name' => 'Original Name']);

        $updatedTeam = (new UpdateTeam)->handle($team, 'Updated Name');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Updated Name']);
        $this->assertEquals('Updated Name', $updatedTeam->name);
    }
}
