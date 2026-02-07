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
        User::updateOrCreate(
            ['email' => 'admin@vnf.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Create Warehouse Staff user
        User::updateOrCreate(
            ['email' => 'warehouse@vnf.com'],
            [
                'name' => 'Maria Garcia',
                'password' => Hash::make('warehouse123'),
                'role' => 'warehouse_staff',
            ]
        );

        // Create Delivery Personnel user
        User::updateOrCreate(
            ['email' => 'delivery@vnf.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('delivery123'),
                'role' => 'delivery_personnel',
            ]
        );

        // Additional warehouse staff
        User::updateOrCreate(
            ['email' => 'pedro@vnf.com'],
            [
                'name' => 'Pedro Santos',
                'password' => Hash::make('warehouse123'),
                'role' => 'warehouse_staff',
            ]
        );

        // Additional delivery personnel
        User::updateOrCreate(
            ['email' => 'miguel@vnf.com'],
            [
                'name' => 'Miguel Rivera',
                'password' => Hash::make('delivery123'),
                'role' => 'delivery_personnel',
            ]
        );
    }
}
