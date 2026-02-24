<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for Google reCAPTCHA.
    | You can obtain your keys from https://www.google.com/recaptcha/admin
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY', ''),
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),
    'version' => env('RECAPTCHA_VERSION', 'v2'), // v2, v3, invisible
];
