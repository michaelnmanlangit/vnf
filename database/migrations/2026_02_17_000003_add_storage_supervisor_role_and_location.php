<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update role enum to include storage_supervisor
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'warehouse_staff', 'delivery_personnel', 'inventory_staff', 'temperature_staff', 'payment_staff', 'storage_supervisor') DEFAULT 'warehouse_staff'");
        
        // Add storage location assignment for supervisors
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('assigned_storage_location_id')->nullable()->after('role')->constrained('storage_units')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['assigned_storage_location_id']);
            $table->dropColumn('assigned_storage_location_id');
        });
        
        // Revert role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'warehouse_staff', 'delivery_personnel', 'inventory_staff', 'temperature_staff', 'payment_staff') DEFAULT 'warehouse_staff'");
    }
};
