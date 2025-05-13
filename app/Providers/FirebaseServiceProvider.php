<?php

namespace App\Providers;

use App\Services\FirebaseService;
use Illuminate\Support\ServiceProvider;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });
        
        $this->app->alias(FirebaseService::class, 'firebase');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
