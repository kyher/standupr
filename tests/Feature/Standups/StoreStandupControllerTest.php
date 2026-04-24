<?php

namespace Tests\Feature\Standups;

use App\Models\Role;
use App\Models\Standup;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreStandupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->post(route('standups.store', $team));

        $response->assertRedirect(route('login'));
    }

    public function test_member_can_create_a_standup(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);

        $response = $this->actingAs($user)->post(route('standups.store', $team));

        $standup = Standup::where('team_id', $team->id)->first();
        $this->assertNotNull($standup);
        $this->assertTrue($standup->date->isToday());
        $response->assertRedirect(route('standups.show', [$team, $standup]));
    }

    public function test_creating_standup_when_one_already_exists_today_redirects_to_existing(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);
        $existing = Standup::factory()->today()->create(['team_id' => $team->id]);

        $this->actingAs($user)->post(route('standups.store', $team));

        $this->assertDatabaseCount('standups', 1);
        $this->assertEquals($existing->id, Standup::where('team_id', $team->id)->first()->id);
    }

    public function test_non_member_cannot_create_a_standup(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($user)->post(route('standups.store', $team));

        $response->assertForbidden();
        $this->assertDatabaseEmpty('standups');
    }
}
