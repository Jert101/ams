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
        
        <!-- Bootstrap CSS and JS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
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
        
        <!-- Global Responsive CSS -->
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        
        <!-- jQuery (needed for some Bootstrap components) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <style>
            /* Variables */
            :root {
                --primary-color: #b91c1c; /* Red */
                --secondary-color: #eab308; /* Golden Yellow */
                --white-color: #ffffff; /* White */
                --dark-primary: #991b1b; /* Darker Red */
                --light-secondary: #fef3c7; /* Light Gold */
                --sidebar-width: 18rem;
                --header-height: 4rem;
            }
            
            body {
                font-family: 'Figtree', sans-serif;
                scroll-behavior: smooth;
                overflow-x: hidden;
                background-color: #f8fafc;
            }
            
            .pattern-bg {
                background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23b91c1c' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
            }
            
            /* Sidebar styles */
            .sidebar-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 18rem; /* 288px = 18rem, equivalent to w-72 */
                height: 100vh;
                z-index: 30;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
                transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
            }
            
            /* Mobile responsiveness */
            @media (max-width: 1024px) {
                .sidebar-container {
                    transform: translateX(-100%);
                    transition: transform 0.3s ease-in-out;
                    box-shadow: none;
                    width: 18rem;
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 1050;
                }
                
                .sidebar-container.show {
                    transform: translateX(0);
                    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
                }
                
                .content-wrapper {
                    margin-left: 0;
                    width: 100%;
                    transition: margin-left 0.3s ease-in-out;
                    padding-top: 3.5rem; /* Space for mobile toggle button */
                }
                
                .sidebar-toggle {
                    display: flex !important;
                    justify-content: center;
                    align-items: center;
                    position: fixed;
                    top: 1rem;
                    left: 1rem;
                    z-index: 1051;
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
            }
            
            @media (max-width: 640px) {
                .content-wrapper {
                    padding: 3.5rem 0.75rem 1rem 0.75rem;
                }
                
                .card, .dashboard-card {
                    margin-bottom: 1rem;
                    overflow-x: auto;
                }
                
                .button-container {
                    flex-direction: column;
                }
                
                .button-container > * {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                /* Responsive table styles */
                .table-responsive {
                    display: block;
                    width: 100%;
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
            }
            
            @media (max-width: 480px) {
                .table-responsive {
                    font-size: 0.875rem;
                }
                
                .pagination {
                    font-size: 0.875rem;
                    flex-wrap: wrap;
                    justify-content: center;
                }
                
                /* Stack action buttons vertically on very small screens */
                .btn-group {
                    display: flex;
                    flex-direction: column;
                }
                
                .btn-group .btn {
                    margin-bottom: 0.25rem;
                    width: 100%;
                }
            }
            
            .sidebar-toggle {
                display: none;
            }
            
            /* Table Styles */
            table {
                width: 100%;
                border-collapse: collapse;
            }
            
            th {
                background-color: #f9fafb;
                font-weight: 600;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #6b7280;
                text-align: left;
                padding: 0.75rem 1.5rem;
            }
            
            td {
                padding: 1rem 1.5rem;
                font-size: 0.875rem;
                border-bottom: 1px solid #e5e7eb;
            }
            
            tr:hover {
                background-color: #f9fafb;
            }
            
            /* Button Styles */
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.5rem 1rem;
                font-weight: 600;
                border-radius: 0.375rem;
                transition: all 0.15s ease-in-out;
                cursor: pointer;
            }
            
            .btn-primary {
                background-color: #b91c1c;
                color: white;
            }
            
            .btn-primary:hover {
                background-color: #991b1b;
            }
            
            .btn-secondary {
                background-color: #f3f4f6;
                color: #374151;
            }
            
            .btn-secondary:hover {
                background-color: #e5e7eb;
            }
            
            .btn-danger {
                background-color: #ef4444;
                color: white;
            }
            
            .btn-danger:hover {
                background-color: #dc2626;
            }
            
            /* Card Styles */
            .card {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
                padding: 1.5rem;
            }
            
            /* Form Styles */
            .form-control {
                width: 100%;
                padding: 0.5rem 0.75rem;
                border: 1px solid #d1d5db;
                border-radius: 0.375rem;
                color: #1f2937;
                font-size: 0.875rem;
            }
            
            .form-control:focus {
                outline: none;
                border-color: #b91c1c;
                box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.1);
            }
            
            .form-label {
                display: block;
                margin-bottom: 0.25rem;
                font-size: 0.875rem;
                font-weight: 500;
                color: #374151;
            }
            
            /* Utility classes */
            .text-3xl {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
            
            .font-bold {
                font-weight: 700;
            }
            
            .mb-6 {
                margin-bottom: 1.5rem;
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
            
            .space-x-2 > * + * {
                margin-left: 0.5rem;
            }
            
            .rounded {
                border-radius: 0.25rem;
            }
            
            .rounded-lg {
                border-radius: 0.5rem;
            }
            
            .shadow-md {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            }
            
            .overflow-hidden {
                overflow: hidden;
            }
            
            .overflow-x-auto {
                overflow-x: auto;
            }
            
            .bg-white {
                background-color: white;
            }
            
            .py-2 {
                padding-top: 0.5rem;
                padding-bottom: 0.5rem;
            }
            
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .py-4 {
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            
            .py-6 {
                padding-top: 1.5rem;
                padding-bottom: 1.5rem;
            }
            
            .py-3 {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
            
            .px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .container {
                width: 100%;
                padding-right: 1rem;
                padding-left: 1rem;
                margin-right: auto;
                margin-left: auto;
            }
            
            .mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            
            .text-gray-500 {
                color: #6b7280;
            }
            
            .text-gray-900 {
                color: #111827;
            }
            
            .text-indigo-600 {
                color: #4f46e5;
            }
            
            .text-indigo-900 {
                color: #312e81;
            }
            
            .text-yellow-600 {
                color: #d97706;
            }
            
            .text-yellow-900 {
                color: #78350f;
            }
            
            .text-red-600 {
                color: #dc2626;
            }
            
            .text-red-900 {
                color: #7f1d1d;
            }
            
            .hover\:text-indigo-900:hover {
                color: #312e81;
            }
            
            .hover\:text-yellow-900:hover {
                color: #78350f;
            }
            
            .hover\:text-red-900:hover {
                color: #7f1d1d;
            }
            
            .bg-indigo-600 {
                background-color: #4f46e5;
            }
            
            .hover\:bg-indigo-700:hover {
                background-color: #4338ca;
            }
            
            .text-white {
                color: white;
            }
            
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            
            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }
            
            .font-medium {
                font-weight: 500;
            }
            
            .uppercase {
                text-transform: uppercase;
            }
            
            .tracking-wider {
                letter-spacing: 0.05em;
            }
            
            .text-left {
                text-align: left;
            }
            
            .text-center {
                text-align: center;
            }
            
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            
            .divide-y {
                border-top-width: 1px;
                border-bottom-width: 1px;
            }
            
            .divide-gray-200 {
                border-color: #e5e7eb;
            }
            
            .min-w-full {
                min-width: 100%;
            }
            
            .mt-4 {
                margin-top: 1rem;
            }
            
            .inline {
                display: inline;
            }
            
            .bg-green-100 {
                background-color: #d1fae5;
            }
            
            .border-green-400 {
                border-color: #34d399;
            }
            
            .text-green-700 {
                color: #047857;
            }
            
            .bg-red-100 {
                background-color: #fee2e2;
            }
            
            .border-red-400 {
                border-color: #f87171;
            }
            
            .text-red-700 {
                color: #b91c1c;
            }
            
            .border {
                border-width: 1px;
            }
            
            .relative {
                position: relative;
            }
            
            .block {
                display: block;
            }
            
            .sm\:inline {
                display: inline;
            }
            
            /* Mobile responsiveness enhancements */
            @media (max-width: 768px) {
                .container {
                    padding-left: 0.75rem;
                    padding-right: 0.75rem;
                }
                
                .text-3xl {
                    font-size: 1.5rem;
                    line-height: 2rem;
                }
                
                h1, .h1 {
                    font-size: 1.5rem;
                    margin-bottom: 0.75rem;
                }
                
                h2, .h2 {
                    font-size: 1.25rem;
                }
                
                /* Table adjustments for small screens */
                .table-responsive {
                    border: 0;
                }
                
                .table-responsive table {
                    width: 100%;
                }
                
                /* Responsive tables with data-labels */
                table.mobile-responsive thead {
                    display: none;
                }
                
                table.mobile-responsive tbody tr {
                    display: block;
                    margin-bottom: 1rem;
                    border: 1px solid #e5e7eb;
                    border-radius: 0.375rem;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
                }
                
                table.mobile-responsive tbody td {
                    display: flex;
                    justify-content: space-between;
                    text-align: right;
                    padding: 0.5rem 0.75rem;
                    border-bottom: 1px solid #f3f4f6;
                }
                
                table.mobile-responsive tbody td:last-child {
                    border-bottom: none;
                }
                
                table.mobile-responsive tbody td:before {
                    content: attr(data-label);
                    font-weight: 600;
                    float: left;
                    text-align: left;
                    color: #4b5563;
                }
                
                /* Fix buttons on mobile */
                .btn {
                    padding: 0.4rem 0.75rem;
                    font-size: 0.875rem;
                }
                
                /* Card layouts for tables on mobile */
                .admin-card {
                    background-color: white;
                    border-radius: 0.5rem;
                    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                    padding: 1rem;
                    margin-bottom: 0.75rem;
                }
                
                .admin-card-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 0.75rem;
                    font-weight: 600;
                }
                
                .admin-card-body {
                    font-size: 0.875rem;
                }
                
                /* Improved form controls */
                input[type="text"],
                input[type="email"],
                input[type="password"],
                input[type="number"],
                select,
                textarea {
                    font-size: 16px !important; /* Prevent zoom on iOS */
                    padding: 0.5rem;
                    width: 100%;
                }
                
                /* Improved spacing in forms */
                .form-group {
                    margin-bottom: 0.75rem;
                }
                
                /* Stack form elements on mobile */
                .mobile-stack {
                    display: flex;
                    flex-direction: column;
                }
                
                /* Improve buttons on mobile */
                .btn-group {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 0.5rem;
                }
                
                /* Fix modals on mobile */
                .modal-content {
                    margin: 0.5rem;
                    width: calc(100% - 1rem);
                }
            }
            
            /* Phone specific enhancements */
            @media (max-width: 480px) {
                /* Smaller text for data tables */
                .table-cell-content {
                    font-size: 0.75rem;
                }
                
                /* Compact cards */
                .compact-card {
                    padding: 0.5rem;
                }
                
                /* Adjust action buttons */
                .action-buttons {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                }
                
                /* Simplified badges */
                .badge {
                    padding: 0.15rem 0.4rem;
                    font-size: 0.7rem;
                }
                
                /* Full width buttons */
                .btn-mobile-full {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
            }
            
            /* Touch improvements for mobile */
            @media (hover: none) {
                .btn:active {
                    transform: scale(0.98);
                }
                
                input[type="text"],
                input[type="email"],
                input[type="password"],
                textarea {
                    font-size: 1rem !important; /* Prevent zoom on iOS */
                }
            }
            
            /* Print styles */
            @media print {
                .sidebar-container, 
                .sidebar-toggle,
                .btn-group,
                .modal,
                .no-print {
                    display: none !important;
                }
                
                .content-wrapper {
                    margin-left: 0 !important;
                    width: 100% !important;
                }
                
                .card {
                    box-shadow: none !important;
                    border: 1px solid #ddd !important;
                }
            }
        </style>
        
        <!-- Livewire Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased pattern-bg bg-gray-50">
        <!-- Mobile Sidebar Toggle Button -->
        <button class="sidebar-toggle" aria-label="Toggle Sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <!-- Sidebar - Fixed position on the left -->
        <div class="sidebar-container">
            @include('layouts.sidebar')
        </div>
        
        <!-- Main Content - With appropriate margin to accommodate sidebar -->
        <div class="content-wrapper">
            <div class="container mx-auto px-4 py-4 w-full">
                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white shadow-sm border-b border-yellow-400">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
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
                // Sidebar toggle functionality
                const sidebarToggle = document.querySelector('.sidebar-toggle');
                const sidebarContainer = document.querySelector('.sidebar-container');
                const contentWrapper = document.querySelector('.content-wrapper');
                
                if (sidebarToggle && sidebarContainer && contentWrapper) {
                    sidebarToggle.addEventListener('click', function() {
                        sidebarContainer.classList.toggle('show');
                        
                        // Add overlay when sidebar is shown on mobile
                        if (sidebarContainer.classList.contains('show')) {
                            // Create overlay if it doesn't exist
                            let overlay = document.getElementById('sidebar-overlay');
                            if (!overlay) {
                                overlay = document.createElement('div');
                                overlay.id = 'sidebar-overlay';
                                overlay.style.position = 'fixed';
                                overlay.style.top = '0';
                                overlay.style.left = '0';
                                overlay.style.width = '100%';
                                overlay.style.height = '100%';
                                overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                                overlay.style.zIndex = '1049';
                                overlay.style.display = 'block';
                                document.body.appendChild(overlay);
                                
                                // Close sidebar when overlay is clicked
                                overlay.addEventListener('click', function() {
                                    sidebarContainer.classList.remove('show');
                                    overlay.remove();
                                });
                            }
                        } else {
                            // Remove overlay when sidebar is hidden
                            const overlay = document.getElementById('sidebar-overlay');
                            if (overlay) {
                                overlay.remove();
                            }
                        }
                    });
                    
                    // Close sidebar when window is resized to desktop size
                    window.addEventListener('resize', function() {
                        if (window.innerWidth > 1024 && sidebarContainer.classList.contains('show')) {
                            sidebarContainer.classList.remove('show');
                            const overlay = document.getElementById('sidebar-overlay');
                            if (overlay) {
                                overlay.remove();
                            }
                        }
                    });
                    
                    // Close sidebar when clicking outside on mobile
                    document.addEventListener('click', function(event) {
                        const isMobile = window.innerWidth <= 1024;
                        const isClickInsideSidebar = sidebarContainer.contains(event.target);
                        const isClickOnToggle = sidebarToggle.contains(event.target);
                        
                        if (isMobile && !isClickInsideSidebar && !isClickOnToggle && sidebarContainer.classList.contains('show')) {
                            sidebarContainer.classList.remove('show');
                            const overlay = document.getElementById('sidebar-overlay');
                            if (overlay) {
                                overlay.remove();
                            }
                        }
                    });
                }
                
                // Add data-label attributes to table cells for mobile responsive view
                const tables = document.querySelectorAll('.table-responsive table');
                tables.forEach(function(table) {
                    const headerCells = table.querySelectorAll('thead th');
                    const headerTexts = Array.from(headerCells).map(cell => cell.textContent.trim());
                    
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(function(row) {
                        const cells = row.querySelectorAll('td');
                        cells.forEach(function(cell, index) {
                            if (index < headerTexts.length) {
                                cell.setAttribute('data-label', headerTexts[index]);
                            }
                        });
                    });
                });
                
                // Handle sidebar close button
                const sidebarCloseBtn = document.querySelector('.sidebar-close');
                if (sidebarCloseBtn) {
                    sidebarCloseBtn.addEventListener('click', function() {
                        if (sidebarContainer) {
                            sidebarContainer.classList.remove('show');
                            const overlay = document.getElementById('sidebar-overlay');
                            if (overlay) {
                                overlay.remove();
                            }
                        }
                    });
                }
            });
        </script>
    </body>
</html>
