@extends('layouts.admin-app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-red-700 mb-4 sm:mb-0">Print QR Code ID Card</h1>
        <div class="flex flex-wrap gap-2 button-container">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded print-button">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print QR Code
            </button>
            <button id="download-card-btn" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded no-print">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download as Image
            </button>
            <a href="{{ route('admin.qrcode.manage') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded no-print">
                Back to Management
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden qr-card mx-auto" id="printable-card">
        <!-- Card Header with Red Gradient Background -->
        <div class="bg-gradient-to-r from-red-700 to-red-600 text-white p-6 relative">
            <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-10 rounded-bl-full"></div>
            
            <div class="flex items-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mr-4 p-2">
                    <!-- KofA Logo -->
                    <img src="{{ asset('kofa.png') }}" alt="KofA Logo" class="w-full h-full" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0iI2RjMjYyNiI+PHBhdGggZD0iTTEyIDJDNi40OCAyIDIgNi40OCAyIDEyczQuNDggMTAgMTAgMTAgMTAtNC40OCAxMC0xMFMxNy41MiAyIDEyIDJ6bTAgMThjLTQuNDEgMC04LTMuNTktOC04czMuNTktOCA4LTggOCAzLjU5IDggOC0zLjU5IDgtOCA4em0tMS0xNHY0aDJ2LTRoLTJ6bTAgNnYyaDJ2LTJoLTJ6Ii8+PC9zdmc+'" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold">KofA AMS</h1>
                    <h2 class="text-sm opacity-90">Attendance Management System</h2>
                </div>
            </div>
        </div>
        
        <!-- QR Code Section -->
        <div class="py-6 px-8">
            <div class="flex flex-col items-center">
                <div id="qrcode-container" class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 qr-image-container"></div>
                <p class="text-center text-gray-600 text-sm mt-3 bg-gray-50 px-4 py-2 rounded-full overflow-x-auto max-w-full">
                    <span class="font-mono font-bold">{{ $qrCode->code }}</span>
                </p>
                <p class="text-center text-gray-500 text-sm mt-2">Scan this code for attendance</p>
            </div>
        </div>
        
        <!-- User Info Section -->
        <div class="px-8 py-6 bg-gray-50">
            <div class="flex flex-col sm:flex-row">
                <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-white shadow-md mr-8 flex-shrink-0 mx-auto sm:mx-0 mb-4 sm:mb-0">
                    <!-- User Profile Image -->
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover" onerror="this.onerror=null;this.src='{{ asset('img/kofa.png') }}';">
                    <p style="font-size:10px;word-break:break-all;">Photo URL: {{ $user->profile_photo_url }}</p>
                </div>
                <div class="flex-1 sm:ml-4">
                    <h3 class="text-xl font-bold text-gray-800 text-center sm:text-left auto-text-size mb-1" data-max-size="text-xl" data-min-size="text-base">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 mb-5 text-center sm:text-left">Member since {{ $user->created_at->format('M Y') }}</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6">
                        <div class="col-span-1">
                            <p class="text-sm text-gray-500 mb-1">ID</p>
                            <p class="font-semibold">{{ $user->user_id }}</p>
                        </div>
                        <div class="col-span-1">
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            <p class="font-semibold" style="word-break: break-all; font-size: 14px; line-height: 1.3;">{{ $user->email }}</p>
                        </div>
                        <div class="col-span-1 sm:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Address</p>
                            <p class="font-semibold" style="word-break: break-word; max-width: 100%;">{{ $user->address ?? 'N/A' }}</p>
                        </div>
                        <div class="col-span-1">
                            <p class="text-sm text-gray-500 mb-1">Birth Date</p>
                            <p class="font-semibold">{{ $user->date_of_birth ? date('F j, Y', strtotime($user->date_of_birth)) : 'N/A' }}</p>
                        </div>
                        <div class="col-span-1">
                            <p class="text-sm text-gray-500 mb-1">Gender</p>
                            <p class="font-semibold">{{ $user->gender ?? 'N/A' }}</p>
                        </div>
                        <div class="col-span-1">
                            <p class="text-sm text-gray-500 mb-1">Mobile</p>
                            <p class="font-semibold">{{ $user->mobile_number ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Footer -->
        <div class="border-t border-gray-200 px-6 py-4 bg-white text-xs">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                <div class="text-center sm:text-left mb-2 sm:mb-0">
                    <span class="font-bold text-red-700">KofA AMS</span>
                </div>
                <div class="text-center sm:text-right text-gray-600">
                    Generated on {{ date('F j, Y') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Generate QR code with responsive sizing
        const qrCodeContainer = document.getElementById('qrcode-container');
        
        // Determine QR code size based on screen width
        let qrSize = 200; // Default size
        if (window.innerWidth < 640) {
            qrSize = Math.min(window.innerWidth - 100, 200); // Responsive but not too small
        }
        
        const qrCode = new QRCode(qrCodeContainer, {
            text: "{{ $qrCode->code }}",
            width: qrSize,
            height: qrSize,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
        
        // Handle window resize for responsiveness
        window.addEventListener('resize', function() {
            adjustLayout();
        });
        
        // Initial layout adjustment
        adjustLayout();
        
        function adjustLayout() {
            // Adjust QR code container if needed
            if (window.innerWidth < 640) {
                const containerWidth = Math.min(window.innerWidth - 100, 200);
                qrCodeContainer.style.width = containerWidth + 'px';
                qrCodeContainer.style.height = containerWidth + 'px';
            } else {
                qrCodeContainer.style.width = '200px';
                qrCodeContainer.style.height = '200px';
            }
        }
        
        // Download card as image
        document.getElementById('download-card-btn').addEventListener('click', function() {
            // Show loading indicator
            this.innerHTML = '<svg class="animate-spin h-5 w-5 text-white inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            
            const card = document.getElementById('printable-card');
            
            // We need to wait for QR code to render completely
            setTimeout(() => {
                html2canvas(card, {
                    scale: 3, // Higher scale for better quality
                    logging: false,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    onclone: function(clonedDoc) {
                        // Ensure footer text is visible in the clone
                        const clonedFooter = clonedDoc.querySelector('.border-t');
                        if (clonedFooter) {
                            const footerText = clonedFooter.querySelectorAll('div');
                            footerText.forEach(el => {
                                el.style.color = '#111827'; // dark gray
                                el.style.fontSize = '14px';
                                el.style.fontWeight = '500';
                            });
                        }
                    }
                }).then(canvas => {
                    // Create a link element to download the image
                    const link = document.createElement('a');
                    link.download = 'qr-card-{{ $user->user_id }}.png';
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    
                    // Reset button text
                    document.getElementById('download-card-btn').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg> Download as Image';
                });
            }, 500);
        });
        
        // Automatically adjust font size based on content
        function adjustTextSizes() {
            const elements = document.querySelectorAll('.auto-text-size');
            elements.forEach(element => {
                const maxSize = element.getAttribute('data-max-size') || 'text-xl';
                const minSize = element.getAttribute('data-min-size') || 'text-base';
                
                // Reset to maximum size first
                element.classList.remove('text-base', 'text-lg', 'text-xl');
                element.classList.add(maxSize);
                
                // Check if text is overflowing
                if (element.scrollWidth > element.clientWidth) {
                    element.classList.remove(maxSize);
                    element.classList.add('text-lg');
                    
                    // Check again, if still overflowing, use minimum size
                    if (element.scrollWidth > element.clientWidth) {
                        element.classList.remove('text-lg');
                        element.classList.add(minSize);
                    }
                }
            });
        }
        
        // Run on load and on resize
        adjustTextSizes();
        window.addEventListener('resize', adjustTextSizes);
    });
</script>
<style>
    /* Ensure text overflow handling */
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Make sure address and other potentially long text fields wrap properly */
    .break-words {
        word-wrap: break-word;
        overflow-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
        max-width: 100%;
    }
    
    /* Force email to break and wrap */
    .email-field {
        word-break: break-all !important;
        overflow-wrap: break-word !important;
        white-space: normal !important;
        font-size: 14px !important;
        width: 100% !important;
        display: block !important;
    }
    
    /* QR code responsiveness */
    .qr-image-container {
        max-width: 100%;
        margin: 0 auto;
    }
    
    .qr-image-container img {
        max-width: 100%;
        height: auto;
    }
    
    /* Button container responsive styling */
    @media (max-width: 640px) {
        .button-container {
            width: 100%;
            justify-content: center;
        }
        
        .button-container > * {
            width: 100%;
            text-align: center;
            margin-bottom: 0.5rem;
        }
        
        .qr-card {
            width: 100%;
            padding: 0;
        }
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
        
        body {
            background-color: white;
        }
        
        #printable-card {
            box-shadow: none;
            margin: 0 auto;
            max-width: 100%;
        }
        
        .print-button {
            display: none !important;
        }
        
        /* Ensure page breaks don't happen inside the card */
        #printable-card {
            page-break-inside: avoid;
        }
        
        /* Ensure footer is dark enough to print */
        #printable-card .text-gray-500,
        #printable-card .text-gray-600 {
            color: #111827 !important; /* text-gray-900 */
        }
    }
    
    /* Animation for spinner */
    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Additional styling for text fields */
    .grid-cols-1 > div, .grid-cols-2 > div {
        margin-bottom: 0.5rem;
    }
    
    /* Improved container for better mobile display */
    @media (max-width: 640px) {
        .flex-col {
            align-items: center;
            text-align: center;
        }
    }
</style>
@endpush
@endsection 