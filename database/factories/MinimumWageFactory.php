<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MinimumWage>
 */
class MinimumWageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'effective_date' => $this->faker->date(),
            'amount' => $this->faker->randomFloat(2, 1000, 2000),
        ];
    }
}
