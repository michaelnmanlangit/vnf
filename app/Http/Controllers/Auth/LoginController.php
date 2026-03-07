<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OTPService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    protected $otpService;

    public function __construct(OTPService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate all fields including reCAPTCHA
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA verification.',
            'g-recaptcha-response.recaptcha' => 'reCAPTCHA verification failed. Please try again.',
        ]);

        // Extract only email and password for authentication
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::user();
            
            // Check if customer needs email verification
            if ($user->role === 'customer' && is_null($user->email_verified_at)) {
                // Store email in session for OTP verification
                $request->session()->put('verification_email', $user->email);
                
                // Generate and send OTP
                $result = $this->otpService->generateAndSendOTP($user->email, 'email_verification');
                
                // Logout user temporarily (they'll be logged in after OTP verification)
                Auth::logout();
                
                if ($result['success']) {
                    return redirect()->route('customer.otp.verify.form')
                        ->with('success', 'Please verify your email. Check your inbox for the verification code.');
                } else {
                    return redirect()->route('customer.otp.verify.form')
                        ->with('warning', 'Please verify your email. You can request a verification code.');
                }
            }
            
            // Redirect based on user role
            return match($user->role) {
                'admin' => redirect()->intended('/admin/dashboard'),
                'inventory_staff' => redirect()->intended('/inventory'),
                'temperature_staff' => redirect()->intended('/warehouse/temperature'),
                'payment_staff' => redirect()->intended('/admin/billing'),
                'delivery_personnel' => redirect()->intended('/delivery/dashboard'),
                'customer' => redirect()->intended('/customer/shop'),
                default => redirect()->intended('/'),
            };
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
