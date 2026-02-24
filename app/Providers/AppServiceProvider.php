<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Models\Invoice;
use App\Models\Employee;
use App\Observers\InvoiceObserver;
use App\Observers\EmployeeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register reCAPTCHA validation rule
        Validator::extend('recaptcha', function ($attribute, $value, $parameters, $validator) {
            $secretKey = config('services.recaptcha.secret_key');
            
            if (empty($secretKey)) {
                return true; // Skip validation if secret key is not configured
            }

            try {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                $result = $response->json();
                
                return isset($result['success']) && $result['success'] === true;
            } catch (\Exception $e) {
                // Log the error and return false
                \Log::error('reCAPTCHA validation error: ' . $e->getMessage());
                return false;
            }
        });

        Validator::replacer('recaptcha', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':attribute', $attribute, 'The reCAPTCHA verification failed.');
        });

        // Register Invoice Observer for auto-assignment
        Invoice::observe(InvoiceObserver::class);

        // Register Employee Observer for auto-worker assignment
        Employee::observe(EmployeeObserver::class);
    }
}
