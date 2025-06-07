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
        
        // Bind the approved middleware
        $this->app->bind('approved', function ($app) {
            return new \App\Http\Middleware\EnsureAccountIsApproved;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share pending registrations count with admin views and sidebar
        \Illuminate\Support\Facades\View::composer(['admin.*', 'layouts.sidebar'], function ($view) {
            // Use a try-catch to prevent errors if the table or column doesn't exist yet
            try {
                $pendingCount = \App\Models\User::where('approval_status', 'pending')->count();
                $view->with('pendingRegistrationsCount', $pendingCount);
            } catch (\Exception $e) {
                // If there's an error (like missing column), just set count to 0
                $view->with('pendingRegistrationsCount', 0);
            }
        });
    }
}
