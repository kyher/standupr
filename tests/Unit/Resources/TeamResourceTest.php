<?php

namespace Tests\Unit\Resources;

use App\Http\Resources\TeamResource;
use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_the_correct_structure(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->admin()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);

        $loadedTeam = $user->teams()->first();
        $data = TeamResource::make($loadedTeam)->resolve();

        $this->assertEquals([
            'id' => $team->id,
            'name' => $team->name,
            'role' => $role->name,
        ], $data);
    }
}
