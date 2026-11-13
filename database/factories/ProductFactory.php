<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'store_id' => Store::factory(),
            'name' => fake()->unique()->words(3, true),
            'description' => fake()->sentence(10),
            'body' => fake()->paragraphs(3, true),
            'price' => fake()->randomFloat(2, 10, 5000),
        ];
    }
}
