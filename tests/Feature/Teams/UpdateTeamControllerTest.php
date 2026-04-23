<?php

namespace Tests\Feature\Teams;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTeamControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->patch(route('teams.update', $team), ['name' => 'New Name']);

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_update_their_team(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => 'Updated Name']);

        $response->assertRedirect();
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Updated Name']);
    }

    public function test_member_cannot_update_a_team(): void
    {
        $memberRole = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Original Name']);
        $user->teams()->attach($team, ['role_id' => $memberRole->id]);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => 'Updated Name']);

        $response->assertForbidden();
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Original Name']);
    }

    public function test_non_member_cannot_update_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => 'Updated Name']);

        $response->assertForbidden();
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'Original Name']);
    }

    public function test_name_is_required(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_at_least_3_characters(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => 'AB']);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_not_exceed_255_characters(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($user)->patch(route('teams.update', $team), ['name' => str_repeat('a', 256)]);

        $response->assertSessionHasErrors('name');
    }
}
