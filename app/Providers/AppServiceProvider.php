<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Share default currency symbol with all views, cached forever until updated
        $currencySymbol = \Illuminate\Support\Facades\Cache::rememberForever('currency_symbol', function () {
            try {
                return \App\Models\Currency::where('is_default', true)->value('symbol') ?? '$';
            } catch (\Exception $e) {
                return '$';
            }
        });

        \Illuminate\Support\Facades\View::share('currency_symbol', $currencySymbol);
    }
}
