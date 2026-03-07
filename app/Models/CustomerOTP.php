<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CustomerOTP extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'customer_otps';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'otp_code',
        'expires_at',
        'is_used',
        'attempt_count',
        'purpose',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
        'attempt_count' => 'integer',
    ];

    /**
     * Check if the OTP is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if the OTP is still valid (not expired and not used).
     */
    public function isValid(): bool
    {
        return !$this->is_used && !$this->isExpired();
    }

    /**
     * Mark the OTP as used.
     */
    public function markAsUsed(): bool
    {
        $this->is_used = true;
        return $this->save();
    }

    /**
     * Increment the attempt count.
     */
    public function incrementAttempts(): bool
    {
        $this->attempt_count++;
        return $this->save();
    }

    /**
     * Scope to get valid OTPs for an email.
     */
    public function scopeValidForEmail($query, string $email)
    {
        return $query->where('email', $email)
            ->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    /**
     * Scope to get OTPs for a specific purpose.
     */
    public function scopeForPurpose($query, string $purpose)
    {
        return $query->where('purpose', $purpose);
    }
}
