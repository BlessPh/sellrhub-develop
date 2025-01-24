<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'total_price' => $this->faker->randomFloat(),
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'payment_method_id' => $this->faker->randomNumber(),
            'delivery_type_id' => $this->faker->randomNumber(),

            'user_id' => User::factory(),
            'shop_id' => Shop::factory(),
        ];
    }
}
