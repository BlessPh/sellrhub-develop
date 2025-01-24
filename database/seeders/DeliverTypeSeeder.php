<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliverTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DeliveryType::create(['delivery_type_name' => 'Standard']);
        DeliveryType::create(['delivery_type_name' => 'Regular']);
        DeliveryType::create(['delivery_type_name' => 'Express']);
    }
}
