<?php

namespace Database\Factories;

use App\Enums\OverheadCostType;
use App\Models\Business;
use App\Models\OverheadCost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OverheadCost>
 */
class OverheadCostFactory extends Factory
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
            'name' => fake()->word(),
            'type' => OverheadCostType::Fixed,
            'amount' => '100000.0000',
        ];
    }
}
