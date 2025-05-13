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
        $this->app->register(RouteServiceProvider::class);
        
        // Bind the role middleware to avoid 'Target class [role] does not exist' error
        $this->app->bind('role', function ($app) {
            return new \App\Http\Middleware\RoleMiddleware;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
