<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KofA Attendance Monitoring System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

        <!-- Scripts -->
        <!-- Using absolute URLs for production environment -->
        <link rel="stylesheet" href="/build/assets/app-CoOQjfJF.css">
        <script src="/build/assets/app-DQS8sAPH.js" defer></script>

        <!-- Auth Fix CSS -->
        <link rel="stylesheet" href="/css/auth-fix.css">
        
        <!-- Global Responsive CSS -->
        <link href="/css/responsive.css" rel="stylesheet">
        
        <!-- Fallback CSS in case of asset loading issues -->
        <style>
            /* Basic fallback styles */
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            .bg-white {
                background-color: #ffffff;
            }
            .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            .rounded-lg {
                border-radius: 0.5rem;
            }
            .px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            .text-center {
                text-align: center;
            }
            .w-full {
                width: 100%;
            }
            .mt-6 {
                margin-top: 1.5rem;
            }
            .text-sm {
                font-size: 0.875rem;
            }
            .text-gray-700 {
                color: #4a5568;
            }
            .btn-primary {
                background-color: #3b82f6;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.25rem;
                font-weight: 600;
                display: inline-block;
                text-decoration: none;
            }
        </style>

        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #991b1b;
                background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            }
            
            /* Responsive enhancements for auth pages */
            @media (max-width: 768px) {
                .auth-card {
                    width: 90% !important;
                    max-width: 90% !important;
                    padding: 1.5rem !important;
                }
            }
            
            @media (max-width: 480px) {
                .auth-card {
                    width: 95% !important;
                    padding: 1rem !important;
                }
                
                .auth-card h1 {
                    font-size: 1.5rem !important;
                }
                
                .auth-card input {
                    font-size: 0.9rem !important;
                }
                
                .auth-card button {
                    width: 100% !important;
                }
            }
            
            /* Compact card */
            .compact-card {
                max-width: 300px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
        </style>

        <!-- Styles -->
        @livewireStyles
    </head>
    <body>
        <div class="font-sans text-gray-900 antialiased">
            {{ $slot }}
        </div>

        @livewireScripts
    </body>
</html>
