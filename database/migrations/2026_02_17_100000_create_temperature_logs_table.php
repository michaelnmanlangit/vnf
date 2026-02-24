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
        Schema::create('temperature_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('storage_unit_id')->constrained('storage_units')->onDelete('cascade');
            $table->decimal('temperature', 5, 2); // Current temperature reading
            $table->decimal('humidity', 5, 2)->nullable(); // Optional humidity reading
            $table->enum('status', ['normal', 'warning', 'critical'])->default('normal');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
            
            // Index for faster queries
            $table->index(['storage_unit_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temperature_logs');
    }
};
