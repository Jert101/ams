@extends('layouts.main')

@section('content')
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
                    @if (Route::has('login'))
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            @elseif(auth()->user()->isOfficer())
                                <a href="{{ route('officer.dashboard') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            @elseif(auth()->user()->isSecretary())
                                <a href="{{ route('secretary.dashboard') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            @elseif(auth()->user()->isMember())
                                <a href="{{ route('member.dashboard') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4 py-3 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 py-3 fw-semibold">
                                    <i class="bi bi-person-plus me-2"></i> Register
                                </a>
                            @endif
                            <a href="{{ route('mobile.app.download') }}" class="btn btn-warning btn-lg px-4 py-3 fw-semibold animate__animated animate__pulse animate__infinite">
                                <i class="bi bi-download me-2"></i> Download App
                            </a>
                        @endauth
                    @endif
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

    <div class="mt-4 text-center">
        <h4>Test Links</h4>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/mobile-app') }}" class="btn btn-primary">Mobile App Download Page</a>
            <a href="{{ asset('downloads/ckp-kofa-app.apk') }}" class="btn btn-success">Direct APK Download</a>
            <a href="{{ url('/test-download.php') }}" class="btn btn-info">Test APK Access</a>
            <a href="{{ url('/download-apk.php') }}" class="btn btn-warning">PHP Download Script</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Parallax effect for hero section
    window.addEventListener('scroll', function() {
        const parallax = document.querySelector('.parallax-bg');
        let scrollPosition = window.pageYOffset;
        parallax.style.transform = 'translateY(' + scrollPosition * 0.5 + 'px)';
    });
    
    // PWA Installation
    let deferredPrompt;
    const installButton = document.getElementById('pwa-install-button');
    const installButtonCta = document.getElementById('pwa-install-button-cta');
    
    // Initially hide the buttons
    if (installButton) {
        installButton.style.display = 'none';
    }
    if (installButtonCta) {
        installButtonCta.style.display = 'none';
    }
    
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        
        // Show the install buttons
        if (installButton) {
            installButton.style.display = 'inline-flex';
            
            installButton.addEventListener('click', showInstallPrompt);
        }
        
        if (installButtonCta) {
            installButtonCta.style.display = 'inline-flex';
            
            installButtonCta.addEventListener('click', showInstallPrompt);
        }
    });
    
    function showInstallPrompt() {
        if (!deferredPrompt) {
            // If no install prompt is available, show the installation instructions
            showInstallInstructions();
            return;
        }
        
        // Show the install prompt
        deferredPrompt.prompt();
        
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
                // Hide buttons after installation
                if (installButton) installButton.style.display = 'none';
                if (installButtonCta) installButtonCta.style.display = 'none';
            } else {
                console.log('User dismissed the install prompt');
            }
            deferredPrompt = null;
        });
    }
    
    // If app is already installed, hide the buttons
    window.addEventListener('appinstalled', () => {
        console.log('PWA was installed');
        if (installButton) installButton.style.display = 'none';
        if (installButtonCta) installButtonCta.style.display = 'none';
    });
    
    // Show download buttons immediately for browsers that might not support beforeinstallprompt
    setTimeout(() => {
        // If the beforeinstallprompt event didn't fire after 3 seconds,
        // show the buttons anyway with fallback behavior
        if (!deferredPrompt) {
            if (installButton) {
                installButton.style.display = 'inline-flex';
                installButton.addEventListener('click', showInstallInstructions);
            }
            
            if (installButtonCta) {
                installButtonCta.style.display = 'inline-flex';
                installButtonCta.addEventListener('click', showInstallInstructions);
            }
        }
    }, 3000);
    
    function showInstallInstructions() {
        // Create a modal with installation instructions
        const modal = document.createElement('div');
        modal.className = 'pwa-install-modal';
        modal.innerHTML = `
            <div class="pwa-install-modal-content">
                <span class="pwa-install-modal-close">&times;</span>
                <h2>Install CKP-KofA App</h2>
                <p>To install our app on your device:</p>
                
                <div class="pwa-install-instructions">
                    <h4>On Android:</h4>
                    <ol>
                        <li>Open this website in Chrome</li>
                        <li>Tap the menu button (three dots)</li>
                        <li>Select "Add to Home screen"</li>
                    </ol>
                    
                    <h4>On iPhone/iPad:</h4>
                    <ol>
                        <li>Open this website in Safari</li>
                        <li>Tap the Share button</li>
                        <li>Scroll down and select "Add to Home Screen"</li>
                    </ol>
                    
                    <h4>On Desktop:</h4>
                    <ol>
                        <li>Open this website in Chrome, Edge or other supported browser</li>
                        <li>Look for the install icon in the address bar</li>
                        <li>Click on it and follow the instructions</li>
                    </ol>
                </div>
                
                <a href="{{ route('pwa.instructions') }}" class="btn btn-primary mt-3">More Information</a>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Style the modal
        const style = document.createElement('style');
        style.textContent = `
            .pwa-install-modal {
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.7);
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .pwa-install-modal-content {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                max-width: 500px;
                width: 90%;
                max-height: 90vh;
                overflow-y: auto;
            }
            .pwa-install-modal-close {
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }
            .pwa-install-instructions {
                margin: 20px 0;
                text-align: left;
            }
            .pwa-install-instructions h4 {
                margin-top: 15px;
                color: #b91c1c;
            }
        `;
        document.head.appendChild(style);
        
        // Close modal functionality
        const closeBtn = modal.querySelector('.pwa-install-modal-close');
        closeBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
            document.head.removeChild(style);
        });
        
        // Close when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
                document.head.removeChild(style);
            }
        });
    }
    
    // Counter animation
    document.addEventListener('DOMContentLoaded', function() {
        const counters = document.querySelectorAll('.counter-value');
        const speed = 200;
        
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-count');
                const count = +counter.innerText;
                const increment = target / speed;
                
                if(count < target) {
                    counter.innerText = Math.ceil(count + increment);
                    setTimeout(updateCount, 1);
                } else {
                    counter.innerText = target;
                }
            };
            updateCount();
        });
        
        // Testimonial slider controls
        const prevBtn = document.querySelector('.testimonial-nav-prev');
        const nextBtn = document.querySelector('.testimonial-nav-next');
        const testimonials = document.querySelectorAll('.testimonial-card');
        let currentIndex = 0;
        
        if(prevBtn && nextBtn) {
            prevBtn.addEventListener('click', function() {
                testimonials[currentIndex].classList.remove('active');
                currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
                testimonials[currentIndex].classList.add('active');
            });
            
            nextBtn.addEventListener('click', function() {
                testimonials[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % testimonials.length;
                testimonials[currentIndex].classList.add('active');
            });
        }
    });
</script>
@endpush
