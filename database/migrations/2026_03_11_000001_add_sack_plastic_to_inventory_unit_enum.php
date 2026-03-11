<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory MODIFY COLUMN unit ENUM('kg', 'liter', 'pieces', 'boxes', 'sack', 'plastic') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory MODIFY COLUMN unit ENUM('kg', 'liter', 'pieces', 'boxes') NOT NULL");
    }
};
