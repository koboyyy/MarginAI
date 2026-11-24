<?php

namespace Database\Factories;

use App\Models\Business;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
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
            'sku' => fake()->unique()->bothify('SKU-####'),
            'batch_yield' => '10.00',
            'batches_per_month' => '10.00',
            'labor_cost_per_batch' => '0.0000',
            'overhead_cost_per_batch' => '0.0000',
            'calculated_hpp' => '0.00',
            'recommended_price' => null,
            'selling_price' => null,
        ];
    }
}
