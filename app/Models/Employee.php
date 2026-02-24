<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'position',
        'department',
        'employment_status',
        'return_date',
        'hire_date',
        'salary',
        'address',
        'image',
        'assigned_storage_unit_id',
        'supervisor_id',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'return_date' => 'date',
        'salary' => 'decimal:2',
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the storage unit this employee is assigned to.
     */
    public function assignedStorageUnit()
    {
        return $this->belongsTo(StorageUnit::class, 'assigned_storage_unit_id');
    }

    /**
     * Get the supervisor (user) responsible for this employee.
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Get all attendance records for this employee.
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get task assignments for this employee (production workers without user accounts).
     */
    public function taskAssignments()
    {
        return $this->hasMany(TaskAssignment::class, 'employee_id');
    }

    /**
     * Check if employee is a production worker.
     */
    public function isProductionWorker()
    {
        return $this->department === 'production';
    }

    /**
     * Get today's attendance for this employee.
     */
    public function getTodayAttendanceAttribute()
    {
        return $this->attendance()->today()->first();
    }
}
