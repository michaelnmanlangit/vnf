<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\StorageUnit;
use Illuminate\Support\Facades\DB;

class WorkerAutoAssignmentService
{
    /**
     * Automatically assign a production worker to the storage unit with the lowest worker count.
     *
     * @param Employee $employee
     * @return void
     */
    public function assignWorkerToUnit(Employee $employee)
    {
        // Only auto-assign production and warehouse workers
        if (!in_array($employee->department, ['production', 'warehouse'])) {
            return;
        }

        // Find the storage unit with the lowest assigned worker count
        $storageUnit = StorageUnit::where('status', 'active')
            ->where('type', 'cold_storage')
            ->withCount(['assignedEmployees' => function ($query) {
                $query->whereIn('department', ['production', 'warehouse']);
            }])
            ->orderBy('assigned_employees_count', 'asc')
            ->first();

        if ($storageUnit) {
            $employee->assigned_storage_unit_id = $storageUnit->id;
            $employee->save();
        }
    }

    /**
     * Redistribute all production workers evenly across all storage units.
     * This ensures fair load balancing.
     *
     * @return array Statistics about redistribution
     */
    public function redistributeAllWorkers()
    {
        $activeUnits = StorageUnit::where('status', 'active')
            ->where('type', 'cold_storage')
            ->get();

        if ($activeUnits->isEmpty()) {
            return ['error' => 'No active storage units available'];
        }

        $productionWorkers = Employee::whereIn('department', ['production', 'warehouse'])
            ->where('employment_status', 'active')
            ->orderBy('id')
            ->get();

        if ($productionWorkers->isEmpty()) {
            return ['error' => 'No production or warehouse workers to assign'];
        }

        $totalWorkers = $productionWorkers->count();
        $totalUnits = $activeUnits->count();
        $workersPerUnit = floor($totalWorkers / $totalUnits);
        $remainder = $totalWorkers % $totalUnits;

        $stats = [
            'total_workers' => $totalWorkers,
            'total_units' => $totalUnits,
            'workers_per_unit' => $workersPerUnit,
            'extra_workers' => $remainder,
            'distribution' => [],
        ];

        DB::beginTransaction();
        try {
            $workerIndex = 0;
            
            foreach ($activeUnits as $index => $unit) {
                // Each unit gets base amount, first units get +1 if there's remainder
                $workersForThisUnit = $workersPerUnit + ($index < $remainder ? 1 : 0);
                
                for ($i = 0; $i < $workersForThisUnit; $i++) {
                    if ($workerIndex < $totalWorkers) {
                        $worker = $productionWorkers[$workerIndex];
                        $worker->assigned_storage_unit_id = $unit->id;
                        $worker->save();
                        $workerIndex++;
                    }
                }

                $stats['distribution'][$unit->name] = $workersForThisUnit;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ['error' => 'Failed to redistribute workers: ' . $e->getMessage()];
        }

        return $stats;
    }

    /**
     * Get distribution statistics for all storage units.
     *
     * @return array
     */
    public function getDistributionStats()
    {
        $units = StorageUnit::where('status', 'active')
            ->where('type', 'cold_storage')
            ->withCount(['assignedEmployees' => function ($query) {
                $query->whereIn('department', ['production', 'warehouse'])
                    ->where('employment_status', 'active');
            }])
            ->get();

        $stats = [
            'total_units' => $units->count(),
            'total_workers' => Employee::whereIn('department', ['production', 'warehouse'])->where('employment_status', 'active')->count(),
            'unassigned_workers' => Employee::whereIn('department', ['production', 'warehouse'])->where('employment_status', 'active')->whereNull('assigned_storage_unit_id')->count(),
            'units' => [],
        ];

        foreach ($units as $unit) {
            $stats['units'][] = [
                'id' => $unit->id,
                'name' => $unit->name,
                'code' => $unit->code,
                'worker_count' => $unit->assigned_employees_count,
                'supervisor' => $unit->supervisor ? $unit->supervisor->name : 'Not Assigned',
            ];
        }

        return $stats;
    }

    /**
     * Manually reassign a worker to a different storage unit.
     *
     * @param Employee $employee
     * @param int $storageUnitId
     * @return bool
     */
    public function reassignWorker(Employee $employee, int $storageUnitId)
    {
        $storageUnit = StorageUnit::find($storageUnitId);

        if (!$storageUnit) {
            return false;
        }

        $employee->assigned_storage_unit_id = $storageUnitId;
        return $employee->save();
    }
}
