<?php

namespace Database\Factories;

use App\Models\Standup;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Standup>
 */
class StandupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'date' => fake()->unique()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }

    public function today(): static
    {
        return $this->state(['date' => today()->toDateString()]);
    }
}
