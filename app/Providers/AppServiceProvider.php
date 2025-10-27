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
        // Intentionally left blank. Package service providers should be discovered
        // or registered in the application's configuration. Avoid forcing the
        // legacy Excel provider here because older package versions call
        // Application::share() which doesn't exist in modern Laravel.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
