<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'assigned_user_id',
        'status',
        'notes',
        'started_at',
        'delivered_at',
    ];

    protected $casts = [
        'started_at'   => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function locations()
    {
        return $this->hasMany(DeliveryLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(DeliveryLocation::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'Pending',
            'in_transit' => 'In Transit',
            'delivered'  => 'Delivered',
            'cancelled'  => 'Cancelled',
            default      => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'    => 'warning',
            'in_transit' => 'info',
            'delivered'  => 'success',
            'cancelled'  => 'danger',
            default      => 'secondary',
        };
    }
}
