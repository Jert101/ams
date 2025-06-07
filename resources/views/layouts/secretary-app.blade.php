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
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            primary: '#4f46e5',
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
                --primary-color: #4f46e5; /* Indigo for Secretary */
                --secondary-color: #facc15; /* Golden Yellow */
                --white-color: #ffffff; /* White */
                --dark-primary: #3730a3; /* Darker Indigo */
                --light-secondary: #fef3c7; /* Light Gold */
                --sidebar-width: 18rem;
                --header-height: 4rem;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                scroll-behavior: smooth;
                overflow-x: hidden;
                background-color: #f9fafb;
                color: #111827;
            }
            
            .pattern-bg {
                background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%234f46e5' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            }
            
            /* Card styles */
            .card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                padding: 1.5rem;
                transition: all 0.3s;
            }
            
            .card-hover:hover {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                transform: translateY(-0.25rem);
            }
            
            .section-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #4338ca;
                margin-bottom: 1rem;
            }
            
            /* Fixed sidebar positioning */
            .sidebar-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 18rem; /* 288px = 18rem, equivalent to w-72 */
                height: 100vh;
                z-index: 30;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                background-color: white;
                overflow-y: auto;
                transition: transform 0.3s ease-in-out;
            }
            
            /* Main content positioning */
            .content-wrapper {
                margin-left: 18rem; /* 288px = 18rem, equivalent to w-72 of the sidebar */
                width: calc(100% - 18rem);
                min-height: 100vh;
                padding: 0;
                box-sizing: border-box;
                position: relative;
                z-index: 10;
                transition: margin-left 0.3s ease;
            }
            
            /* Additional fixes to ensure no gap */
            .min-h-screen {
                overflow-x: hidden;
            }

            /* Mobile responsiveness */
            @media (max-width: 1024px) {
                .sidebar-container {
                    transform: translateX(-100%);
                    box-shadow: none;
                }
                
                .sidebar-container.show {
                    transform: translateX(0);
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                }
                
                .content-wrapper {
                    margin-left: 0;
                    width: 100%;
                    padding-top: 3.5rem; /* Space for mobile toggle button */
                }
                
                .sidebar-toggle {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    position: fixed;
                    top: 1rem;
                    left: 1rem;
                    z-index: 40;
                    background-color: var(--primary-color);
                    color: white;
                    border-radius: 0.375rem;
                    padding: 0.5rem;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    width: 2.5rem;
                    height: 2.5rem;
                    border: none;
                    cursor: pointer;
                }
                
                .page-title {
                    padding-left: 3.5rem; /* Space for toggle button */
                }
                
                header {
                    padding-left: 3.5rem !important;
                }
            }
            
            @media (max-width: 640px) {
                .content-wrapper {
                    padding: 3.5rem 0.75rem 1rem 0.75rem;
                }
                
                .user-profile-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
                
                .user-profile-info {
                    margin-top: 0.5rem;
                    margin-left: 0 !important;
                }
                
                header h2 {
                    font-size: 1.25rem;
                }
            }
            
            .sidebar-toggle {
                display: none;
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }
            
            ::-webkit-scrollbar-thumb {
                background: #d1d5db;
                border-radius: 3px;
            }
            
            ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
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
        
        <!-- Sidebar - Fixed position on the left -->
        <div class="sidebar-container" id="sidebar">
            @include('layouts.sidebar')
        </div>
        
        <!-- Main Content - With appropriate margin to accommodate sidebar -->
        <div class="content-wrapper">
            <div class="container mx-auto px-4 py-4 w-full">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-yellow-400">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center flex-wrap">
                                <h2 class="font-semibold text-xl text-indigo-700 leading-tight mb-2 sm:mb-0">
                                    {{ $header }}
                                </h2>
                                
                                <!-- User Profile -->
                                <div class="flex items-center user-profile-header">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ Auth::user()->name }}" />
                                    <div class="ml-2 user-profile-info">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->role->name ?? 'Secretary' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                @else
                    <header class="bg-white shadow-sm border-b border-yellow-400 mb-6">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            <div class="flex justify-between items-center flex-wrap">
                                <h2 class="font-semibold text-xl text-indigo-700 leading-tight mb-2 sm:mb-0">
                                    Secretary Dashboard
                                </h2>
                                
                                <!-- User Profile -->
                                <div class="flex items-center user-profile-header">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ Auth::user()->name }}" />
                                    <div class="ml-2 user-profile-info">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->role->name ?? 'Secretary' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="py-6 px-4">
                    @yield('content')
                </main>
            </div>
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
                const contentWrapper = document.querySelector('.content-wrapper');
                
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside of it
                document.addEventListener('click', function(event) {
                    if (!sidebar.contains(event.target) && 
                        !sidebarToggle.contains(event.target) && 
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
                
                // Close sidebar on window resize if screen becomes larger
                window.addEventListener('resize', function() {
                    if (window.innerWidth > 1024) {
                        sidebar.classList.remove('show');
                    }
                });
                
                // Dropdown toggles
                const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
                dropdownToggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function() {
                        const dropdown = this.nextElementSibling;
                        dropdown.classList.toggle('show');
                    });
                });
            });
        </script>
    </body>
</html> 