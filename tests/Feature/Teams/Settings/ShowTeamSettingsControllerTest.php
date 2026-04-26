<?php

namespace Tests\Feature\Teams\Settings;

use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTeamSettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $team = Team::factory()->create();

        $response = $this->get(route('team-settings.edit', $team));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_view_team_settings(): void
    {
        $role = Role::factory()->admin()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('team-settings.edit', $team));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('teams/Settings')
            ->has('team')
            ->where('team.id', $team->id)
            ->where('team.name', $team->name)
            ->where('team.role', $role->name)
        );
    }

    public function test_member_cannot_view_team_settings(): void
    {
        $role = Role::factory()->member()->create();
        $user = User::factory()->create();
        $team = Team::factory()->create();
        $user->teams()->attach($team, ['role_id' => $role->id]);

        $response = $this->actingAs($user)->get(route('team-settings.edit', $team));

        $response->assertForbidden();
    }

    public function test_non_member_cannot_view_team_settings(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        $response = $this->actingAs($user)->get(route('team-settings.edit', $team));

        $response->assertForbidden();
    }

    public function test_members_are_returned(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $member->teams()->attach($team, ['role_id' => $memberRole->id]);

        $response = $this->actingAs($admin)->get(route('team-settings.edit', $team));

        $response->assertInertia(fn ($page) => $page
            ->has('members', 2)
            ->where('members.0.id', $admin->id)
            ->where('members.0.role', $adminRole->name)
            ->where('members.1.id', $member->id)
            ->where('members.1.role', $memberRole->name)
        );
    }

    public function test_pending_invitations_are_returned(): void
    {
        $role = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $role->id]);
        $invitation = TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->get(route('team-settings.edit', $team));

        $response->assertInertia(fn ($page) => $page
            ->has('pending_invitations', 1)
            ->where('pending_invitations.0.id', $invitation->id)
            ->where('pending_invitations.0.user.id', $invitee->id)
        );
    }

    public function test_only_pending_invitations_are_returned(): void
    {
        $role = Role::factory()->admin()->create();
        $admin = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $role->id]);
        TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Pending,
        ]);
        TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Accepted,
        ]);
        TeamInvitation::factory()->create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'status' => InvitationStatus::Rejected,
        ]);

        $response = $this->actingAs($admin)->get(route('team-settings.edit', $team));

        $response->assertInertia(fn ($page) => $page
            ->has('pending_invitations', 1)
        );
    }
}
