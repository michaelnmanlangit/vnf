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
        Schema::table('employees', function (Blueprint $table) {
            // Link employee to storage unit (for production workers)
            $table->foreignId('assigned_storage_unit_id')->nullable()->after('department')->constrained('storage_units')->onDelete('set null');
            
            // Link employee to supervisor (for production workers)
            $table->foreignId('supervisor_id')->nullable()->after('assigned_storage_unit_id')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['assigned_storage_unit_id']);
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn(['assigned_storage_unit_id', 'supervisor_id']);
        });
    }
};
