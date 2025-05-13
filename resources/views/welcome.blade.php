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
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
            }
            
            .pattern-bg {
                background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23b91c1c' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, rgba(250,204,21,0.05) 0%, rgba(185,28,28,0.05) 100%);
            }
            
            .nav-gradient {
                background: linear-gradient(90deg, #b91c1c, #991b1b);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .gold-gradient {
                background: linear-gradient(90deg, #facc15 0%, #fef3c7 100%);
            }
            
            .red-gradient {
                background: linear-gradient(90deg, #b91c1c 0%, #991b1b 100%);
            }
            
            .feature-card {
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: #facc15;
                transform: translateX(-100%);
                transition: all 0.3s ease;
            }
            
            .feature-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px -5px rgba(185, 28, 28, 0.2), 0 10px 10px -5px rgba(185, 28, 28, 0.1);
                border-color: #facc15;
            }
            
            .feature-card:hover::before {
                transform: translateX(0);
            }
            
            .animate-fadeIn {
                animation: fadeIn 0.5s ease-in;
            }
            
            @keyframes fadeIn {
                0% { opacity: 0; transform: translateY(10px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            
            .floating {
                animation: floating 3s ease-in-out infinite;
            }
            
            @keyframes floating {
                0% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
                100% { transform: translateY(0px); }
            }
            
            .section-divider {
                height: 4px;
                background: linear-gradient(90deg, transparent, #facc15, transparent);
                margin: 2rem auto;
                width: 50%;
                border-radius: 2px;
            }
            
            /* Breeze-inspired custom styles */
            .btn-primary {
                @apply inline-flex items-center px-4 py-2 bg-red-700 dark:bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white dark:text-white uppercase tracking-widest hover:bg-red-800 dark:hover:bg-red-700 focus:bg-red-800 dark:focus:bg-red-700 active:bg-red-900 dark:active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150;
            }
            
            .btn-secondary {
                @apply inline-flex items-center px-4 py-2 bg-yellow-400 dark:bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-800 uppercase tracking-widest hover:bg-yellow-500 dark:hover:bg-yellow-400 focus:bg-yellow-500 dark:focus:bg-yellow-400 active:bg-yellow-600 dark:active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150;
            }
            
            .btn-outline {
                @apply inline-flex items-center px-4 py-2 bg-transparent border border-red-700 dark:border-red-600 rounded-md font-semibold text-xs text-red-700 dark:text-red-600 uppercase tracking-widest hover:bg-red-700 hover:text-white dark:hover:bg-red-600 dark:hover:text-white focus:bg-red-700 focus:text-white dark:focus:bg-red-600 dark:focus:text-white active:bg-red-800 dark:active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150;
            }
            
            .card {
                @apply bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6;
            }
        </style>
    </head>
    <body class="antialiased pattern-bg">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav x-data="{ open: false }" class="nav-gradient border-b border-yellow-400 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <a href="{{ url('/') }}" class="flex items-center">
                                    @if(file_exists(public_path('kofa.png')))
                                        <img src="{{ asset('kofa.png') }}" alt="KofA Logo" class="h-9 w-auto">
                                    @else
                                        <div class="bg-yellow-400 p-2 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="ml-3 text-xl font-bold text-white tracking-tight">
                                        <span class="text-white">KofA</span>
                                        <span class="text-yellow-400">AMS</span>
                                    </span>
                                </a>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                                    Dashboard
                                </a>
                                <a href="#features" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                                    Features
                                </a>
                                <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                                    About
                                </a>
                            </div>
                        </div>

                        <!-- Authentication Links -->
                        <div class="hidden sm:flex sm:items-center sm:ml-6">
                            @if (Route::has('login'))
                                <div class="flex space-x-3">
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            Dashboard
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            Login
                                        </a>

                                        @if (Route::has('register'))
                                            <a href="{{ route('register') }}" class="btn-outline">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                                </svg>
                                                Register
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-yellow-300 hover:bg-red-800 focus:outline-none focus:bg-red-800 focus:text-yellow-300 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                            Dashboard
                        </a>
                        <a href="#features" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                            Features
                        </a>
                        <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-white hover:text-yellow-300 hover:border-yellow-300 focus:outline-none focus:text-yellow-300 focus:border-yellow-300 transition duration-150 ease-in-out">
                            About
                        </a>
                    </div>

                    <!-- Responsive Authentication Links -->
                    <div class="pt-4 pb-1 border-t border-yellow-400/30">
                        @if (Route::has('login'))
                            <div class="px-4 space-y-2">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="block w-full text-left px-4 py-2 bg-yellow-400 rounded-md font-medium text-sm text-gray-800 hover:bg-yellow-500 focus:outline-none focus:bg-yellow-500 transition duration-150 ease-in-out">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="block w-full text-left px-4 py-2 bg-yellow-400 rounded-md font-medium text-sm text-gray-800 hover:bg-yellow-500 focus:outline-none focus:bg-yellow-500 transition duration-150 ease-in-out">
                                        Login
                                    </a>

                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="block w-full text-left px-4 py-2 bg-transparent border border-yellow-400 rounded-md font-medium text-sm text-yellow-400 hover:bg-yellow-400 hover:text-gray-800 focus:outline-none focus:bg-yellow-400 focus:text-gray-800 transition duration-150 ease-in-out">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="relative isolate overflow-hidden bg-white pattern-bg py-16 md:py-24">
                <!-- Decorative elements -->
                <div class="absolute top-20 right-10 w-16 h-16 rounded-full bg-yellow-400 opacity-10 floating"></div>
                <div class="absolute bottom-40 left-10 w-12 h-12 rounded-full bg-red-700 opacity-10 floating" style="animation-delay: 1s;"></div>
                <div class="absolute top-40 left-20 w-8 h-8 rounded-full bg-yellow-400 opacity-10 floating" style="animation-delay: 0.5s;"></div>
                
                <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                    <div class="lg:flex lg:items-center lg:gap-x-16">
                        <!-- Left side content -->
                        <div class="lg:w-1/2 mb-10 lg:mb-0 animate-fadeIn">
                            <div class="relative">
                                <div class="absolute -left-3 -top-3 w-10 h-10 bg-yellow-400 opacity-20 rounded-full"></div>
                                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tight relative z-10">
                                    <span class="block text-red-700">KofA Attendance</span>
                                    <span class="block text-yellow-500 mt-1">Monitoring System</span>
                                </h1>
                            </div>
                            
                            <div class="mt-6 relative">
                                <div class="absolute -left-2 top-0 w-1 h-full bg-red-700 opacity-30"></div>
                                <p class="text-lg text-gray-700 pl-4 max-w-xl">
                                    A streamlined system for tracking mass attendance with QR codes. Monitor attendance, manage absences, and send notifications all in one place.
                                </p>
                            </div>
                            
                            <div class="mt-10 flex flex-wrap gap-4">
                                @if (Route::has('login'))
                                    @auth
                                        <a href="{{ url('/dashboard') }}" class="btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                            GO TO DASHBOARD
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                            </svg>
                                            GET STARTED
                                        </a>
                                        <a href="#features" class="btn-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            LEARN MORE
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side card -->
                        <div class="lg:w-1/2 mt-10 lg:mt-0">
                            <div class="card border border-yellow-400 relative overflow-hidden transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                                <!-- Decorative corner element -->
                                <div class="absolute top-0 right-0 w-12 h-12 overflow-hidden">
                                    <div class="bg-red-700 rotate-45 transform origin-bottom-left w-16 h-16 -translate-y-8 translate-x-4"></div>
                                </div>
                                
                                <div class="relative z-10">
                                    <div class="flex justify-center mb-6">
                                        <div class="p-4 bg-red-50 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <h2 class="text-2xl font-bold text-center text-gray-900 mb-4">Simple & Efficient</h2>
                                    
                                    <p class="text-gray-600 text-center mb-6">
                                        Our QR code-based system makes attendance tracking simple, accurate, and efficient. No more manual record-keeping or paperwork.
                                    </p>
                                    
                                    <div class="space-y-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-sm font-medium text-gray-900">Quick Scanning</h3>
                                                <p class="text-sm text-gray-500">Scan QR codes in seconds for instant attendance recording</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-sm font-medium text-gray-900">Real-time Updates</h3>
                                                <p class="text-sm text-gray-500">See attendance data update in real-time as members arrive</p>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-700">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <h3 class="text-sm font-medium text-gray-900">Comprehensive Reports</h3>
                                                <p class="text-sm text-gray-500">Generate detailed attendance reports with just a few clicks</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Section divider -->
                <div class="section-divider mt-16"></div>
                
                <!-- Stats section -->
                <div class="mx-auto max-w-7xl px-6 lg:px-8 mt-16">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="card border-t-4 border-red-700 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="text-4xl font-bold text-red-700 mb-2">100%</div>
                            <div class="text-gray-600">Attendance Accuracy</div>
                        </div>
                        
                        <div class="card border-t-4 border-yellow-400 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="text-4xl font-bold text-yellow-500 mb-2">90%</div>
                            <div class="text-gray-600">Time Saved</div>
                        </div>
                        
                        <div class="card border-t-4 border-red-700 text-center transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                            <div class="text-4xl font-bold text-red-700 mb-2">24/7</div>
                            <div class="text-gray-600">Data Access</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Features Section with Breeze Styling -->
            <div id="features" class="bg-white py-16 md:py-24 relative overflow-hidden">
                <!-- Decorative elements -->
                <div class="absolute -left-20 top-10 w-32 h-32 rounded-full bg-red-700 opacity-5"></div>
                <div class="absolute -right-20 bottom-10 w-32 h-32 rounded-full bg-yellow-400 opacity-5"></div>
                
                <div class="mx-auto max-w-7xl px-6 lg:px-8 relative z-10">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center justify-center mb-4">
                            <div class="h-1 w-10 bg-red-700"></div>
                            <span class="mx-4 text-sm font-bold text-red-700 uppercase tracking-widest">Powerful Features</span>
                            <div class="h-1 w-10 bg-red-700"></div>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100">Everything You Need for Attendance Management</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                        <!-- Feature 1 -->
                        <div class="card border border-yellow-400 transition-all duration-300 feature-card relative hover:shadow-lg hover:-translate-y-1">
                            <div class="absolute -top-3 -right-3 w-8 h-8 bg-red-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">1</div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-red-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-red-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75Z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-gray-100 mb-3">QR Code Scanning</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center">Generate unique QR codes for each member and scan them for quick and accurate attendance recording.</p>
                        </div>
                        
                        <!-- Feature 2 -->
                        <div class="card border border-yellow-400 transition-all duration-300 feature-card relative hover:shadow-lg hover:-translate-y-1">
                            <div class="absolute -top-3 -right-3 w-8 h-8 bg-red-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">2</div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-red-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-red-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-gray-100 mb-3">Detailed Reports</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center">Generate comprehensive attendance reports and analytics to track participation trends over time.</p>
                        </div>
                        
                        <!-- Feature 3 -->
                        <div class="card border border-yellow-400 transition-all duration-300 feature-card relative hover:shadow-lg hover:-translate-y-1">
                            <div class="absolute -top-3 -right-3 w-8 h-8 bg-red-700 rounded-full flex items-center justify-center text-white font-bold shadow-md">3</div>
                            <div class="flex justify-center mb-4">
                                <div class="p-3 bg-red-50 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-red-700">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                    </svg>
                                </div>
                            </div>
                            <h3 class="text-xl font-bold text-center text-gray-900 dark:text-gray-100 mb-3">Notifications</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-center">Send automated notifications for upcoming events and follow up with absent members.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Testimonial Section with Breeze Styling -->
            <div id="testimonials" class="bg-gray-50 dark:bg-gray-800 py-16 md:py-20">
                <div class="max-w-5xl mx-auto px-6 lg:px-8">
                    <div class="text-center mb-12">
                        <span class="text-sm font-bold text-red-700 dark:text-red-500 uppercase tracking-widest">What People Say</span>
                        <h2 class="mt-2 text-3xl md:text-4xl font-bold text-gray-900 dark:text-gray-100">Trusted by Organizations</h2>
                    </div>
                    
                    <div class="card border-l-4 border-yellow-400 transition-all duration-300 hover:shadow-lg">
                        <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                            <div class="flex-shrink-0">
                                <div class="w-20 h-20 bg-red-700 rounded-full flex items-center justify-center text-white text-2xl font-bold">KP</div>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-400 dark:text-yellow-500 mb-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                                <p class="text-gray-700 dark:text-gray-300 text-lg italic mb-4">"The KofA Attendance Monitoring System has revolutionized how we track attendance at our parish events. The QR code system is incredibly easy to use, and the reporting features give us valuable insights we never had before."</p>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-gray-100">Fr. Kevin Peterson</h4>
                                    <p class="text-gray-600 dark:text-gray-400">Parish Priest, St. Michael's Church</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer with Breeze Styling -->
            <footer class="bg-gray-800 text-white mt-auto">
                <div class="max-w-7xl mx-auto px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Logo and about -->
                        <div>
                                <div class="flex items-center mb-4">
                                    <div class="bg-yellow-400 p-2 rounded-md mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-white">KofA <span class="text-yellow-400">AMS</span></h3>
                                </div>
                                <p class="text-gray-400 text-sm mb-4">
                                    Streamlined attendance monitoring system for organizations that need reliable tracking solutions.
                                </p>
                            </div>
                            
                            <!-- Quick links -->
                            <div>
                                <h4 class="text-md font-bold mb-4 border-b border-gray-700 pb-2 text-white">Quick Links</h4>
                                <ul class="space-y-2">
                                    <li><a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors duration-300">Dashboard</a></li>
                                    <li><a href="#features" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors duration-300">Features</a></li>
                                    <li><a href="#testimonials" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors duration-300">Testimonials</a></li>
                                    <li><a href="#" class="text-gray-400 hover:text-yellow-400 text-sm transition-colors duration-300">Contact</a></li>
                                </ul>
                            </div>
                            
                            <!-- Contact -->
                            <div>
                                <h4 class="text-md font-bold mb-4 border-b border-gray-700 pb-2 text-white">Contact Us</h4>
                                <ul class="space-y-2">
                                    <li class="flex items-center text-gray-400 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        info@kofaams.com
                                    </li>
                                    <li class="flex items-center text-gray-400 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        (123) 456-7890
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-700 mt-8 pt-6 flex flex-col md:flex-row justify-between items-center">
                            <p class="text-gray-400 text-sm mb-4 md:mb-0">&copy; {{ date('Y') }} KofA AMS. All rights reserved.</p>
                            <div class="flex space-x-4">
                                <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                                    </svg>
                                </a>
                                <a href="#" class="text-gray-400 hover:text-yellow-400 transition-colors duration-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
