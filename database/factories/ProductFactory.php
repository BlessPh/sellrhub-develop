<?php

namespace Database\Factories;

use App\Models\Category;
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
            'product_name' => $this->faker->name(),
            'product_description' => $this->faker->text(),
            'product_price' => $this->faker->numberBetween(1000, 10000),
            'product_video' => $this->faker->url(),
            'product_quantity' => $this->faker->numberBetween(1, 1000),
            'category_id' => Category::all()->random()->id,
        ];
    }
}
