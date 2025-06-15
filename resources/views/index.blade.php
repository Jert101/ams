<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KofA Attendance Monitoring System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom styles -->
    <link href="{{ url('/css/style.css') }}" rel="stylesheet">
    <link href="{{ url('/css/sidebar-enhancements.css') }}" rel="stylesheet">
    <link href="{{ url('/css/responsive.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="/">
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
<!-- Hero Section with Parallax Effect -->
<div class="hero-parallax position-relative overflow-hidden">
    <div class="parallax-bg" style="background-image: url('https://source.unsplash.com/1600x900/?church,altar,catholic')"></div>
    <div class="hero-overlay"></div>
    <!-- Animated Shapes -->
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>
    <div class="container position-relative z-index-1">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-7 text-center text-lg-start animate__animated animate__fadeInLeft">
                <h1 class="display-2 fw-bold text-white mb-3">Knights of the <span class="text-warning">Altar</span></h1>
                <h2 class="display-5 fw-bold text-white mb-4">Attendance Monitoring System</h2>
                <p class="lead text-white mb-5 opacity-90">A powerful system to track attendance, manage events, and grow your ministry with elegant design and seamless experience</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-lg-start">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                            </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
                                    <i class="bi bi-person-plus me-2"></i> Register
                                </a>
                            <a href="{{ route('mobile.app.download') }}" class="btn btn-warning btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                <i class="bi bi-download me-2"></i> Download App
                            </a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block animate__animated animate__fadeInRight">
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
    <div class="hero-wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#f8f9fa" fill-opacity="1" d="M0,192L48,176C96,160,192,128,288,122.7C384,117,480,139,576,165.3C672,192,768,224,864,213.3C960,203,1056,149,1152,122.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
    </div>
</div>
<!-- Features Section with Enhanced Cards -->
<div class="container py-5">
    <div class="row my-5">
        <div class="col-lg-6 mx-auto text-center">
            <div class="section-title animate__animated animate__fadeInUp">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">POWERFUL FEATURES</span>
                <h2 class="fw-bold display-5 mb-4 position-relative d-inline-block">
                    <span class="position-relative z-2">What We Offer</span>
                    <span class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 10px; opacity: 0.3; z-index: -1;"></span>
                </h2>
                <p class="lead text-muted">Everything you need to manage your altar servers effectively with a beautiful interface</p>
            </div>
        </div>
    </div>
    <div class="row g-4">
        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-qr-code-scan"></i>
                </div>
                <h3>QR Code Attendance</h3>
                <p>Quickly track attendance with our easy-to-use QR code scanning system that works seamlessly with any device.</p>
                <a href="#" class="feature-link">Learn more <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
            <div class="feature-card active">
                <div class="feature-icon">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <h3>Event Management</h3>
                <p>Create and manage events with our intuitive calendar system. Send notifications and reminders automatically.</p>
                <a href="#" class="feature-link">Learn more <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
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
    <!-- Stats Counter Section -->
    <div class="row mt-5 pt-5 gy-4 counter-section">
        <div class="col-6 col-md-3 text-center animate__animated animate__fadeIn">
            <div class="counter-card">
                <div class="counter-value" data-count="24">0</div>
                <h4 class="counter-title">Parishes</h4>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center animate__animated animate__fadeIn" style="animation-delay: 0.2s">
            <div class="counter-card">
                <div class="counter-value" data-count="350">0</div>
                <h4 class="counter-title">Altar Servers</h4>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center animate__animated animate__fadeIn" style="animation-delay: 0.4s">
            <div class="counter-card">
                <div class="counter-value" data-count="98">0</div>
                <h4 class="counter-title">Events Tracked</h4>
            </div>
        </div>
        <div class="col-6 col-md-3 text-center animate__animated animate__fadeIn" style="animation-delay: 0.6s">
            <div class="counter-card">
                <div class="counter-value" data-count="99">0</div>
                <h4 class="counter-title">Satisfaction %</h4>
            </div>
        </div>
    </div>
    <!-- Testimonial Section with Enhanced Design -->
    <div class="row mt-5 pt-5 align-items-center">
        <div class="col-lg-4 pe-lg-5 mb-5 mb-lg-0">
            <div class="section-title animate__animated animate__fadeIn">
                <span class="badge bg-warning text-dark px-3 py-2 mb-3">TESTIMONIALS</span>
                <h2 class="fw-bold h1 mb-4">What People Say About Us</h2>
                <p class="text-muted">Discover why parishes love using our attendance monitoring system.</p>
            </div>
            <div class="d-flex gap-2 mt-4 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
                <button class="btn btn-sm btn-outline-primary testimonial-nav-prev">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <button class="btn btn-sm btn-primary testimonial-nav-next">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
        <div class="col-lg-8 animate__animated animate__fadeIn">
            <div class="testimonial-slider">
                <div class="testimonial-card active">
                    <div class="testimonial-content">
                        <i class="bi bi-quote text-primary display-1 opacity-25"></i>
                        <p class="lead fst-italic">"This system has revolutionized how we manage our altar servers. Attendance tracking is now effortless, and the analytics help us understand participation patterns."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Fr.+Michael&background=b91c1c&color=fff" alt="Fr. Michael" class="rounded-circle">
                        <div>
                            <h5 class="mb-0">Fr. Michael Rodriguez</h5>
                            <p class="text-muted mb-0">Parish Priest, St. Joseph's Church</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <i class="bi bi-quote text-primary display-1 opacity-25"></i>
                        <p class="lead fst-italic">"The QR code attendance system has saved us countless hours of manual record keeping. Our servers enjoy using it and it's improved our overall attendance."</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="https://ui-avatars.com/api/?name=Sister+Maria&background=4f46e5&color=fff" alt="Sister Maria" class="rounded-circle">
                        <div>
                            <h5 class="mb-0">Sister Maria Garcia</h5>
                            <p class="text-muted mb-0">Youth Coordinator, Holy Trinity Parish</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Call to Action with Enhanced Design -->
    <div class="row mt-5 pt-5 mb-4 cta-section">
        <div class="col-12 text-center animate__animated animate__fadeInUp">
            <div class="cta-card">
                <div class="cta-content">
                    <h2 class="fw-bold text-white mb-4">Ready to Transform Your Ministry?</h2>
                    <p class="lead text-white mb-4">Join hundreds of parishes using KofA AMS to elevate their ministry</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3 fw-bold">
                            Get Started Today <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                        <a href="{{ route('mobile.app.download') }}" class="btn btn-warning btn-lg px-5 py-3 fw-bold">
                            <i class="bi bi-download me-2"></i> Download Our App
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </main>
    <footer class="footer py-3 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <p class="mb-0">KofA AMS is a trademark of Jerson L. Catadman. &copy; 2025 CKP-KofA Network. All rights reserved.</p>
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
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
