<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@vnf.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create Warehouse Staff user
        User::create([
            'name' => 'Maria Garcia',
            'email' => 'warehouse@vnf.com',
            'password' => Hash::make('warehouse123'),
            'role' => 'warehouse_staff',
        ]);

        // Create Delivery Personnel user
        User::create([
            'name' => 'John Doe',
            'email' => 'delivery@vnf.com',
            'password' => Hash::make('delivery123'),
            'role' => 'delivery_personnel',
        ]);

        // Additional warehouse staff
        User::create([
            'name' => 'Pedro Santos',
            'email' => 'pedro@vnf.com',
            'password' => Hash::make('warehouse123'),
            'role' => 'warehouse_staff',
        ]);

        // Additional delivery personnel
        User::create([
            'name' => 'Miguel Rivera',
            'email' => 'miguel@vnf.com',
            'password' => Hash::make('delivery123'),
            'role' => 'delivery_personnel',
        ]);
    }
}
