<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\StorageUnit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeder to create sample inventory items.
     */
    public function run(): void
    {
        $units = StorageUnit::where('type', 'cold_storage')->get();

        if ($units->isEmpty()) {
            $this->command->warn('No storage units found. Please run storage units migration first.');
            return;
        }

        $products = [
            // Meat products
            ['product_name' => 'Frozen Chicken Whole', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 500, 'temp' => -18],
            ['product_name' => 'Chicken Wings', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 250, 'temp' => -18],
            ['product_name' => 'Chicken Breast', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 300, 'temp' => -18],
            ['product_name' => 'Beef Ribeye', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 200, 'temp' => -20],
            ['product_name' => 'Ground Beef', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 350, 'temp' => -20],
            ['product_name' => 'Pork Chops', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 280, 'temp' => -18],
            ['product_name' => 'Pork Belly', 'category' => 'meat', 'unit' => 'kg', 'quantity' => 320, 'temp' => -18],
            
            // Seafood
            ['product_name' => 'Frozen Tuna', 'category' => 'seafood', 'unit' => 'kg', 'quantity' => 400, 'temp' => -25],
            ['product_name' => 'Salmon Fillet', 'category' => 'seafood', 'unit' => 'kg', 'quantity' => 180, 'temp' => -25],
            ['product_name' => 'Shrimp', 'category' => 'seafood', 'unit' => 'kg', 'quantity' => 220, 'temp' => -22],
            ['product_name' => 'Sardines', 'category' => 'seafood', 'unit' => 'kg', 'quantity' => 300, 'temp' => -20],
            
            // Vegetables
            ['product_name' => 'Mixed Vegetables', 'category' => 'vegetables', 'unit' => 'kg', 'quantity' => 450, 'temp' => -15],
            ['product_name' => 'Green Peas', 'category' => 'vegetables', 'unit' => 'kg', 'quantity' => 200, 'temp' => -15],
            ['product_name' => 'Corn Kernels', 'category' => 'vegetables', 'unit' => 'kg', 'quantity' => 250, 'temp' => -15],
            
            // Fruits
            ['product_name' => 'Frozen Strawberries', 'category' => 'fruits', 'unit' => 'kg', 'quantity' => 150, 'temp' => -18],
            ['product_name' => 'Frozen Mango', 'category' => 'fruits', 'unit' => 'kg', 'quantity' => 180, 'temp' => -18],
            ['product_name' => 'Mixed Berries', 'category' => 'fruits', 'unit' => 'kg', 'quantity' => 120, 'temp' => -18],
            
            // Ice
            ['product_name' => 'Ice Blocks', 'category' => 'ice', 'unit' => 'pieces', 'quantity' => 1000, 'temp' => -10],
            ['product_name' => 'Crushed Ice', 'category' => 'ice', 'unit' => 'kg', 'quantity' => 800, 'temp' => -10],
        ];

        // Distribute products across storage units
        $unitIndex = 0;
        $unitNames = ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E'];
        
        foreach ($products as $product) {
            $unit = $units[$unitIndex % $units->count()];
            $storageLocation = $unitNames[$unitIndex % count($unitNames)];
            
            Inventory::create([
                'product_name' => $product['product_name'],
                'category' => $product['category'],
                'quantity' => $product['quantity'],
                'unit' => $product['unit'],
                'storage_location' => $storageLocation,
                'temperature_requirement' => $product['temp'],
                'expiration_date' => Carbon::now()->addMonths(rand(3, 12)),
                'date_received' => Carbon::now()->subDays(rand(1, 30)),
                'supplier' => 'Sample Supplier Inc.',
                'status' => 'in_stock',
                'notes' => 'Sample inventory item',
            ]);

            $this->command->info("Added {$product['product_name']} to {$storageLocation}");
            $unitIndex++;
        }

        $this->command->info('Inventory seeding completed successfully!');
    }
}
