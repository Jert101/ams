<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class DomPDFServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->alias('PDF', \Barryvdh\DomPDF\Facade\Pdf::class);
        $this->app->bind('dompdf.options', function () {
            return [
                'enable_php' => true,
                'enable_javascript' => true,
                'enable_remote' => true,
                'temp_dir' => storage_path('app/dompdf'),
                'font_dir' => storage_path('app/dompdf/fonts/'),
                'font_cache' => storage_path('app/dompdf/fonts/'),
                'chroot' => storage_path('app/dompdf'),
            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 