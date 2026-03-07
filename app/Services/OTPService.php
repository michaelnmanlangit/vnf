<?php

namespace App\Services;

use App\Models\CustomerOTP;
use App\Mail\EmailVerificationOTP;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OTPService
{
    /**
     * OTP expiration time in minutes.
     */
    const OTP_EXPIRY_MINUTES = 15;

    /**
     * Maximum OTP attempts allowed.
     */
    const MAX_ATTEMPTS = 5;

    /**
     * Generate and send OTP to email.
     */
    public function generateAndSendOTP(string $email, string $purpose = 'email_verification'): array
    {
        try {
            // Invalidate any existing OTPs for this email and purpose
            $this->invalidateExistingOTPs($email, $purpose);

            // Generate 6-digit OTP
            $otpCode = $this->generateOTPCode();

            // Create OTP record
            $otp = CustomerOTP::create([
                'email' => $email,
                'otp_code' => $otpCode,
                'expires_at' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
                'purpose' => $purpose,
            ]);

            Log::info("OTP Created", [
                'email' => $email,
                'otp_code' => $otpCode,
                'otp_id' => $otp->id,
                'expires_at' => $otp->expires_at,
            ]);

            // Send email
            Mail::to($email)->send(new EmailVerificationOTP($otpCode, $purpose));

            Log::info("OTP sent successfully to: {$email}");

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'expires_at' => $otp->expires_at,
            ];

        } catch (\Exception $e) {
            Log::error("Failed to send OTP to {$email}: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
            ];
        }
    }

    /**
     * Verify OTP code for email.
     */
    public function verifyOTP(string $email, string $otpCode, string $purpose = 'email_verification'): array
    {
        // Find valid OTP
        $otp = CustomerOTP::validForEmail($email)
            ->forPurpose($purpose)
            ->first();

        Log::info("OTP Verification attempt", [
            'email' => $email,
            'entered_code' => $otpCode,
            'otp_found' => $otp ? 'yes' : 'no',
            'otp_code_in_db' => $otp ? $otp->otp_code : 'N/A',
            'is_used' => $otp ? $otp->is_used : 'N/A',
            'expires_at' => $otp ? $otp->expires_at : 'N/A',
        ]);

        if (!$otp) {
            return [
                'success' => false,
                'message' => 'Invalid or expired OTP code.',
            ];
        }

        // Check attempt limit
        if ($otp->attempt_count >= self::MAX_ATTEMPTS) {
            return [
                'success' => false,
                'message' => 'Maximum OTP attempts exceeded. Please request a new code.',
            ];
        }

        // Increment attempts
        $otp->incrementAttempts();

        // Verify code (trim whitespace)
        if (trim($otp->otp_code) !== trim($otpCode)) {
            return [
                'success' => false,
                'message' => 'Invalid OTP code.',
                'remaining_attempts' => self::MAX_ATTEMPTS - $otp->attempt_count,
            ];
        }

        // Mark as used
        $otp->markAsUsed();

        Log::info("OTP verified successfully for: {$email}");

        return [
            'success' => true,
            'message' => 'OTP verified successfully.',
        ];
    }

    /**
     * Check if user can request new OTP.
     */
    public function canRequestNewOTP(string $email, string $purpose = 'email_verification'): bool
    {
        // Check if there's a recent valid OTP (prevent spam)
        $recentOTP = CustomerOTP::where('email', $email)
            ->where('purpose', $purpose)
            ->where('created_at', '>', Carbon::now()->subMinutes(2))
            ->first();

        return !$recentOTP;
    }

    /**
     * Get remaining time for OTP expiry.
     */
    public function getOTPExpiryInfo(string $email, string $purpose = 'email_verification'): ?array
    {
        $otp = CustomerOTP::validForEmail($email)
            ->forPurpose($purpose)
            ->first();

        if (!$otp) {
            return null;
        }

        $remainingSeconds = Carbon::now()->diffInSeconds($otp->expires_at, false);

        return [
            'expires_at' => $otp->expires_at,
            'remaining_seconds' => max(0, (int) $remainingSeconds),
            'attempt_count' => $otp->attempt_count,
            'max_attempts' => self::MAX_ATTEMPTS,
        ];
    }

    /**
     * Generate a 6-digit OTP code.
     */
    private function generateOTPCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Invalidate existing OTPs for email and purpose.
     */
    private function invalidateExistingOTPs(string $email, string $purpose): void
    {
        CustomerOTP::where('email', $email)
            ->where('purpose', $purpose)
            ->where('is_used', false)
            ->update(['is_used' => true]);
    }

    /**
     * Clean up expired OTPs (can be called via scheduled task).
     */
    public static function cleanupExpiredOTPs(): int
    {
        $deleted = CustomerOTP::where('expires_at', '<', Carbon::now())
            ->orWhere('is_used', true)
            ->where('created_at', '<', Carbon::now()->subDays(7))
            ->delete();

        Log::info("Cleaned up {$deleted} expired OTP records.");

        return $deleted;
    }
}