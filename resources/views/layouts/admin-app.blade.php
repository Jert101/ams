<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Scripts and Styles -->
        @php
            $manifestPath = public_path('build/manifest.json');
            $manifest = file_exists($manifestPath) ? json_decode(file_get_contents($manifestPath), true) : null;
        @endphp

        @if ($manifest)
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback for production if Vite manifest is not found -->
            <link rel="stylesheet" href="{{ asset('build/assets/app-DkuVia4n.css') }}">
            <script src="{{ asset('build/assets/app-CdRc4Ovg.js') }}" defer></script>
        @endif

        @livewireStyles

        <style>
            :root {
                --primary-color: #b91c1c;
                --secondary-color: #eab308;
                --white-color: #ffffff;
                --dark-primary: #991b1b;
                --light-secondary: #fef3c7;
                --sidebar-width: 18rem;
                --header-height: 4rem;
            }

            /* Base styles */
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f8fafc;
                overflow-x: hidden;
            }

            /* Sidebar styles */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                width: var(--sidebar-width);
                height: 100vh;
                background-color: white;
                z-index: 40;
                transition: transform 0.3s ease-in-out;
                box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
                overflow-y: auto;
            }

            /* Content wrapper styles */
            .content-wrapper {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                transition: margin-left 0.3s ease-in-out;
                padding: 1.5rem;
            }

            /* Hamburger menu button */
            .sidebar-toggle {
                display: none;
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 50;
                padding: 0.5rem;
                background-color: var(--primary-color);
                color: white;
                border: none;
                border-radius: 0.375rem;
                cursor: pointer;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* Sidebar overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 30;
                opacity: 0;
                transition: opacity 0.3s ease-in-out;
            }

            /* Mobile styles */
            @media (max-width: 1024px) {
                .sidebar {
                    transform: translateX(-100%);
                }

                .sidebar.show {
                    transform: translateX(0);
                }

                .content-wrapper {
                    margin-left: 0;
                    padding-top: 4rem;
                }

                .sidebar-toggle {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .sidebar-overlay {
                    display: block;
                }

                .sidebar-overlay.show {
                    opacity: 1;
                }

                /* Fix content spacing */
                .container {
                    padding-left: 1rem;
                    padding-right: 1rem;
                }

                /* Stack buttons on mobile */
                .button-group {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }

                .button-group > * {
                    width: 100%;
                }
            }

            /* Small screen optimizations */
            @media (max-width: 640px) {
                .content-wrapper {
                    padding: 4rem 0.75rem 1.5rem 0.75rem;
                }

                /* Improve table responsiveness */
                .table-responsive {
                    margin-left: -0.75rem;
                    margin-right: -0.75rem;
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                    width: calc(100% + 1.5rem);
                }

                /* Adjust card spacing */
                .card {
                    margin-bottom: 1rem;
                    padding: 1rem !important;
                }

                /* Improve form layouts */
                .form-group {
                    margin-bottom: 1rem;
                }

                .form-label {
                    margin-bottom: 0.25rem;
                }

                /* Stack flex items */
                .flex-stack-sm {
                    flex-direction: column !important;
                    gap: 0.75rem !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <!-- Mobile Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>

        <div class="min-h-screen bg-gray-50">
            <!-- Sidebar -->
            <aside id="sidebar" class="sidebar">
                @include('layouts.sidebar')
            </aside>

            <!-- Main Content -->
            <div id="content-wrapper" class="content-wrapper">
                <!-- Page Content -->
                <main>
                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebarOverlay = document.getElementById('sidebar-overlay');
                const contentWrapper = document.getElementById('content-wrapper');

                function toggleSidebar() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                    document.body.classList.toggle('overflow-hidden');
                }

                // Toggle sidebar on button click
                sidebarToggle.addEventListener('click', toggleSidebar);

                // Close sidebar when clicking overlay
                sidebarOverlay.addEventListener('click', function() {
                    if (sidebar.classList.contains('show')) {
                        toggleSidebar();
                    }
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.classList.remove('overflow-hidden');
                    }
                });

                // Fix flex layouts on small screens
                function adjustFlexLayouts() {
                    if (window.innerWidth <= 640) {
                        document.querySelectorAll('.flex.justify-between:not(.flex-col)').forEach(flex => {
                            flex.classList.add('flex-stack-sm');
                        });

                        document.querySelectorAll('.button-group:not(.flex-col)').forEach(group => {
                            group.classList.add('flex-col');
                        });
                    } else {
                        document.querySelectorAll('.flex-stack-sm').forEach(flex => {
                            flex.classList.remove('flex-stack-sm');
                        });

                        document.querySelectorAll('.button-group.flex-col').forEach(group => {
                            group.classList.remove('flex-col');
                        });
                    }
                }

                // Run on page load and resize
                adjustFlexLayouts();
                window.addEventListener('resize', adjustFlexLayouts);
            });
        </script>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
