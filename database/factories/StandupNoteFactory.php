<?php

namespace Database\Factories;

use App\Models\Standup;
use App\Models\StandupNote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StandupNote>
 */
class StandupNoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'standup_id' => Standup::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentence(),
        ];
    }
}
