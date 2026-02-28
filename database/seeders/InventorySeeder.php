<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\StorageUnit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $units = StorageUnit::all();

        if ($units->isEmpty()) {
            $this->command->warn('No storage units found. Skipping inventory seeding.');
            return;
        }

        // Products matching the fields on the Add Inventory form:
        // product_name, category, quantity, unit, storage_location,
        // expiration_date, date_received, supplier, status, notes
        $products = [
            // Ice
            ['product_name' => 'Ice Block (25kg)',        'category' => 'ice',        'unit' => 'pieces', 'quantity' => 500,  'supplier' => 'V&F Ice Plant',               'days_received' => 2,  'months_expire' => 1],
            ['product_name' => 'Crushed Ice',             'category' => 'ice',        'unit' => 'kg',     'quantity' => 300,  'supplier' => 'V&F Ice Plant',               'days_received' => 1,  'months_expire' => 1],
            // Meat
            ['product_name' => 'Frozen Chicken Whole',    'category' => 'meat',       'unit' => 'kg',     'quantity' => 420,  'supplier' => 'Bounty Fresh Agri-Ventures', 'days_received' => 5,  'months_expire' => 6],
            ['product_name' => 'Chicken Wings',           'category' => 'meat',       'unit' => 'kg',     'quantity' => 180,  'supplier' => 'Bounty Fresh Agri-Ventures', 'days_received' => 5,  'months_expire' => 6],
            ['product_name' => 'Pork Belly',              'category' => 'meat',       'unit' => 'kg',     'quantity' => 250,  'supplier' => 'San Miguel Foods Inc.',       'days_received' => 7,  'months_expire' => 5],
            ['product_name' => 'Ground Beef',             'category' => 'meat',       'unit' => 'kg',     'quantity' => 200,  'supplier' => 'Monterey Foods Corp.',        'days_received' => 4,  'months_expire' => 4],
            // Seafood
            ['product_name' => 'Frozen Tuna',             'category' => 'seafood',    'unit' => 'kg',     'quantity' => 350,  'supplier' => 'Genova Seafoods',             'days_received' => 3,  'months_expire' => 8],
            ['product_name' => 'Shrimp',                  'category' => 'seafood',    'unit' => 'kg',     'quantity' => 150,  'supplier' => 'Genova Seafoods',             'days_received' => 6,  'months_expire' => 7],
            ['product_name' => 'Sardines',                'category' => 'seafood',    'unit' => 'kg',     'quantity' => 280,  'supplier' => 'Century Pacific Food',        'days_received' => 8,  'months_expire' => 10],
            // Vegetables
            ['product_name' => 'Mixed Vegetables',        'category' => 'vegetables', 'unit' => 'kg',     'quantity' => 300,  'supplier' => 'Dole Philippines Inc.',       'days_received' => 3,  'months_expire' => 4],
            ['product_name' => 'Corn Kernels',            'category' => 'vegetables', 'unit' => 'kg',     'quantity' => 220,  'supplier' => 'Dole Philippines Inc.',       'days_received' => 4,  'months_expire' => 5],
            // Fruits
            ['product_name' => 'Frozen Mango',            'category' => 'fruits',     'unit' => 'kg',     'quantity' => 160,  'supplier' => 'Dole Philippines Inc.',       'days_received' => 6,  'months_expire' => 9],
            ['product_name' => 'Frozen Strawberries',     'category' => 'fruits',     'unit' => 'kg',     'quantity' => 120,  'supplier' => 'Dole Philippines Inc.',       'days_received' => 5,  'months_expire' => 8],
        ];

        $unitCount = $units->count();

        foreach ($products as $i => $product) {
            $unit = $units[$i % $unitCount];

            Inventory::create([
                'product_name'    => $product['product_name'],
                'category'        => $product['category'],
                'quantity'        => $product['quantity'],
                'unit'            => $product['unit'],
                'storage_location'=> $unit->name,
                'expiration_date' => Carbon::now()->addMonths($product['months_expire']),
                'date_received'   => Carbon::now()->subDays($product['days_received']),
                'supplier'        => $product['supplier'],
                'status'          => 'in_stock',
                'notes'           => null,
            ]);

            $this->command->info("Added: {$product['product_name']} → {$unit->name}");
        }

        $this->command->info('Inventory seeding completed.');
    }
}
