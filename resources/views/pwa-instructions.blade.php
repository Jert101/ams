@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <h1 class="mb-4">CKP-KofA Network Progressive Web App (PWA)</h1>
                    
                    <p class="lead">The CKP-KofA Network now supports installation as a Progressive Web App (PWA), allowing you to download and use the system as an app on your device.</p>
                    
                    <div class="my-5">
                        <h2>What is a PWA?</h2>
                        <p>A Progressive Web App (PWA) is a type of application software delivered through the web, built using common web technologies including HTML, CSS, and JavaScript. It is intended to work on any platform with a standards-compliant browser, including desktop and mobile devices.</p>
                    </div>
                    
                    <div class="my-5">
                        <h2>Benefits of Using CKP-KofA Network as a PWA</h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="bi bi-wifi-off me-2 text-primary"></i> <strong>Offline Access:</strong> Access key features even when offline</li>
                            <li class="list-group-item"><i class="bi bi-lightning-charge me-2 text-primary"></i> <strong>Fast Loading:</strong> Cached resources load quickly</li>
                            <li class="list-group-item"><i class="bi bi-phone me-2 text-primary"></i> <strong>Home Screen Icon:</strong> Install the app on your device's home screen</li>
                            <li class="list-group-item"><i class="bi bi-app me-2 text-primary"></i> <strong>Native-like Experience:</strong> Feels like a native app with full-screen mode</li>
                            <li class="list-group-item"><i class="bi bi-arrow-repeat me-2 text-primary"></i> <strong>Automatic Updates:</strong> Always get the latest version without manual updates</li>
                        </ul>
                    </div>
                    
                    <div class="my-5">
                        <h2>How to Install the CKP-KofA Network PWA</h2>
                        
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h3 class="h5 mb-0">On Mobile Devices (Android/iOS)</h3>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Open the CKP-KofA Network website in your browser (Chrome, Safari, etc.)</li>
                                    <li>For most users, you'll see an "Install CKP-KofA App" button at the bottom of the screen</li>
                                    <li>Tap this button to install the app</li>
                                    <li>Alternatively:
                                        <ul>
                                            <li><strong>On Android (Chrome):</strong> Tap the three dots menu → "Add to Home screen"</li>
                                            <li><strong>On iOS (Safari):</strong> Tap the share icon → "Add to Home Screen"</li>
                                        </ul>
                                    </li>
                                    <li>Follow the on-screen instructions to complete installation</li>
                                    <li>The CKP-KofA app will now appear on your home screen</li>
                                </ol>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h3 class="h5 mb-0">On Desktop (Windows/Mac/Linux)</h3>
                            </div>
                            <div class="card-body">
                                <ol>
                                    <li>Open the CKP-KofA Network website in a compatible browser (Chrome, Edge, etc.)</li>
                                    <li>Look for the install icon in the address bar (usually on the right side)</li>
                                    <li>Click this icon and follow the installation prompts</li>
                                    <li>The CKP-KofA app will be installed and can be launched from your desktop/start menu</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="my-5">
                        <h2>System Requirements</h2>
                        <ul>
                            <li><strong>Mobile:</strong> iOS 12.2+ (Safari) or Android 5+ (Chrome)</li>
                            <li><strong>Desktop:</strong> Chrome 73+, Edge 79+, Opera 64+, or Firefox 67+</li>
                        </ul>
                    </div>
                    
                    <div class="my-5">
                        <h2>Troubleshooting</h2>
                        <p>If you don't see the install option:</p>
                        <ul>
                            <li>Make sure you're using a supported browser</li>
                            <li>Try clearing your browser cache and cookies</li>
                            <li>Ensure you have a stable internet connection for the first installation</li>
                            <li>Contact your system administrator if issues persist</li>
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
@endsection 