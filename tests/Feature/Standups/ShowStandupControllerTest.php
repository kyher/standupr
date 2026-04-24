<?php

namespace Tests\Feature\Standups;

use App\Models\Role;
use App\Models\Standup;
use App\Models\StandupNote;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowStandupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();
        $standup = Standup::factory()->create(['team_id' => $team->id]);

        $response = $this->get(route('standups.show', [$team, $standup]));

        $response->assertRedirect(route('login'));
    }

    public function test_member_can_view_a_standup(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('standups.show', [$team, $standup]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('standups/Show')
            ->where('standup.id', $standup->id)
            ->where('standup.date', $standup->date->toDateString())
        );
    }

    public function test_standup_notes_are_included_with_user(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);
        $note = StandupNote::factory()->create(['standup_id' => $standup->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('standups.show', [$team, $standup]));

        $response->assertInertia(fn ($page) => $page
            ->has('notes', 1)
            ->where('notes.0.id', $note->id)
            ->where('notes.0.body', $note->body)
            ->where('notes.0.user.id', $user->id)
        );
    }

    public function test_non_member_cannot_view_a_standup(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->get(route('standups.show', [$team, $standup]));

        $response->assertForbidden();
    }
}
