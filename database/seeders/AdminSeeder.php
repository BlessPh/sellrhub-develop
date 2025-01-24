<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'firstname' => 'Bruno',
            'lastname' => 'Masiala',
            'email' => 'admin@admin.com',
            'phone_number' => '+243891613082',
            'password' => 'admin_password',
        ])->assignRole(['super_admin', 'admin', 'seller']);
    }
}
