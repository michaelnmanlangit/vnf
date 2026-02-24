<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\WorkerAutoAssignmentService;

class EmployeeObserver
{
    protected $autoAssignService;

    public function __construct(WorkerAutoAssignmentService $autoAssignService)
    {
        $this->autoAssignService = $autoAssignService;
    }

    /**
     * Handle the Employee "created" event.
     * Automatically assign production workers to storage units.
     */
    public function created(Employee $employee): void
    {
        // Auto-assign production and warehouse workers to the storage unit with lowest count
        if (in_array($employee->department, ['production', 'warehouse']) && $employee->employment_status === 'active') {
            $this->autoAssignService->assignWorkerToUnit($employee);
        }
    }

    /**
     * Handle the Employee "updated" event.
     * Reassign if department changes to production.
     */
    public function updated(Employee $employee): void
    {
        // If employee becomes a production or warehouse worker, assign them
        if ($employee->isDirty('department') && in_array($employee->department, ['production', 'warehouse']) && !$employee->assigned_storage_unit_id) {
            $this->autoAssignService->assignWorkerToUnit($employee);
        }

        // If employee becomes inactive, we might want to unassign them
        if ($employee->isDirty('employment_status') && $employee->employment_status === 'inactive') {
            // Optionally unassign from storage unit
            // $employee->assigned_storage_unit_id = null;
            // $employee->saveQuietly(); // Prevent infinite loop
        }
    }
}
