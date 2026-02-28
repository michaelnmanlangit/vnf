<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'business_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'latitude',
        'longitude',
        'customer_type',
        'status',
        'notes'
    ];

    protected $casts = [
        'customer_type' => 'string',
        'status' => 'string',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // Relationships
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
