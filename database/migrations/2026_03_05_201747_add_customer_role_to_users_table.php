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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'warehouse_staff',
                'delivery_personnel',
                'inventory_staff',
                'temperature_staff',
                'payment_staff',
                'storage_supervisor',
                'customer'
            ])->default('warehouse_staff')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'admin',
                'warehouse_staff',
                'delivery_personnel',
                'inventory_staff',
                'temperature_staff',
                'payment_staff',
                'storage_supervisor'
            ])->default('warehouse_staff')->change();
        });
    }
};
