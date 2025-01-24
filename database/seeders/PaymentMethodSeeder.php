<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::create(['method_name' => 'Cash']);
        PaymentMethod::create(['method_name' => 'Card']);
        PaymentMethod::create(['method_name' => 'Paypal']);
        PaymentMethod::create(['method_name' => 'Orange_Money']);
        PaymentMethod::create(['method_name' => 'Crypto']);
    }
}
