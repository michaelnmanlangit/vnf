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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('business_name')->nullable()->change();
            $table->string('contact_person')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->enum('customer_type', ['wet_market', 'restaurant', 'meat_supplier', 'fishery', 'grocery', 'distribution_company', 'other'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('business_name')->nullable(false)->change();
            $table->string('contact_person')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->enum('customer_type', ['wet_market', 'restaurant', 'meat_supplier', 'fishery', 'grocery', 'distribution_company', 'other'])->nullable(false)->change();
        });
    }
};
