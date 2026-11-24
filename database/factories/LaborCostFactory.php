<?php

namespace Database\Factories;

use App\Enums\LaborRateType;
use App\Models\Business;
use App\Models\LaborCost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaborCost>
 */
class LaborCostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => Business::factory(),
            'name' => fake()->jobTitle(),
            'rate_type' => LaborRateType::PerHour,
            'rate' => '25000.0000',
        ];
    }
}
