<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_id',
        'assigned_by',
        'task_type',
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'completed_at',
        'notes',
        'relocate_to_storage_unit_id',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
    ];

    /**
     * Task type constants
     */
    const TASK_TYPES = [
        'storage_unit' => 'Storage Unit Assignment',
        'delivery' => 'Delivery Task',
        'temperature_check' => 'Temperature Check',
        'inventory_check' => 'Inventory Check',
        'payment_collection' => 'Payment Collection',
        'relocation' => 'Relocation',
    ];

    /**
     * Priority constants
     */
    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
    ];

    /**
     * Status constants
     */
    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
    ];

    /**
     * Get the user assigned to the task
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin who assigned the task
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the employee record for the assigned user
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the storage unit the worker is being relocated to
     */
    public function relocateToStorageUnit()
    {
        return $this->belongsTo(StorageUnit::class, 'relocate_to_storage_unit_id');
    }

    /**
     * Scope to filter by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by task type
     */
    public function scopeTaskType($query, $taskType)
    {
        return $query->where('task_type', $taskType);
    }

    /**
     * Scope to get overdue tasks
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereIn('status', ['pending', 'in_progress']);
    }

    /**
     * Check if task is overdue
     */
    public function isOverdue()
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               in_array($this->status, ['pending', 'in_progress']);
    }

    /**
     * Mark task as completed
     */
    public function markCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }
}
