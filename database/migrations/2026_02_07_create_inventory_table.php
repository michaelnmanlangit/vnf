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
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->enum('category', ['ice', 'meat', 'seafood', 'vegetables', 'fruits', 'beverages', 'dairy']);
            $table->decimal('quantity', 10, 2);
            $table->enum('unit', ['kg', 'liter', 'pieces', 'boxes']);
            $table->enum('storage_location', ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E']);
            $table->decimal('temperature_requirement', 5, 2);
            $table->date('expiration_date');
            $table->date('date_received');
            $table->string('supplier');
            $table->enum('status', ['in_stock', 'low_stock', 'expired', 'expiring_soon'])->default('in_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
