<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'KofA Attendance Monitoring System') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Custom styles -->
    <link href="/css/style.css" rel="stylesheet">
    <link href="/css/sidebar-enhancements.css" rel="stylesheet">
    <link href="/css/responsive.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="/js/app.js" defer></script>
    
    <style>
        :root {
            --primary-color: #b91c1c; /* Red */
            --secondary-color: #facc15; /* Golden Yellow */
        }
        
        .navbar-custom {
            background-color: var(--primary-color);
        }
        
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        
        .sidebar .nav-link {
            color: #333;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .sidebar .nav-link:hover {
            background-color: rgba(185, 28, 28, 0.1);
        }
        
        .content-wrapper {
            min-height: calc(100vh - 56px);
        }
        
        footer {
            background-color: #343a40;
            color: #f8f9fa;
        }
        
        /* Additional responsive styles */
        @media (max-width: 768px) {
            .container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
            
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
            
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
        }
        
        @media (max-width: 576px) {
            h1, .h1 {
                font-size: 1.75rem;
            }
            
            h2, .h2 {
                font-size: 1.5rem;
            }
            
            h3, .h3 {
                font-size: 1.25rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .admin-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .admin-header .date-display {
                margin-top: 0.5rem;
            }
        }
    </style>
</head>
<body class="role-{{ strtolower(auth()->user()->role ?? 'guest') }}">
    @auth
        <!-- Sidebar Toggle Button for Mobile -->
        <button class="sidebar-toggle d-lg-none border-0">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Sidebar -->
        <div class="sidebar-container">
            @include('layouts.sidebar')
        </div>

        <!-- Main Content -->
        <main class="admin-content">
            <div class="admin-header d-flex justify-content-between align-items-center animate__animated animate__fadeIn">
                <h2 class="h5 mb-0">{{ $title ?? 'Dashboard' }}</h2>
                <div class="date-display">
                    <i class="bi bi-calendar2"></i> {{ date('M d, Y') }}
                </div>
            </div>
            
            @yield('content')
        </main>
    @else
        <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    KofA AMS
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
        
        <footer class="footer py-3 bg-dark text-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <p class="mb-0">&copy; {{ date('Y') }} KofA AMS. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="#" class="text-decoration-none text-light me-2">
                            <i class="bi bi-facebook"></i>
                        </a>
                        <a href="#" class="text-decoration-none text-light">
                            <i class="bi bi-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Mobile sidebar toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar-container');
            
            if (sidebarToggle && sidebar) {
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
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html> 