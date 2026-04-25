<?php

namespace Tests\Feature\Teams\Invitations;

use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTeamInvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->post(route('team-invitations.store', $team), ['email' => 'test@example.com']);

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_invite_a_user_by_email(): void
    {
        $adminRole = Role::factory()->admin()->create();
        Role::factory()->member()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->post(route('team-invitations.store', $team), [
            'email' => $invitee->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $team->id,
            'user_id' => $invitee->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Pending->value,
        ]);
    }

    public function test_member_cannot_invite(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $member = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $member->teams()->attach($team, ['role_id' => $memberRole->id]);

        $response = $this->actingAs($member)->post(route('team-invitations.store', $team), [
            'email' => $invitee->email,
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_invite_existing_member(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $admin = User::factory()->create();
        $existingMember = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $existingMember->teams()->attach($team, ['role_id' => $memberRole->id]);

        $response = $this->actingAs($admin)->post(route('team-invitations.store', $team), [
            'email' => $existingMember->email,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('team_invitations', 0);
    }

    public function test_cannot_invite_when_pending_invitation_exists(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('team-invitations.store', $team), [
            'email' => $invitee->email,
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('team_invitations', 1);
    }

    public function test_can_reinvite_after_rejection(): void
    {
        $adminRole = Role::factory()->admin()->create();
        Role::factory()->member()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Rejected,
        ]);

        $response = $this->actingAs($admin)->post(route('team-invitations.store', $team), [
            'email' => $invitee->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('team_invitations', 2);
    }

    public function test_email_must_belong_to_an_existing_user(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->post(route('team-invitations.store', $team), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
