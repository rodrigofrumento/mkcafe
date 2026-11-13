<?php

namespace Database\Factories;

use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Store>
 */
class StoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->owner(),
            'name' => fake()->unique()->company(),
            'description' => fake()->sentence(8),
            'phone' => fake()->numerify('(##) ####-####'),
            'mobile_phone' => fake()->numerify('(##) #####-####'),
        ];
    }
}
