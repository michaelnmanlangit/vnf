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
        DB::statement("UPDATE payments SET payment_method = 'gcash' WHERE payment_method = 'online_payment'");
        DB::statement("UPDATE payments SET payment_method = 'cash' WHERE payment_method = 'check'");
        DB::statement("UPDATE payments SET payment_method = 'cash' WHERE payment_method = 'bank_transfer'");
        DB::statement("UPDATE payments SET payment_method = 'cash' WHERE payment_method = 'other'");
        
        // Then update the enum definition
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'gcash', 'paymaya') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old payment method enum values
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('cash', 'bank_transfer', 'check', 'online_payment', 'other') NOT NULL");
    }
};
