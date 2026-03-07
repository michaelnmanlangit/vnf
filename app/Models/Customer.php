<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'user_id',
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
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(CustomerProfile::class, 'user_id', 'user_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'user_id');
    }
}
