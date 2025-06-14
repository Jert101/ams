<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KofA Attendance Monitoring System') }}</title>

        <!-- PWA Meta Tags -->
        <meta name="theme-color" content="#000000">
        <meta name="description" content="CKP-KofA Network">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="apple-mobile-web-app-title" content="CKP-KofA">
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#b91c1c',
                            secondary: '#facc15'
                        }
                    }
                }
            }
        </script>
        
        <!-- jQuery (needed for some Bootstrap components) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Global Responsive CSS -->
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        
        <style>
            :root {
                --primary-color: #b91c1c; /* Red */
                --secondary-color: #facc15; /* Golden Yellow */
                --white-color: #ffffff; /* White */
                --dark-primary: #991b1b; /* Darker Red */
                --light-secondary: #fef3c7; /* Light Gold */
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                scroll-behavior: smooth;
                background-color: #f9fafb;
                color: #111827;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            .pattern-bg {
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23b91c1c' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            /* Sidebar styles */
            .sidebar {
                width: 280px;
                transition: all 0.3s ease-in-out;
                background-color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                height: 100vh;
                overflow-y: auto;
                position: fixed;
                z-index: 40;
            }
            
            .sidebar-collapsed {
                width: 0;
                overflow: hidden;
            }
            
            .sidebar-toggle {
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 100;
                background-color: var(--primary-color);
                border-radius: 0.375rem;
                padding: 0.5rem;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
                border: none;
                display: none;
                cursor: pointer;
                color: white;
            }
            
            .sidebar-toggle:hover {
                background-color: var(--dark-primary);
            }
            
            @media (max-width: 1023px) {
                .sidebar-toggle {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            }
            
            .main-content {
                transition: margin-left 0.3s ease;
                flex: 1;
                overflow-y: auto;
                margin-left: 280px;
                width: calc(100% - 280px);
            }
            
            @media (max-width: 1023px) {
                .sidebar {
                    transform: translateX(-100%);
                }
                
                .sidebar.show {
                    transform: translateX(0);
                }
                
                .main-content {
                    margin-left: 0;
                    width: 100%;
                    padding-top: 3rem;
                }
                
                .user-profile-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .user-profile-info {
                    margin-left: 0;
                    margin-top: 0.5rem;
                }
            }
            
            /* Overlay for mobile sidebar */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 30;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .sidebar-overlay.show {
                display: block;
                opacity: 1;
            }
            
            /* Card styles */
            .card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                padding: 1.5rem;
                transition: all 0.3s;
            }
            
            .card:hover {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            
            /* Table styles */
            .table-container {
                overflow-x: auto;
                margin-bottom: 1rem;
            }

            /* Improved mobile buttons */
            @media (max-width: 640px) {
                .button-group {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                
                .button-group > * {
                    width: 100%;
                }
            }
        </style>
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased pattern-bg bg-gray-50">
        <!-- Mobile Sidebar Toggle Button -->
        <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay" class="sidebar-overlay"></div>
        
        <div class="flex h-screen bg-gray-50">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar">
                @include('layouts.sidebar')
            </div>
            
            <!-- Main Content -->
            <div id="main-content" class="main-content">
                <!-- Page Heading -->
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center flex-wrap">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2 sm:mb-0">
                                {{ $header ?? 'Officer Dashboard' }}
                            </h2>
                            <!-- User Profile removed as per request -->
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </main>
            </div>
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Additional Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebar = document.getElementById('sidebar');
                const sidebarOverlay = document.getElementById('sidebar-overlay');
                
                function toggleSidebar() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                    document.body.classList.toggle('overflow-hidden');
                }
                
                sidebarToggle.addEventListener('click', toggleSidebar);
                
                // Close sidebar when clicking outside
                sidebarOverlay.addEventListener('click', function() {
                    if (sidebar.classList.contains('show')) {
                        toggleSidebar();
                    }
                });
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024 && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.classList.remove('overflow-hidden');
                    }
                });
            });
        </script>
        
        <!-- Responsive Tables Script -->
        <script src="{{ asset('js/responsive-tables.js') }}"></script>
        
        @stack('scripts')
    </body>
</html>
