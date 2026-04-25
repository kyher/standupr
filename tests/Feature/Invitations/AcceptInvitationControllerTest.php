<?php

namespace Tests\Feature\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptInvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $invitation = TeamInvitation::factory()->create();

        $response = $this->post(route('invitations.accept', $invitation));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_accept_their_invitation(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
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

        $response = $this->actingAs($user)->post(route('invitations.accept', $invitation));

        $response->assertRedirect(route('teams.show', $team));
        $this->assertDatabaseHas('team_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Accepted->value,
        ]);
        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $user->id,
            'role_id' => $memberRole->id,
        ]);
    }

    public function test_user_cannot_accept_another_users_invitation(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $otherUser = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($otherUser)->post(route('invitations.accept', $invitation));

        $response->assertForbidden();
    }
}
