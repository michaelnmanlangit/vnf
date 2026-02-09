<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'product_name',
        'category',
        'quantity',
        'unit',
        'storage_location',
        'temperature_requirement',
        'expiration_date',
        'date_received',
        'supplier',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_received' => 'date',
        'expiration_date' => 'date',
        'temperature_requirement' => 'float',
        'quantity' => 'float',
    ];

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'in_stock' => '<span class="status-badge status-in-stock">In Stock</span>',
            'low_stock' => '<span class="status-badge status-low-stock">Low Stock</span>',
            'expired' => '<span class="status-badge status-expired">Expired</span>',
            'expiring_soon' => '<span class="status-badge status-expiring-soon">Expiring Soon</span>',
            default => '<span class="status-badge status-unknown">Unknown</span>',
        };
    }

    public function isExpiringWithin30Days()
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function isNearingExpiration()
    {
        return $this->expiration_date && $this->expiration_date->diffInDays(now()) <= 30 && !$this->expiration_date->isPast();
    }
}
