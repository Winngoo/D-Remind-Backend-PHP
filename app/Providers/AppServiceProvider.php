<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
    public function boot()
    {
        // Custom validation rule for expiration date
        Validator::extend('expiration_date', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(0[1-9]|1[0-2])\/\d{4}$/', $value);
        });

        // Custom error message
        Validator::replacer('expiration_date', function ($message, $attribute, $rule, $parameters) {
            return 'The expiration date must be in MM/YYYY format.';
        });

    }
}
