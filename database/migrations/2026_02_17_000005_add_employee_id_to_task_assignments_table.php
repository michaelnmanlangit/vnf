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
        Schema::table('task_assignments', function (Blueprint $table) {
            // Add employee_id for production workers (who don't have user accounts)
            $table->foreignId('employee_id')->nullable()->after('user_id')->constrained('employees')->onDelete('cascade');
            
            // Make user_id nullable since now we can assign to employees too
            $table->foreignId('user_id')->nullable()->change();
            
            // Add check: either user_id OR employee_id must be set (handled in application logic)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignments', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');
            
            // Restore user_id as required
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
