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
        // Update old warehouse_staff roles to inventory_staff (default warehouse role)
        DB::table('users')
            ->where('role', 'warehouse_staff')
            ->update(['role' => 'inventory_staff']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert inventory_staff back to warehouse_staff (for rollback)
        DB::table('users')
            ->where('role', 'inventory_staff')
            ->update(['role' => 'warehouse_staff']);
    }
};
