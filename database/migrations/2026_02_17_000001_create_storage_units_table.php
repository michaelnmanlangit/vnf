<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_units', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Cold Storage Unit 1"
            $table->string('code')->unique(); // e.g., "CS-001"
            $table->enum('type', ['cold_storage', 'production', 'delivery', 'maintenance'])->default('cold_storage');
            $table->decimal('capacity', 10, 2)->nullable(); // in cubic meters or tons
            $table->decimal('temperature_min', 5, 2)->nullable(); // minimum temperature
            $table->decimal('temperature_max', 5, 2)->nullable(); // maximum temperature
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default 5 storage units
        DB::table('storage_units')->insert([
            [
                'name' => 'Unit A',
                'code' => 'CS-001',
                'type' => 'cold_storage',
                'capacity' => 500.00,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'status' => 'active',
                'description' => 'Primary cold storage for frozen goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unit B',
                'code' => 'CS-002',
                'type' => 'cold_storage',
                'capacity' => 500.00,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'status' => 'active',
                'description' => 'Secondary cold storage for frozen goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unit C',
                'code' => 'CS-003',
                'type' => 'cold_storage',
                'capacity' => 500.00,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'status' => 'active',
                'description' => 'Third cold storage for frozen goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unit D',
                'code' => 'CS-004',
                'type' => 'cold_storage',
                'capacity' => 500.00,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'status' => 'active',
                'description' => 'Fourth cold storage for frozen goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Unit E',
                'code' => 'CS-005',
                'type' => 'cold_storage',
                'capacity' => 500.00,
                'temperature_min' => -20.00,
                'temperature_max' => -10.00,
                'status' => 'active',
                'description' => 'Fifth cold storage for frozen goods',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_units');
    }
};
