<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        /* User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]); */

        // Product::factory(20)->create();
        // Shop::factory(5)->create();
        // $this->call(RoleSeeder::class);
        // $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
        // $this->call([DeliverTypeSeeder::class]);
        // $this->call([PaymentMethodSeeder::class]);
    }
}
