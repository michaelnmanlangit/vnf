<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Customer;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Only check for customers
        if ($user && $user->role === 'customer') {
            $profile = \App\Models\CustomerProfile::where('user_id', $user->id)->first();

            // If customer doesn't have a completed profile, redirect to profile completion
            if (!$profile || !$profile->profile_completed) {
                // Don't redirect if already on profile completion routes
                if (!$request->routeIs('customer.profile.complete') && 
                    !$request->routeIs('customer.profile.store')) {
                    return redirect()->route('customer.profile.complete')
                        ->with('info', 'Please complete your profile to continue.');
                }
            }
        }

        return $next($request);
    }
}
