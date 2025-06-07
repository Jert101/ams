<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="flex items-center justify-start mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-transparent border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="bi bi-arrow-left me-2"></i>
                Back to Home
            </a>
        </div>

        <h2 class="text-2xl font-bold mb-4 text-center text-[#B22234]">Facial Authentication</h2>
        <p class="text-center text-gray-600 mb-6">Look directly at the camera for face verification</p>

        @if (session('error'))
            <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Authentication Error</h3>
                        <div class="mt-1 text-sm text-red-700">
                            {{ session('error') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <div class="flex flex-col space-y-4">
                <div class="bg-gray-100 p-4 rounded-md shadow-inner">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none z-10">
                            <div id="face-guide" class="w-3/5 h-4/5 border-2 border-dashed border-blue-400 rounded-full opacity-70"></div>
                        </div>
                        <video id="video" width="100%" height="auto" class="rounded-md border border-gray-300 bg-black" autoplay muted></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                        <div id="face-overlay" class="absolute top-0 left-0 w-full h-full pointer-events-none"></div>
                        <div id="loading-indicator" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-75 z-20">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-700 inline-block"></div>
                                <p class="mt-2 text-gray-700 font-medium">Initializing camera...</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div id="status-message" class="text-center py-2 px-3 rounded-md bg-blue-50 text-blue-700"></div>
                        <div id="quality-indicator" class="mt-2 hidden">
                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                <span>Image Quality</span>
                                <span id="quality-text">Good</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="quality-bar" class="bg-green-600 h-2.5 rounded-full" style="width: 70%"></div>
                            </div>
                        </div>
                        <form id="facial-login-form" action="{{ route('facial.verify') }}" method="POST" enctype="multipart/form-data" class="hidden">
                            @csrf
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="file" name="face_image" id="face_image" class="hidden">
                        </form>
                    </div>
                </div>

                <div class="flex flex-col space-y-2 mt-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3 text-sm text-blue-700">
                                <p>Position your face within the guide and look directly at the camera.</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('login') }}" class="mt-4 text-center py-2 px-4 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Use Password Login Instead') }}
                    </a>
                </div>
            </div>
        </div>
    </x-authentication-card>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const faceOverlay = document.getElementById('face-overlay');
            const statusMessage = document.getElementById('status-message');
            const loadingIndicator = document.getElementById('loading-indicator');
            const facialLoginForm = document.getElementById('facial-login-form');
            const userIdInput = document.getElementById('user_id');
            const fileInput = document.getElementById('face_image');
            const qualityIndicator = document.getElementById('quality-indicator');
            const qualityBar = document.getElementById('quality-bar');
            const qualityText = document.getElementById('quality-text');
            
            let stream = null;
            let isModelLoaded = false;
            let faceDetectionInterval = null;
            let detectionQuality = 0;
            
            statusMessage.textContent = "Loading facial recognition models...";
            
            // Load face-api.js models
            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models/face-api'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models/face-api'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models/face-api')
            ]).then(() => {
                isModelLoaded = true;
                statusMessage.textContent = "Models loaded. Starting camera...";
                startVideo();
            }).catch(err => {
                console.error('Error loading face-api models:', err);
                statusMessage.textContent = "Error loading facial recognition models. Please try again later.";
                loadingIndicator.style.display = 'none';
            });
            
            function startVideo() {
                navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        width: { ideal: 1280 },
                        height: { ideal: 720 },
                        facingMode: 'user'
                    } 
                })
                .then(function(s) {
                    stream = s;
                    video.srcObject = stream;
                    loadingIndicator.style.display = 'none';
                    statusMessage.textContent = "Looking for your face...";
                })
                .catch(function(err) {
                    console.error("Error accessing the camera:", err);
                    statusMessage.textContent = "Could not access the camera. Please make sure you have granted camera permissions.";
                    loadingIndicator.style.display = 'none';
                });
            }
            
            video.addEventListener('play', () => {
                if (!isModelLoaded) return;
                
                // Start face detection when video starts playing
                faceDetectionInterval = setInterval(async () => {
                    try {
                        const detections = await faceapi.detectAllFaces(
                            video, 
                            new faceapi.TinyFaceDetectorOptions({
                                inputSize: 512,
                                scoreThreshold: 0.5
                            })
                        ).withFaceLandmarks();
                        
                        // Clear previous detections
                        faceOverlay.innerHTML = '';
                        
                        if (detections.length > 0) {
                            // Sort detections by size (larger faces are closer and more likely the user)
                            detections.sort((a, b) => {
                                const areaA = a.detection.box.width * a.detection.box.height;
                                const areaB = b.detection.box.width * b.detection.box.height;
                                return areaB - areaA;
                            });
                            
                            const detection = detections[0]; // Use the largest face
                            const box = detection.detection.box;
                            
                            // Calculate detection quality based on face size and position
                            const videoArea = video.videoWidth * video.videoHeight;
                            const faceArea = box.width * box.height;
                            const faceRatio = (faceArea / videoArea) * 100;
                            
                            // Measure face centering
                            const centerX = video.videoWidth / 2;
                            const centerY = video.videoHeight / 2;
                            const faceX = box.x + (box.width / 2);
                            const faceY = box.y + (box.height / 2);
                            
                            const xOffset = Math.abs(centerX - faceX) / centerX;
                            const yOffset = Math.abs(centerY - faceY) / centerY;
                            const positionScore = 100 - ((xOffset + yOffset) * 50);
                            
                            // Final quality score (0-100)
                            detectionQuality = Math.min(100, Math.max(0, 
                                (faceRatio * 3) * 0.7 + positionScore * 0.3
                            ));
                            
                            // Update quality indicator
                            updateQualityIndicator(detectionQuality);
                            
                            // Create an enhanced face box with better styling
                            const faceBox = document.createElement('div');
                            faceBox.style.position = 'absolute';
                            faceBox.style.left = `${box.x}px`;
                            faceBox.style.top = `${box.y}px`;
                            faceBox.style.width = `${box.width}px`;
                            faceBox.style.height = `${box.height}px`;
                            faceBox.style.border = '2px solid rgba(66, 153, 225, 0.8)';
                            faceBox.style.borderRadius = '4px';
                            faceBox.style.boxShadow = '0 0 0 2px rgba(255,255,255,0.3)';
                            
                            // Draw landmarks as dots for a professional look
                            const landmarks = detection.landmarks;
                            const points = landmarks.positions;
                            
                            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                            svg.setAttribute('width', '100%');
                            svg.setAttribute('height', '100%');
                            svg.style.position = 'absolute';
                            svg.style.top = '0';
                            svg.style.left = '0';
                            svg.style.pointerEvents = 'none';
                            
                            points.forEach(point => {
                                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                                circle.setAttribute('cx', point.x);
                                circle.setAttribute('cy', point.y);
                                circle.setAttribute('r', '1');
                                circle.setAttribute('fill', 'rgba(66, 153, 225, 0.8)');
                                svg.appendChild(circle);
                            });
                            
                            faceOverlay.appendChild(faceBox);
                            faceOverlay.appendChild(svg);
                            
                            statusMessage.textContent = "Face detected!";
                            
                            // Verify if quality is good enough
                            if (detectionQuality >= 70) {
                                // Capture the face image after a short stabilization period
                                setTimeout(() => {
                                    // Only proceed if we still have good quality
                                    if (detectionQuality >= 70) {
                                        statusMessage.textContent = "Face detected! Verifying...";
                                        
                                        // Capture the face image
                                        canvas.width = video.videoWidth;
                                        canvas.height = video.videoHeight;
                                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                                        
                                        // Convert to base64
                                        const imageData = canvas.toDataURL('image/jpeg', 0.95);
                                        
                                        // Send to API for verification
                                        verifyFace(imageData);
                                        
                                        // Stop the interval to prevent multiple requests
                                        clearInterval(faceDetectionInterval);
                                    }
                                }, 500);
                            } else {
                                statusMessage.textContent = "Position your face properly for verification";
                            }
                        } else {
                            // No face detected
                            statusMessage.textContent = "No face detected. Please position your face in the camera.";
                            qualityIndicator.classList.add('hidden');
                        }
                    } catch (err) {
                        console.error('Face detection error:', err);
                        statusMessage.textContent = "Error processing face. Please try again.";
                    }
                }, 200); // More frequent updates for smoother experience
            });
            
            function updateQualityIndicator(quality) {
                qualityIndicator.classList.remove('hidden');
                qualityBar.style.width = `${quality}%`;
                
                if (quality < 40) {
                    qualityBar.classList.remove('bg-yellow-500', 'bg-green-600');
                    qualityBar.classList.add('bg-red-500');
                    qualityText.textContent = 'Poor';
                } else if (quality < 70) {
                    qualityBar.classList.remove('bg-red-500', 'bg-green-600');
                    qualityBar.classList.add('bg-yellow-500');
                    qualityText.textContent = 'Fair';
                } else {
                    qualityBar.classList.remove('bg-red-500', 'bg-yellow-500');
                    qualityBar.classList.add('bg-green-600');
                    qualityText.textContent = 'Good';
                }
            }
            
            function verifyFace(imageData) {
                statusMessage.textContent = "Verifying your face...";
                
                // Get the CSRF token from the meta tag
                const csrfToken = document.querySelector('meta[name="csrf-token"]') 
                    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                    : '';
                
                fetch('/api/facial-recognition/verify', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        face_image: imageData
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        statusMessage.textContent = `Welcome, ${data.user.name}! Logging you in...`;
                        qualityIndicator.classList.add('hidden');
                        
                        // Add success animation
                        faceOverlay.innerHTML = '';
                        const successOverlay = document.createElement('div');
                        successOverlay.style.position = 'absolute';
                        successOverlay.style.inset = '0';
                        successOverlay.style.display = 'flex';
                        successOverlay.style.alignItems = 'center';
                        successOverlay.style.justifyContent = 'center';
                        successOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        successOverlay.innerHTML = `
                            <div class="bg-white rounded-full p-4">
                                <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        `;
                        faceOverlay.appendChild(successOverlay);
                        
                        // Prepare form submission with the matched user ID
                        userIdInput.value = data.user.id;
                        
                        // Convert base64 to blob and create a File object
                        const byteString = atob(imageData.split(',')[1]);
                        const mimeString = imageData.split(',')[0].split(':')[1].split(';')[0];
                        const ab = new ArrayBuffer(byteString.length);
                        const ia = new Uint8Array(ab);
                        
                        for (let i = 0; i < byteString.length; i++) {
                            ia[i] = byteString.charCodeAt(i);
                        }
                        
                        const blob = new Blob([ab], { type: mimeString });
                        const file = new File([blob], 'face-verification.jpg', { type: 'image/jpeg' });
                        
                        // Create a FileList-like object
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        
                        // Set the file input's files
                        fileInput.files = dataTransfer.files;
                        
                        // Submit the form
                        setTimeout(() => {
                            facialLoginForm.classList.remove('hidden');
                            facialLoginForm.submit();
                        }, 1000);
                    } else {
                        statusMessage.textContent = "Face verification failed. Please try again.";
                        
                        // Add failure animation
                        faceOverlay.innerHTML = '';
                        const failureOverlay = document.createElement('div');
                        failureOverlay.style.position = 'absolute';
                        failureOverlay.style.inset = '0';
                        failureOverlay.style.display = 'flex';
                        failureOverlay.style.alignItems = 'center';
                        failureOverlay.style.justifyContent = 'center';
                        failureOverlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                        failureOverlay.innerHTML = `
                            <div class="bg-white rounded-full p-4">
                                <svg class="w-16 h-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                        `;
                        faceOverlay.appendChild(failureOverlay);
                        
                        // Restart face detection after a short delay
                        setTimeout(() => {
                            startFaceDetection();
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    statusMessage.textContent = "An error occurred during verification. Please try again later.";
                    // Restart face detection
                    setTimeout(() => {
                        startFaceDetection();
                    }, 2000);
                });
            }
            
            function startFaceDetection() {
                // Clear any existing intervals
                if (faceDetectionInterval) {
                    clearInterval(faceDetectionInterval);
                }
                
                // Clear overlays
                faceOverlay.innerHTML = '';
                
                // Reset quality indicator
                qualityIndicator.classList.add('hidden');
                
                // Restart detection with a fresh interval
                video.addEventListener('play', () => {
                    if (!isModelLoaded) return;
                    
                    faceDetectionInterval = setInterval(async () => {
                        // ... (same detection code as above)
                    }, 200);
                });
            }
            
            // Clean up on page unload
            window.addEventListener('beforeunload', () => {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                if (faceDetectionInterval) {
                    clearInterval(faceDetectionInterval);
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
