<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Carbon\Carbon;

class ActivateReturnedEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees:activate-returned';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically activate employees whose return date has been reached';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        
        // Find employees on leave whose return date is today or earlier
        $employees = Employee::where('employment_status', 'on_leave')
            ->whereNotNull('return_date')
            ->whereDate('return_date', '<=', $today)
            ->get();

        $count = 0;

        foreach ($employees as $employee) {
            $employee->update([
                'employment_status' => 'active',
                'return_date' => null, // Clear return date after activation
            ]);
            
            $count++;
            $this->info("Activated: {$employee->full_name} (ID: {$employee->id})");
        }

        if ($count > 0) {
            $this->info("Successfully activated {$count} employee(s).");
        } else {
            $this->info('No employees to activate.');
        }

        return 0;
    }
}
