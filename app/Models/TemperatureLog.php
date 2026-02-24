<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TemperatureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'storage_unit_id',
        'temperature',
        'humidity',
        'status',
        'recorded_by',
        'notes',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    /**
     * Get the storage unit this log belongs to
     */
    public function storageUnit()
    {
        return $this->belongsTo(StorageUnit::class);
    }

    /**
     * Get the user who recorded this log
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Check if temperature is within acceptable range
     */
    public function isWithinRange()
    {
        $unit = $this->storageUnit;
        return $this->temperature >= $unit->temperature_min && 
               $this->temperature <= $unit->temperature_max;
    }

    /**
     * Determine status based on temperature range
     */
    public static function determineStatus($temperature, StorageUnit $unit)
    {
        if ($temperature < $unit->temperature_min - 5 || $temperature > $unit->temperature_max + 5) {
            return 'critical';
        } elseif ($temperature < $unit->temperature_min || $temperature > $unit->temperature_max) {
            return 'warning';
        }
        return 'normal';
    }
}
