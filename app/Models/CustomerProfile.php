<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'user_id',
        'first_name',
        'last_name',
        'company_name',
        'business_type',
        'business_registration',
        'contact_person_name',
        'contact_person_position',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'latitude',
        'longitude',
        'profile_completed',
        'is_verified',
        'verified_at',
        'verified_by',
        'delivery_instructions',
        'business_hours',
    ];

    protected $casts = [
        'profile_completed' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'business_hours' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Get the user that owns the customer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer that owns the profile.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the admin who verified this profile.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if profile is complete.
     */
    public function isComplete(): bool
    {
        return !empty($this->company_name) 
            && !empty($this->business_type)
            && !empty($this->contact_person_name)
            && !empty($this->phone)
            && !empty($this->address)
            && !empty($this->latitude)
            && !empty($this->longitude);
    }

    /**
     * Mark profile as completed.
     */
    public function markCompleted(): void
    {
        $this->update(['profile_completed' => $this->isComplete()]);
    }
}
