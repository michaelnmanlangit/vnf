<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OTPController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show OTP verification form.
     */
    public function showVerificationForm()
    {
        $email = session('verification_email');
        
        if (!$email) {
            return redirect()->route('login')->with('error', 'Session expired. Please login again.');
        }

        return view('customer.auth.verify-otp', ['email' => $email]);
    }

    /**
     * Verify OTP code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $email = session('verification_email');
        
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
            ], 400);
        }

        // Verify OTP using email
        $result = $this->otpService->verifyOTP($email, $request->otp, 'email_verification');

        if ($result['success']) {
            // Find user by email
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Mark email as verified
            $user->email_verified_at = now();
            $user->save();

            // Update customer status to active
            $customer = Customer::where('user_id', $user->id)->first();
            if ($customer) {
                $customer->update(['status' => 'active']);
            }

            // Log in the user
            Auth::login($user);

            // Clear session
            session()->forget('verification_email');

            return response()->json([
                'success' => true,
                'redirect' => route('customer.profile.complete'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Invalid or expired OTP code.',
            'remaining_attempts' => $result['remaining_attempts'] ?? null,
        ], 400);
    }

    /**
     * Resend OTP code.
     */
    public function resend(Request $request)
    {
        $email = session('verification_email');
        
        if (!$email) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
            ], 400);
        }

        // Generate and send new OTP
        $result = $this->otpService->generateAndSendOTP($email, 'email_verification');

        return response()->json($result);
    }

    /**
     * Get OTP status.
     */
    public function status(Request $request)
    {
        $email = session('verification_email');
        
        if (!$email) {
            return response()->json(['valid' => false]);
        }

        return response()->json([
            'valid' => true,
            'email' => $email,
        ]);
    }
}
