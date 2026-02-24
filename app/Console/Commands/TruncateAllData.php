<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateAllData extends Command
{
    protected $signature = 'data:truncate';
    protected $description = 'Truncate all data from employees, billing, and work assignments tables';

    public function handle()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        $tables = ['task_assignments', 'invoice_items', 'payments', 'invoices', 'employees'];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
            $this->info("Truncated: $table");
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $this->info("\n✅ All data has been successfully deleted!");
    }
}
