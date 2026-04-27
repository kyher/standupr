<?php

namespace Tests\Feature\Teams\Invitations;

use App\Actions\Teams\Invitations\StoreTeamInvitation;
use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTeamInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_pending_invitation(): void
    {
        $team = Team::factory()->create();
        $invitedBy = User::factory()->create();
        $invitee = User::factory()->create();

        $invitation = (new StoreTeamInvitation)->handle($team, $invitedBy, $invitee);

        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $team->id,
            'invited_by_user_id' => $invitedBy->id,
            'user_id' => $invitee->id,
            'status' => InvitationStatus::Pending->value,
        ]);
        $this->assertEquals($team->id, $invitation->team_id);
        $this->assertEquals($invitee->id, $invitation->user_id);
        $this->assertEquals(InvitationStatus::Pending, $invitation->status);
    }
}
