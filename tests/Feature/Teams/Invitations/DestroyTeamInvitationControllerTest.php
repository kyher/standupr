<?php

namespace Tests\Feature\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTeamInvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();
        $invitation = TeamInvitation::factory()->create(['team_id' => $team->id]);

        $response = $this->delete(route('team-invitations.destroy', [$team, $invitation]));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_cancel_a_pending_invitation(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->delete(route('team-invitations.destroy', [$team, $invitation]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
    }

    public function test_member_cannot_cancel_an_invitation(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $member->teams()->attach($team, ['role_id' => $memberRole->id]);
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($member)->delete(route('team-invitations.destroy', [$team, $invitation]));

        $response->assertForbidden();
        $this->assertDatabaseHas('team_invitations', ['id' => $invitation->id]);
    }
}
