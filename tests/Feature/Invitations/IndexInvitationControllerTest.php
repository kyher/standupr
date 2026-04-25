<?php

namespace Tests\Feature\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexInvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get(route('invitations.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_view_their_pending_invitations(): void
    {
        $adminRole = Role::factory()->admin()->create();
        Role::factory()->member()->create();
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $user->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('invitations.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Invitations')
            ->has('invitations', 1)
            ->where('invitations.0.id', $invitation->id)
            ->where('invitations.0.team.name', $team->name)
        );
    }

    public function test_rejected_invitations_are_not_shown(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $user->id,
            'status' => InvitationStatus::Rejected,
        ]);

        $response = $this->actingAs($user)->get(route('invitations.index'));

        $response->assertInertia(fn ($page) => $page->has('invitations', 0));
    }
}
