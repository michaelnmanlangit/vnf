<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax',
        'total_amount',
        'status',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'status' => 'string'
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Helper methods
    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute()
    {
        return $this->total_amount - $this->total_paid;
    }
}
