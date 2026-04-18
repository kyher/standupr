<?php

namespace Tests\Unit\Teams;

use App\Actions\Teams\StoreTeam;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTeamActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_team(): void
    {
        Role::factory()->admin()->create();
        $user = User::factory()->create();

        $team = (new StoreTeam)->handle($user, 'Acme');

        $this->assertDatabaseHas('teams', ['name' => 'Acme']);
        $this->assertEquals('Acme', $team->name);
    }

    public function test_it_attaches_the_user_as_admin(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();

        $team = (new StoreTeam)->handle($user, 'Acme');

        $this->assertDatabaseHas('team_user', [
            'user_id' => $user->id,
            'team_id' => $team->id,
            'role_id' => $adminRole->id,
        ]);
    }
}
