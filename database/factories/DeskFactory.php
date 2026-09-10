<?php

namespace Database\Factories;

use App\Models\Desk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Desk>
 */
class DeskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'desk_number' => $this->faker->unique()->numberBetween(1, 100),
            'capacity' => $this->faker->randomElement([2, 4, 6, 8]),
            'status' => $this->faker->randomElement(['available', 'unavailable']),
        ];
    }
}
