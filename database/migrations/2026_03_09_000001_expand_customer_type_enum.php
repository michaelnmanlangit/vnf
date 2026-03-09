<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `customers` MODIFY COLUMN `customer_type` ENUM(
            'wet_market',
            'restaurant',
            'meat_supplier',
            'fishery',
            'grocery',
            'distribution_company',
            'hotel',
            'retail',
            'wholesale',
            'catering',
            'other'
        ) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `customers` MODIFY COLUMN `customer_type` ENUM(
            'wet_market',
            'restaurant',
            'meat_supplier',
            'fishery',
            'grocery',
            'distribution_company',
            'other'
        ) NULL");
    }
};
