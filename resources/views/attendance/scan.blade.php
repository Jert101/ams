<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('QR Code Scanner') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- React QR Code Scanner -->
            <div 
                data-react-root 
                data-component="QRCodeScanner"
                data-props='{
                    "events": @json($events ?? [])
                }'
            ></div>
            
            <!-- Fallback HTML Content (displayed if React fails) -->
            <div id="qrcodescanner-fallback-content" style="display: none;">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">Scan QR Code</h3>
                        
                        <div class="mb-4">
                            <p class="text-gray-600 mb-2">Please use the camera to scan a member's QR code or enter the code manually.</p>
                            
                            <div class="flex flex-col md:flex-row gap-4">
                                <div class="md:w-1/2">
                                    <div class="bg-gray-100 rounded-lg p-4 text-center">
                                        <div id="qr-video-container" class="mb-4">
                                            <video id="qr-video" width="100%" class="rounded border border-gray-300"></video>
                                        </div>
                                        <button id="start-camera" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Start Camera
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="md:w-1/2">
                                    <form action="{{ route('attendance.record') }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div>
                                            <label for="code" class="block text-sm font-medium text-gray-700">QR Code</label>
                                            <input type="text" name="code" id="qr-result" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Enter code manually or scan QR">
                                        </div>
                                        
                                        <div>
                                            <label for="event_id" class="block text-sm font-medium text-gray-700">Event</label>
                                            <select name="event_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                @foreach($events ?? [] as $event)
                                                    <option value="{{ $event->id }}">{{ $event->name }} ({{ $event->date }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div>
                                            <button type="submit" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                Record Attendance
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div id="scan-result" class="mt-4 p-4 bg-green-100 rounded-lg hidden">
                            <p class="text-green-800 font-medium">Scan successful!</p>
                            <p id="scan-result-name" class="text-green-700"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Only initialize the vanilla JS scanner if the fallback is shown
            if (document.getElementById('qrcodescanner-fallback-content').style.display !== 'none') {
                const startCameraButton = document.getElementById('start-camera');
                const videoElement = document.getElementById('qr-video');
                const qrResultInput = document.getElementById('qr-result');
                const scanResult = document.getElementById('scan-result');
                const scanResultName = document.getElementById('scan-result-name');
                
                let scanning = false;
                
                startCameraButton.addEventListener('click', function() {
                    if (scanning) return;
                    
                    scanning = true;
                    startCameraButton.textContent = 'Scanning...';
                    
                    // Check if browser supports getUserMedia
                    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
                            .then(function(stream) {
                                videoElement.srcObject = stream;
                                videoElement.setAttribute("playsinline", true); // required for iOS
                                videoElement.play();
                                
                                // Here you would typically initialize a QR code scanning library
                                // For demonstration, we'll just simulate a scan after 3 seconds
                                setTimeout(function() {
                                    const mockCode = 'MEMBER' + Math.floor(Math.random() * 1000);
                                    qrResultInput.value = mockCode;
                                    scanResult.classList.remove('hidden');
                                    scanResultName.textContent = 'Member code: ' + mockCode;
                                    
                                    // Stop camera
                                    const tracks = stream.getTracks();
                                    tracks.forEach(function(track) {
                                        track.stop();
                                    });
                                    
                                    scanning = false;
                                    startCameraButton.textContent = 'Start Camera';
                                }, 3000);
                            })
                            .catch(function(error) {
                                console.error("Camera error:", error);
                                alert("Unable to access camera: " + error.message);
                                scanning = false;
                                startCameraButton.textContent = 'Start Camera';
                            });
                    } else {
                        alert("Sorry, your browser does not support camera access");
                        scanning = false;
                        startCameraButton.textContent = 'Start Camera';
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout> 