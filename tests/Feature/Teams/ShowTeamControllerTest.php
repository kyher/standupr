<?php

namespace Tests\Feature\Teams;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTeamControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->get(route('teams.show', $team));

        $response->assertRedirect(route('login'));
    }

    public function test_member_can_view_their_team(): void
    {
        $role = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('teams.show', $team));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('teams/Show')
            ->has('team')
            ->where('team.id', $team->id)
            ->where('team.name', $team->name)
            ->where('team.role', $role->name)
        );
    }

    public function test_non_member_cannot_view_a_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($user)->get(route('teams.show', $team));

        $response->assertForbidden();
    }
}
