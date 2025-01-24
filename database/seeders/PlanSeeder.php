<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'title' => 'Monthly',
            'slug' => 'monthly',
            'stripe_id' => 'price_1QcnOhLa5XDb1e69LzwIKL1m'
        ]);

        Plan::create([
            'title' => 'Yearly',
            'slug' => 'yearly',
            'stripe_id' => 'price_1QcnOhLa5XDb1e69dKpiun5F'
        ]);
    }
}
