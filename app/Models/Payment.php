<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'payment_reference',
        'amount',
        'payment_date',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'payment_method' => 'string'
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
