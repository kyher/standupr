<?php

namespace Tests\Feature\Standups;

use App\Models\Role;
use App\Models\Standup;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreStandupNoteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();
        $standup = Standup::factory()->create(['team_id' => $team->id]);

        $response = $this->post(route('standup-notes.store', [$team, $standup]), ['body' => 'My update']);

        $response->assertRedirect(route('login'));
    }

    public function test_member_can_add_a_note(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => 'My update for today'],
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('standup_notes', [
            'standup_id' => $standup->id,
            'user_id' => $user->id,
            'body' => 'My update for today',
        ]);
    }

    public function test_member_can_add_multiple_notes(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $this->actingAs($user)->post(route('standup-notes.store', [$team, $standup]), ['body' => 'First note']);
        $this->actingAs($user)->post(route('standup-notes.store', [$team, $standup]), ['body' => 'Second note']);

        $this->assertDatabaseCount('standup_notes', 2);
    }

    public function test_non_member_cannot_add_a_note(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => 'My update'],
        );

        $response->assertForbidden();
        $this->assertDatabaseEmpty('standup_notes');
    }

    public function test_body_is_required(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => ''],
        );

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseEmpty('standup_notes');
    }

    public function test_note_can_be_flagged_as_a_blocker(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => 'Blocked on API access', 'has_blocker' => true],
        );

        $this->assertDatabaseHas('standup_notes', [
            'standup_id' => $standup->id,
            'body' => 'Blocked on API access',
            'has_blocker' => true,
        ]);
    }

    public function test_note_has_no_blocker_by_default(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => 'Regular update'],
        );

        $this->assertDatabaseHas('standup_notes', [
            'standup_id' => $standup->id,
            'body' => 'Regular update',
            'has_blocker' => false,
        ]);
    }

    public function test_body_must_not_exceed_10000_characters(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $standup = Standup::factory()->today()->create(['team_id' => $team->id]);

        $response = $this->actingAs($user)->post(
            route('standup-notes.store', [$team, $standup]),
            ['body' => str_repeat('a', 10001)],
        );

        $response->assertSessionHasErrors('body');
        $this->assertDatabaseEmpty('standup_notes');
    }
}
