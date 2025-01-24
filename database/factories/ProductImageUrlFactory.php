<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImageUrl;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageUrlFactory extends Factory
{
    protected $model = ProductImageUrl::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
