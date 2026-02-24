<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend task_type enum to include 'relocation'
        DB::statement("ALTER TABLE task_assignments MODIFY COLUMN task_type ENUM('storage_unit','delivery','temperature_check','inventory_check','payment_collection','relocation') NOT NULL");

        Schema::table('task_assignments', function (Blueprint $table) {
            // Which storage unit the worker is being relocated TO
            $table->foreignId('relocate_to_storage_unit_id')
                  ->nullable()
                  ->after('notes')
                  ->constrained('storage_units')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropForeign(['relocate_to_storage_unit_id']);
            $table->dropColumn('relocate_to_storage_unit_id');
        });

        DB::statement("ALTER TABLE task_assignments MODIFY COLUMN task_type ENUM('storage_unit','delivery','temperature_check','inventory_check','payment_collection') NOT NULL");
    }
};
