<?php

namespace Tests\Feature\Teams;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTeamControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->post(route('teams.store'), ['name' => 'Acme']);

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_a_team(): void
    {
        Role::factory()->admin()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('teams.store'), ['name' => 'Acme']);

        $response->assertRedirect();
        $this->assertDatabaseHas('teams', ['name' => 'Acme']);
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('teams.store'), ['name' => '']);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_be_at_least_3_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('teams.store'), ['name' => 'AB']);

        $response->assertSessionHasErrors('name');
    }

    public function test_name_must_not_exceed_255_characters(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('teams.store'), ['name' => str_repeat('a', 256)]);

        $response->assertSessionHasErrors('name');
    }
}
