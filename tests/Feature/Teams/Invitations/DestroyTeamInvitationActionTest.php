<?php

namespace Tests\Feature\Teams\Invitations;

use App\Actions\Teams\Invitations\DestroyTeamInvitation;
use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTeamInvitationActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_the_invitation(): void
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

        (new DestroyTeamInvitation)->handle($invitation);

        $this->assertDatabaseMissing('team_invitations', ['id' => $invitation->id]);
    }
}
