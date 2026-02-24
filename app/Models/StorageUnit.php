<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'capacity',
        'temperature_min',
        'temperature_max',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'decimal:2',
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
    ];

    /**
     * Get all employees assigned to this storage unit
     */
    public function assignedEmployees()
    {
        return $this->hasMany(Employee::class, 'assigned_storage_unit_id');
    }

    /**
     * Get the supervisor (user) assigned to this storage unit
     */
    public function supervisor()
    {
        return $this->hasOne(User::class, 'assigned_storage_location_id');
    }

    /**
     * Get production workers only (department = 'production')
     */
    public function productionWorkers()
    {
        return $this->assignedEmployees()->where('department', 'production');
    }

    /**
     * Get worker count for this unit
     */
    public function getWorkerCountAttribute()
    {
        return $this->assignedEmployees()->count();
    }

    /**
     * Get production worker count for this unit
     */
    public function getProductionWorkerCountAttribute()
    {
        return $this->productionWorkers()->count();
    }

    /**
     * Get temperature logs for this storage unit
     */
    public function temperatureLogs()
    {
        return $this->hasMany(TemperatureLog::class);
    }

    /**
     * Get the latest temperature log
     */
    public function latestTemperatureLog()
    {
        return $this->hasOne(TemperatureLog::class)->latestOfMany('recorded_at');
    }

    /**
     * Get inventory items stored in this unit
     */
    public function inventoryItems()
    {
        return $this->hasMany(Inventory::class, 'storage_location', 'code');
    }

    /**
     * Get current temperature status
     */
    public function getCurrentTemperatureStatus()
    {
        $latest = $this->latestTemperatureLog;
        if (!$latest) {
            return 'no_data';
        }

        if ($latest->temperature < $this->temperature_min - 5 || 
            $latest->temperature > $this->temperature_max + 5) {
            return 'critical';
        } elseif ($latest->temperature < $this->temperature_min || 
                  $latest->temperature > $this->temperature_max) {
            return 'warning';
        }
        return 'normal';
    }
}
