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
        // Register application service bindings and singletons here.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Bootstrap shared app behavior (macros, observers, policies, etc.).
    }
}
