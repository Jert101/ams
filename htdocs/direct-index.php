<?php
// This file serves as a direct landing page when Laravel routing isn't working

// Define the base URL - this should match your deployment domain
$baseUrl = 'https://ckpkofa-network.ct.ws';

// Check if we're in localhost
if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $baseUrl = 'http://' . $_SERVER['HTTP_HOST'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KofA AMS - Knights of the Altar Attendance Monitoring System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- Inline styles to ensure compatibility -->
    <style>
        :root {
            --primary-color: #b91c1c; /* Red */
            --secondary-color: #facc15; /* Golden Yellow */
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f5f5f5;
            overflow-x: hidden;
        }
        
        .navbar-custom {
            background-color: var(--primary-color);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .nav-link:hover {
            color: white !important;
        }
        
        .hero-parallax {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }
        
        .parallax-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 120%;
            background-image: url('https://source.unsplash.com/1600x900/?church,altar,catholic');
            background-size: cover;
            background-position: center;
            z-index: -2;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5));
            z-index: -1;
        }
        
        .text-warning {
            color: var(--secondary-color) !important;
        }
        
        .feature-card {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .feature-card.active {
            border-top: 4px solid var(--primary-color);
        }
        
        .feature-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .counter-card {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .counter-value {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .testimonial-card {
            background-color: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            display: none;
        }
        
        .testimonial-card.active {
            display: block;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            margin-top: 1.5rem;
        }
        
        .testimonial-author img {
            width: 60px;
            height: 60px;
            margin-right: 1rem;
        }
        
        .cta-card {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            padding: 3rem;
            border-radius: 8px;
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .footer {
            background-color: #343a40;
            color: #f8f9fa;
            padding: 3rem 0;
        }
        
        /* Animations */
        .floating-card-wrapper {
            position: relative;
            height: 400px;
            width: 100%;
        }
        
        .floating-card {
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 12px;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: center;
            align-items: center;
            animation: float 4s ease-in-out infinite;
        }
        
        .floating-card:first-child {
            top: 30px;
            left: 10%;
        }
        
        .floating-card:last-child {
            top: 150px;
            right: 10%;
        }
        
        .card-inner {
            text-align: center;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
            100% {
                transform: translateY(0px);
            }
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .display-2 {
                font-size: 2.75rem;
            }
            .display-5 {
                font-size: 1.5rem;
            }
            .floating-card-wrapper {
                display: none;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>">
                KofA AMS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>/register">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Parallax Effect -->
    <div class="hero-parallax position-relative overflow-hidden">
        <div class="parallax-bg"></div>
        <div class="hero-overlay"></div>
        
        <div class="container position-relative" style="z-index: 1;">
            <div class="row min-vh-100 align-items-center">
                <div class="col-lg-7 text-center text-lg-start">
                    <h1 class="display-2 fw-bold text-white mb-3">Knights of the <span class="text-warning">Altar</span></h1>
                    <h2 class="display-5 fw-bold text-white mb-4">Attendance Monitoring System</h2>
                    <p class="lead text-white mb-5 opacity-90">A powerful system to track attendance, manage events, and grow your ministry with elegant design and seamless experience</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                        <a href="<?php echo $baseUrl; ?>/login" class="btn btn-primary btn-lg px-4 py-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                        </a>
                        <a href="<?php echo $baseUrl; ?>/register" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
                            <i class="bi bi-person-plus me-2"></i> Register
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="floating-card-wrapper">
                        <div class="floating-card">
                            <div class="card-inner">
                                <i class="bi bi-qr-code text-warning display-1"></i>
                                <h4 class="mt-3 text-white">Modern QR Technology</h4>
                            </div>
                        </div>
                        <div class="floating-card" style="animation-delay: 0.3s">
                            <div class="card-inner">
                                <i class="bi bi-graph-up-arrow text-warning display-1"></i>
                                <h4 class="mt-3 text-white">Advanced Analytics</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section with Enhanced Cards -->
    <div class="container py-5">
        <div class="row my-5">
            <div class="col-lg-6 mx-auto text-center">
                <div class="section-title">
                    <span class="badge bg-warning text-dark px-3 py-2 mb-3">POWERFUL FEATURES</span>
                    <h2 class="fw-bold display-5 mb-4 position-relative d-inline-block">
                        <span class="position-relative" style="z-index: 2;">What We Offer</span>
                        <span class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 10px; opacity: 0.3; z-index: -1;"></span>
                    </h2>
                    <p class="lead text-muted">Everything you need to manage your altar servers effectively with a beautiful interface</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-qr-code-scan"></i>
                    </div>
                    <h3>QR Code Attendance</h3>
                    <p>Quickly track attendance with our easy-to-use QR code scanning system that works seamlessly with any device.</p>
                    <a href="#" class="feature-link">Learn more <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card active">
                    <div class="feature-icon">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <h3>Event Management</h3>
                    <p>Create and manage events with our intuitive calendar system. Send notifications and reminders automatically.</p>
                    <a href="#" class="feature-link">Learn more <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h3>Detailed Analytics</h3>
                    <p>Get comprehensive reports and analytics on attendance patterns, participation rates, and more.</p>
                    <a href="#" class="feature-link">Learn more <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Call to Action with Enhanced Design -->
        <div class="row mt-5 pt-5 mb-4">
            <div class="col-12 text-center">
                <div class="cta-card">
                    <div class="cta-content">
                        <h2 class="fw-bold text-white mb-4">Ready to Transform Your Ministry?</h2>
                        <p class="lead text-white mb-4">Join hundreds of parishes using KofA AMS to elevate their ministry</p>
                        <a href="<?php echo $baseUrl; ?>/register" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                            Get Started Today <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> KofA AMS is a trademark of Jerson L. Catadman. &copy; 2025 CKP-KofA Network. All rights reserved.</p>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 