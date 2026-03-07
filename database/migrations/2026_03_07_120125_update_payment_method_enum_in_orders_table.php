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
        // First, update existing payment methods to map to new values
        DB::statement("UPDATE orders SET payment_method = 'cash' WHERE payment_method = 'cash_on_delivery'");
        DB::statement("UPDATE orders SET payment_method = 'cash' WHERE payment_method = 'bank_transfer'");
        DB::statement("UPDATE orders SET payment_method = 'cash' WHERE payment_method = 'credit_account'");
        // gcash already matches, so no need to update it
        
        // Then update the enum definition
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cash', 'gcash', 'paymaya') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old payment method enum values
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cash_on_delivery', 'gcash', 'bank_transfer', 'credit_account') NULL");
    }
};
