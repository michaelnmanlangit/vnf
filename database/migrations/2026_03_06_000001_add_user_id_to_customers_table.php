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
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Make some fields nullable for customer accounts
            $table->string('business_name')->nullable()->change();
            $table->string('contact_person')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('customer_type')->nullable()->change();
            
            // Update status enum to include 'pending' for OTP verification
            $table->dropColumn('status');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('customer_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            
            // Restore original constraints
            $table->string('business_name')->nullable(false)->change();
            $table->string('contact_person')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('customer_type')->nullable(false)->change();
            
            $table->dropColumn('status');
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive'])->default('active')->after('customer_type');
        });
    }
};
