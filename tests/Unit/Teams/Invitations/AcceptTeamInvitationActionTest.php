<?php

namespace Tests\Unit\Teams\Invitations;

use App\Actions\Teams\Invitations\AcceptTeamInvitation;
use App\Enums\InvitationStatus;
use App\Models\Role;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcceptTeamInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_attaches_the_user_to_the_team_as_member(): void
    {
        $adminRole = Role::factory()->admin()->create();
        $memberRole = Role::factory()->member()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $admin->teams()->attach($team, ['role_id' => $adminRole->id]);
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        (new AcceptTeamInvitation)->handle($invitation);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $invitee->id,
            'role_id' => $memberRole->id,
        ]);
    }

    public function test_it_marks_the_invitation_as_accepted(): void
    {
        Role::factory()->admin()->create();
        Role::factory()->member()->create();
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        (new AcceptTeamInvitation)->handle($invitation);

        $this->assertDatabaseHas('team_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Accepted->value,
        ]);
    }
}
