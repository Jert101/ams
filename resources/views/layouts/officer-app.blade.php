<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
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
                transition: all 0.3s;
                background-color: white;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                height: 100vh;
                overflow-y: auto;
            }
            
            .sidebar-collapsed {
                width: 0;
                overflow: hidden;
            }
            
            .sidebar-toggle {
                position: fixed;
                top: 1rem;
                left: 1rem;
                z-index: 50;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 2.5rem;
                height: 2.5rem;
                background-color: var(--primary-color);
                border-radius: 0.375rem;
                color: white;
                cursor: pointer;
                transition: all 0.3s;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            
            .sidebar-toggle:hover {
                background-color: var(--dark-primary);
            }
            
            @media (min-width: 768px) {
                .sidebar-toggle {
                    display: none;
                }
            }
            
            .main-content {
                transition: all 0.3s;
                flex: 1;
                overflow-y: auto;
            }
            
            @media (max-width: 767px) {
                .sidebar {
                    position: fixed;
                    z-index: 40;
                    height: 100vh;
                    transform: translateX(-100%);
                }
                
                .sidebar.show {
                    transform: translateX(0);
                }
                
                .main-content {
                    margin-left: 0 !important;
                }
            }
            
            /* Utility classes */
            .flex {
                display: flex;
            }
            
            .flex-1 {
                flex: 1 1 0%;
            }
            
            .h-screen {
                height: 100vh;
            }
            
            .bg-white {
                background-color: white;
            }
            
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            
            .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .shadow {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            }
            
            .hidden {
                display: none;
            }
            
            .md\:block {
                display: block;
            }
            
            .md\:ml-\[280px\] {
                margin-left: 280px;
            }
            
            .max-w-7xl {
                max-width: 80rem;
            }
            
            .mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .py-6 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }
            
            .sm\:px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }
            
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
            
            .flex {
                display: flex;
            }
            
            .justify-between {
                justify-content: space-between;
            }
            
            .items-center {
                align-items: center;
            }
            
            .relative {
                position: relative;
            }
            
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            
            .font-medium {
                font-weight: 500;
            }
            
            .text-gray-700 {
                color: #374151;
            }
            
            .hover\:text-gray-900:hover {
                color: #111827;
            }
            
            .transition {
                transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 150ms;
            }
            
            .duration-150 {
                transition-duration: 150ms;
            }
            
            .ease-in-out {
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .h-8 {
                height: 2rem;
            }
            
            .w-8 {
                width: 2rem;
            }
            
            .rounded-full {
                border-radius: 9999px;
            }
            
            .object-cover {
                object-fit: cover;
            }
            
            .ml-2 {
                margin-left: 0.5rem;
            }
            
            .text-gray-900 {
                color: #111827;
            }
            
            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }
            
            .text-gray-500 {
                color: #6b7280;
            }
            
            .ml-1 {
                margin-left: 0.25rem;
            }
            
            .fill-current {
                fill: currentColor;
            }
            
            .h-4 {
                height: 1rem;
            }
            
            .w-4 {
                width: 1rem;
            }
            
            .absolute {
                position: absolute;
            }
            
            .right-0 {
                right: 0;
            }
            
            .mt-2 {
                margin-top: 0.5rem;
            }
            
            .w-48 {
                width: 12rem;
            }
            
            .rounded-md {
                border-radius: 0.375rem;
            }
            
            .shadow-lg {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            }
            
            .py-1 {
                padding-top: 0.25rem;
                padding-bottom: 0.25rem;
            }
            
            .ring-1 {
                --tw-ring-offset-shadow: var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
                --tw-ring-shadow: var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);
                box-shadow: var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow, 0 0 #0000);
            }
            
            .ring-black {
                --tw-ring-opacity: 1;
                --tw-ring-color: rgba(0, 0, 0, var(--tw-ring-opacity));
            }
            
            .ring-opacity-5 {
                --tw-ring-opacity: 0.05;
            }
            
            .block {
                display: block;
            }
            
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            
            .text-gray-400 {
                color: #9ca3af;
            }
            
            .border-t {
                border-top-width: 1px;
            }
            
            .border-gray-100 {
                border-color: #f3f4f6;
            }
            
            .hover\:bg-gray-100:hover {
                background-color: #f3f4f6;
            }
            
            .h-6 {
                height: 1.5rem;
            }
            
            .w-6 {
                width: 1.5rem;
            }
            
            .overflow-y-auto {
                overflow-y: auto;
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
        
        <div class="flex h-screen bg-gray-50">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar hidden md:block">
                @include('layouts.sidebar')
            </div>
            
            <!-- Main Content -->
            <div id="main-content" class="main-content md:ml-[280px]">
                <!-- Page Heading -->
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between items-center">
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ $header ?? 'Officer Dashboard' }}
                            </h2>
                            
                            <!-- User Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none transition duration-150 ease-in-out">
                                    <div class="flex items-center">
                                        <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url ?? asset('img/defaults/user.svg') }}" alt="{{ Auth::user()->name }}" />
                                        <div class="ml-2">
                                            <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                            <div class="text-xs text-gray-500">{{ Auth::user()->role->name ?? 'Officer' }}</div>
                                        </div>
                                    </div>
                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                                
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5" style="display: none;">
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Manage Account') }}
                                    </div>
                                    
                                    <div class="border-t border-gray-100"></div>
                                    
                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>
                
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
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('main-content');
                const sidebarToggle = document.getElementById('sidebar-toggle');
                
                if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                        sidebar.classList.toggle('show');
                    });
                }
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (window.innerWidth <= 767) {
                        if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target) && sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                        }
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
