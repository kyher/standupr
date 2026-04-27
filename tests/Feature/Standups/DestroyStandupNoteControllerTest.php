<?php

namespace Tests\Feature\Standups;

use App\Models\Role;
use App\Models\Standup;
use App\Models\StandupNote;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyStandupNoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();
        $standup = Standup::factory()->create(['team_id' => $team->id]);
        $note = StandupNote::factory()->create(['standup_id' => $standup->id]);

        $response = $this->delete(route('standup-notes.destroy', [$team, $standup, $note]));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_delete_their_own_note(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->create(['team_id' => $team->id]);
        $note = StandupNote::factory()->create(['standup_id' => $standup->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('standup-notes.destroy', [$team, $standup, $note]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('standup_notes', ['id' => $note->id]);
    }

    public function test_user_cannot_delete_another_users_note(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $other = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->create(['team_id' => $team->id]);
        $note = StandupNote::factory()->create(['standup_id' => $standup->id, 'user_id' => $other->id]);

        $response = $this->actingAs($user)->delete(route('standup-notes.destroy', [$team, $standup, $note]));

        $response->assertForbidden();
        $this->assertDatabaseHas('standup_notes', ['id' => $note->id]);
    }
}
