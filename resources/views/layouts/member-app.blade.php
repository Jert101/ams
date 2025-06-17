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
                background-color: #f8fafc;
                color: #111827;
            }
            
            .pattern-bg {
                background-color: #f8fafc;
                background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23b91c1c' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            }
            
            /* Utility Classes */
            .bg-white {
                background-color: white;
            }
            
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            
            .shadow-lg {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            
            .fixed {
                position: fixed;
            }
            
            .top-0 {
                top: 0;
            }
            
            .left-0 {
                left: 0;
            }
            
            .h-full {
                height: 100%;
            }
            
            .min-h-screen {
                min-height: 100vh;
            }
            
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .h-6 {
                height: 1.5rem;
            }
            
            .w-6 {
                width: 1.5rem;
            }
            
            /* Sidebar Styles */
            .sidebar {
                width: 280px;
                transition: all 0.3s;
                z-index: 40;
                background-color: white;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                height: 100vh;
                overflow-y: auto;
            }
            
            .sidebar-collapsed {
                margin-left: -280px;
            }
            
            .content-area {
                margin-left: 280px;
                transition: all 0.3s;
            }
            
            .content-area-expanded {
                margin-left: 0;
            }
            
            /* Mobile Sidebar Toggle */
            .sidebar-toggle {
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 50;
                display: none;
                padding: 0.5rem;
                background-color: var(--primary-color);
                color: white;
                border-radius: 0.375rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #b91c1c;
                border-radius: 3px;
            }
            
            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #991b1b;
            }
            
            /* Responsive Styles */
            @media (max-width: 768px) {
                .sidebar {
                    position: fixed;
                    height: 100vh;
                    margin-left: -280px;
                }
                
                .sidebar-expanded {
                    margin-left: 0;
                }
                
                .content-area {
                    margin-left: 0;
                }
                
                .sidebar-toggle {
                    display: block;
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
        
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar fixed top-0 left-0 h-full overflow-y-auto custom-scrollbar">
            @include('layouts.sidebar')
        </div>
        
        <!-- Main Content -->
        <div id="content-area" class="content-area min-h-screen">
            <!-- Header with User Profile -->
            <header class="bg-white shadow-sm p-2">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center">
                        <h2 class="font-semibold text-sm text-gray-800 leading-tight">
                            {{ $header ?? 'Member Dashboard' }}
                        </h2>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <main class="py-6 px-4">
                @yield('content')
            </main>
        </div>
        
        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Additional Scripts -->
        @stack('scripts')
        
        <!-- Sidebar Toggle Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebarToggle = document.getElementById('sidebar-toggle');
                const sidebar = document.getElementById('sidebar');
                const contentArea = document.getElementById('content-area');
                let sidebarOpen = false;
                function openSidebar() {
                    sidebar.classList.add('sidebar-expanded');
                    sidebarOpen = true;
                }
                function closeSidebar() {
                    sidebar.classList.remove('sidebar-expanded');
                    sidebarOpen = false;
                }
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (sidebarOpen) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
                // Close sidebar when clicking outside
                document.addEventListener('click', function(event) {
                    if (sidebarOpen && !sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        closeSidebar();
                    }
                });
            });
        </script>
    </body>
</html>
