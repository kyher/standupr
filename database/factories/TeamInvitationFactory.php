<?php

namespace Database\Factories;

use App\Enums\InvitationStatus;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamInvitation>
 */
class TeamInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'invited_by_user_id' => User::factory(),
            'user_id' => User::factory(),
            'status' => InvitationStatus::Pending,
        ];
    }
}
