<?php

namespace Database\Factories;

use App\Enums\Role as RoleEnum;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
        ];
    }

    public function admin(): static
    {
        return $this->state(['name' => RoleEnum::Admin->value]);
    }

    public function member(): static
    {
        return $this->state(['name' => RoleEnum::Member->value]);
    }
}
