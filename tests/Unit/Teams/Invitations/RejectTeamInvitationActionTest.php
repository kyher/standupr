<?php

namespace Tests\Unit\Teams\Invitations;

use App\Actions\Teams\Invitations\RejectTeamInvitation;
use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RejectTeamInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_marks_the_invitation_as_rejected(): void
    {
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        (new RejectTeamInvitation)->handle($invitation);

        $this->assertDatabaseHas('team_invitations', [
            'id' => $invitation->id,
            'status' => InvitationStatus::Rejected->value,
        ]);
    }

    public function test_it_does_not_add_the_user_to_the_team(): void
    {
        $admin = User::factory()->create();
        $invitee = User::factory()->create();
        $team = Team::factory()->create();
        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'invited_by_user_id' => $admin->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending,
        ]);

        (new RejectTeamInvitation)->handle($invitation);

        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $invitee->id,
        ]);
    }
}
