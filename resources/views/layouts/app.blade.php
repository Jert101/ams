<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
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
                            primary: '#b91c1c',
                            secondary: '#eab308'
                        }
                    }
                }
            }
        </script>
        
        <!-- jQuery (needed for some Bootstrap components) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Global Responsive CSS -->
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f3f4f6;
                overflow-x: hidden;
            }
            
            /* Sidebar styles */
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                width: 250px;
                background-color: #b91c1c;
                color: white;
                z-index: 40;
                transition: transform 0.3s ease;
                overflow-y: auto;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }
            
            /* Content styles */
            .main-content {
                margin-left: 250px;
                padding: 1rem;
                transition: margin-left 0.3s ease;
                min-height: 100vh;
            }
            
            /* Mobile styles */
            @media (max-width: 768px) {
                .sidebar {
                    transform: translateX(-100%);
                    width: 85%;
                    max-width: 300px;
                }
                
                .sidebar.show {
                    transform: translateX(0);
                }
                
                .main-content {
                    margin-left: 0;
                    padding-top: 3.5rem;
                }
                
                /* Add overlay when sidebar is open */
                .sidebar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: rgba(0,0,0,0.5);
                    z-index: 35;
                    display: none;
                }
                
                .sidebar-overlay.show {
                    display: block;
                }
            }
            
            /* Toggle button */
            .sidebar-toggle {
                position: fixed;
                top: 0.75rem;
                left: 0.75rem;
                z-index: 45;
                display: none;
                background-color: #b91c1c;
                color: white;
                border: none;
                border-radius: 0.375rem;
                padding: 0.5rem;
                width: 2.5rem;
                height: 2.5rem;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            
            @media (max-width: 768px) {
                .sidebar-toggle {
                    display: flex;
                }
            }
            
            /* Print styles */
            @media print {
                .sidebar, .sidebar-toggle {
                    display: none !important;
                }
                
                .main-content {
                    margin-left: 0 !important;
                    padding: 0 !important;
                }
                
                body {
                    background-color: white !important;
                }
            }
            
            /* Tailwind Utility Classes */
            .font-semibold {
                font-weight: 600;
            }
            
            .text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }
            
            .text-gray-800 {
                color: #1f2937;
            }
            
            .leading-tight {
                line-height: 1.25;
            }
            
            .py-12 {
                padding-top: 3rem;
                padding-bottom: 3rem;
            }
            
            .max-w-7xl {
                max-width: 80rem;
            }
            
            .mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            
            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }
            
            .space-y-6 > * + * {
                margin-top: 1.5rem;
            }
            
            .bg-white {
                background-color: #ffffff;
            }
            
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            
            .rounded-lg {
                border-radius: 0.5rem;
            }
            
            .mb-6 {
                margin-bottom: 1.5rem;
            }
            
            .p-6 {
                padding: 1.5rem;
            }
            
            .p-4 {
                padding: 1rem;
            }
            
            .p-3 {
                padding: 0.75rem;
            }
            
            .grid {
                display: grid;
            }
            
            .grid-cols-1 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .md\:grid-cols-2 {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            
            .lg\:grid-cols-4 {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
            
            .gap-6 {
                gap: 1.5rem;
            }
            
            .flex {
                display: flex;
            }
            
            .flex-col {
                flex-direction: column;
            }
            
            .items-center {
                align-items: center;
            }
            
            .justify-center {
                justify-content: center;
            }
            
            .justify-between {
                justify-content: space-between;
            }
            
            .rounded-full {
                border-radius: 9999px;
            }
            
            .mr-4 {
                margin-right: 1rem;
            }
            
            .h-6 {
                height: 1.5rem;
            }
            
            .w-6 {
                width: 1.5rem;
            }
            
            .text-sm {
                font-size: 0.875rem;
            }
            
            .font-medium {
                font-weight: 500;
            }
            
            .text-gray-600 {
                color: #4b5563;
            }
            
            .text-2xl {
                font-size: 1.5rem;
            }
            
            .text-gray-900 {
                color: #111827;
            }
            
            .bg-blue-100 {
                background-color: #dbeafe;
            }
            
            .text-blue-600 {
                color: #2563eb;
            }
            
            .bg-green-100 {
                background-color: #d1fae5;
            }
            
            .text-green-600 {
                color: #059669;
            }
            
            .bg-purple-100 {
                background-color: #f3e8ff;
            }
            
            .text-purple-600 {
                color: #9333ea;
            }
            
            .bg-yellow-100 {
                background-color: #fef3c7;
            }
            
            .text-yellow-600 {
                color: #d97706;
            }
            
            .shadow {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            
            .text-lg {
                font-size: 1.125rem;
            }
            
            .font-bold {
                font-weight: 700;
            }
            
            .mb-4 {
                margin-bottom: 1rem;
            }
            
            .bg-red-700 {
                background-color: #b91c1c;
            }
            
            .text-white {
                color: #ffffff;
            }
            
            .hover\:bg-red-800:hover {
                background-color: #991b1b;
            }
            
            .mt-6 {
                margin-top: 1.5rem;
            }
            
            .lg\:col-span-2 {
                grid-column: span 2 / span 2;
            }
            
            .overflow-x-auto {
                overflow-x: auto;
            }
            
            .min-w-full {
                min-width: 100%;
            }
            
            .divide-y {
                border-top-width: 1px;
                border-bottom-width: 1px;
            }
            
            .divide-gray-200 {
                border-color: #e5e7eb;
            }
            
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            
            .px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .py-3 {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
            
            .text-left {
                text-align: left;
            }
            
            .text-xs {
                font-size: 0.75rem;
            }
            
            .text-gray-500 {
                color: #6b7280;
            }
            
            .uppercase {
                text-transform: uppercase;
            }
            
            .tracking-wider {
                letter-spacing: 0.05em;
            }
            
            .whitespace-nowrap {
                white-space: nowrap;
            }
            
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .h-10 {
                height: 2.5rem;
            }
            
            .w-10 {
                width: 2.5rem;
            }
            
            .ml-4 {
                margin-left: 1rem;
            }
            
            .px-2 {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .inline-flex {
                display: inline-flex;
            }
            
            .leading-5 {
                line-height: 1.25rem;
            }
            
            .rounded-full {
                border-radius: 9999px;
            }
            
            .bg-green-100 {
                background-color: #d1fae5;
            }
            
            .text-green-800 {
                color: #065f46;
            }
            
            .bg-yellow-100 {
                background-color: #fef3c7;
            }
            
            .text-yellow-800 {
                color: #92400e;
            }
            
            .text-center {
                text-align: center;
            }
            
            .py-6 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }
        </style>
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <!-- Mobile Sidebar Toggle Button -->
        <button id="sidebarToggle" class="sidebar-toggle">
            <i class="bi bi-list text-xl"></i>
        </button>
        
        <!-- Sidebar Overlay (for mobile) -->
        <div id="sidebarOverlay" class="sidebar-overlay"></div>
        
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <div class="p-4">
                <div class="flex items-center justify-between mb-6">
                    <a href="/" class="flex items-center">
                        <img src="{{ asset('kofa.png') }}" alt="Logo" class="h-10 w-10 mr-2">
                        <span class="text-white font-bold text-xl">KofA AMS</span>
                    </a>
                    <button id="closeSidebar" class="text-white md:hidden">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                
                <!-- Sidebar navigation links -->
                @include('layouts.navigation')
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm rounded-lg mb-6">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $header }}
                            </h2>
                            
                            @auth
                                <!-- User Profile -->
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ Auth::user()->name }}" />
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-500">{{ Auth::user()->role->name ?? 'User' }}</div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </header>
            @endisset
            
            <!-- Facial recognition modal removed -->

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-lg" role="alert">
                    {{ session('warning') }}
                </div>
            @endif

            @if (session('info'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4 rounded-lg" role="alert">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>

        <!-- Livewire Scripts -->
        @livewireScripts
        
        <!-- Additional Scripts -->
        @stack('scripts')
        
        <!-- Script to show fallback content if React fails -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Check if React root exists and show fallback content after a timeout
                setTimeout(function() {
                    const reactRoots = document.querySelectorAll('[data-react-root]');
                    reactRoots.forEach(function(root) {
                        const component = root.getAttribute('data-component');
                        const fallback = document.getElementById(component.toLowerCase() + '-fallback-content');
                        if (fallback) {
                            fallback.style.display = 'block';
                        }
                    });
                }, 100);
            });
        </script>
        
        <!-- JavaScript for Sidebar Toggle -->
        <script>
            $(document).ready(function() {
                // Toggle sidebar
                $('#sidebarToggle').click(function() {
                    $('#sidebar').toggleClass('show');
                    $('#sidebarOverlay').toggleClass('show');
                    $('body').toggleClass('overflow-hidden');
                });
                
                // Close sidebar
                $('#closeSidebar, #sidebarOverlay').click(function() {
                    $('#sidebar').removeClass('show');
                    $('#sidebarOverlay').removeClass('show');
                    $('body').removeClass('overflow-hidden');
                });
                
                // Close sidebar on window resize (if in mobile view and window width becomes larger)
                $(window).resize(function() {
                    if ($(window).width() > 768) {
                        $('#sidebar').removeClass('show');
                        $('#sidebarOverlay').removeClass('show');
                        $('body').removeClass('overflow-hidden');
                    }
                });
            });
        </script>
    </body>
</html> 