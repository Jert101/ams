@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h1 class="mb-4">Download CKP-KofA Network Mobile App</h1>
                    
                    <div class="text-center mb-5">
                        <img src="{{ asset('kofa.png') }}" alt="CKP-KofA Logo" style="max-width: 150px;">
                    </div>
                    
                    <p class="lead text-center mb-5">Download our official mobile application for a better experience on your device.</p>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <i class="bi bi-android2 display-1 text-success mb-3"></i>
                                    <h3>Android App</h3>
                                    <p class="mb-4">Download our Android application</p>
                                    
                                    <div class="alert alert-info mb-3">
                                        <i class="bi bi-info-circle me-2"></i>
                                        The mobile app is temporarily unavailable for download.
                                        <br>
                                        Please check back later.
                                    </div>
                                    
                                    <!-- Download button disabled
                                    <a href="{{ url('/infinity.php') }}" class="btn btn-success btn-lg w-100 mb-2">
                                        <i class="bi bi-download me-2"></i> Download APK
                                    </a>
                                    -->
                                    
                                    <div class="mt-2 small">
                                        <p>App version: 1.0.0</p>
                                        <p>Last updated: {{ date('F j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <i class="bi bi-apple display-1 text-dark mb-3"></i>
                                    <h3>iOS App</h3>
                                    <p class="mb-4">Download our iOS application</p>
                                    <a href="#" class="btn btn-secondary btn-lg w-100 disabled">
                                        <i class="bi bi-clock-history me-2"></i> Coming Soon
                                    </a>
                                    <p class="mt-3 small text-muted">In development</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h2>Installation Instructions</h2>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">Android Installation</h3>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Download the APK file by clicking the "Download APK" button above.</li>
                                    <li>Open the downloaded file on your Android device.</li>
                                    <li>If prompted, allow installation from unknown sources in your device settings.</li>
                                    <li>Follow the on-screen instructions to complete the installation.</li>
                                    <li>Once installed, open the app from your home screen or app drawer.</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-light">
                                <h3 class="h5 mb-0">iOS Installation</h3>
                            </div>
                            <div class="card-body">
                                <p>iOS app is currently in development. Please check back later for updates.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h2>System Requirements</h2>
                        <ul>
                            <li><strong>Android:</strong> Android 5.0 (Lollipop) or higher</li>
                            <li><strong>iOS:</strong> iOS 12.0 or higher (when available)</li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-left me-2"></i> Return to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if the APK file exists
        const apkLink = document.querySelector('a[href$="ckp-kofa-app.apk"]');
        
        if (apkLink) {
            apkLink.addEventListener('click', function(e) {
                // This is a simple client-side check that will show a message
                // In production, you should implement a server-side check
                fetch(this.href, { method: 'HEAD' })
                    .then(response => {
                        if (!response.ok) {
                            e.preventDefault();
                            alert('The APK file is not yet available. Please check back later or contact the administrator.');
                        }
                    })
                    .catch(error => {
                        e.preventDefault();
                        alert('The APK file is not yet available. Please check back later or contact the administrator.');
                    });
            });
        }
    });
</script>
@endpush
@endsection 