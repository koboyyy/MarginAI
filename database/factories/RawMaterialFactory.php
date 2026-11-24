<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RawMaterial>
 */
class RawMaterialFactory extends Factory
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
            'unit' => 'gram',
            'unit_price' => '1000.0000',
            'wastage_percent' => '0.00',
        ];
    }
}
