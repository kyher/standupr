<?php

namespace Tests\Feature\Teams;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTeamControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->delete(route('teams.destroy', $team));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_delete_their_team(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->delete(route('teams.destroy', $team));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_member_cannot_delete_a_team(): void
    {
        $memberRole = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $memberRole->id]);

        $response = $this->actingAs($user)->delete(route('teams.destroy', $team));

        $response->assertForbidden();
        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    public function test_non_member_cannot_delete_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($user)->delete(route('teams.destroy', $team));

        $response->assertForbidden();
        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }
}
