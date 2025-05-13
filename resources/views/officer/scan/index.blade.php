@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">QR Code Scanner</h1>
        <a href="{{ route('officer.dashboard') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Dashboard
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <!-- Event Selection -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Select Event</h2>
                <div class="mb-4">
                    <label for="event_id" class="block text-gray-700 text-sm font-bold mb-2">Event:</label>
                    <select id="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select an event</option>
                        @foreach ($activeEvents as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->date }})</option>
                        @endforeach
                    </select>
                    <div id="event-error" class="text-red-500 text-xs italic mt-1 hidden">Please select an event first.</div>
                </div>
            </div>
            
            <!-- QR Code Scanner -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Scan QR Code</h2>
                <div class="flex flex-col items-center">
                    <div id="reader" class="w-full"></div>
                    <div class="mt-4 text-center">
                        <button id="startButton" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded mr-2">
                            Start Scanner
                        </button>
                        <button id="stopButton" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded hidden">
                            Stop Scanner
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Manual Input -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Manual Input</h2>
                <div class="mb-4">
                    <label for="qr_code" class="block text-gray-700 text-sm font-bold mb-2">QR Code:</label>
                    <input type="text" id="qr_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter QR code manually">
                </div>
                <button id="submitManual" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Process Manual Code
                </button>
            </div>
        </div>
        
        <!-- Results Section -->
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 h-full">
                <h2 class="text-xl font-semibold mb-4">Scan Results</h2>
                <div id="firebase-status" class="mb-4 p-2 bg-blue-50 text-blue-700 rounded hidden">
                    <p><i class="fas fa-info-circle"></i> Real-time sync with Firebase is active</p>
                </div>
                <div id="results-container" class="overflow-y-auto max-h-96">
                    <div id="no-results" class="text-center py-10 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p class="mt-2">No scans performed yet</p>
                    </div>
                    <div id="results-list" class="divide-y divide-gray-200 hidden">
                        <!-- Scan results will be appended here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let html5QrCode;
        const reader = document.getElementById('reader');
        const startButton = document.getElementById('startButton');
        const stopButton = document.getElementById('stopButton');
        const eventSelect = document.getElementById('event_id');
        const eventError = document.getElementById('event-error');
        const manualInput = document.getElementById('qr_code');
        const submitManual = document.getElementById('submitManual');
        const resultsList = document.getElementById('results-list');
        const noResults = document.getElementById('no-results');
        
        function initScanner() {
            html5QrCode = new Html5Qrcode("reader");
        }
        
        function startScanner() {
            const eventId = eventSelect.value;
            if (!eventId) {
                eventError.classList.remove('hidden');
                return;
            }
            eventError.classList.add('hidden');
            
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                processQrCode(decodedText);
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .then(() => {
                    startButton.classList.add('hidden');
                    stopButton.classList.remove('hidden');
                })
                .catch((err) => {
                    console.error(`Error starting scanner: ${err}`);
                    alert("Error starting QR code scanner. Please check camera permissions.");
                });
        }
        
        function stopScanner() {
            if (html5QrCode && html5QrCode.isScanning) {
                html5QrCode.stop()
                    .then(() => {
                        startButton.classList.remove('hidden');
                        stopButton.classList.add('hidden');
                    })
                    .catch((err) => {
                        console.error(`Error stopping scanner: ${err}`);
                    });
            }
        }
        
        function processQrCode(qrCode) {
            const eventId = eventSelect.value;
            if (!eventId) {
                eventError.classList.remove('hidden');
                return;
            }
            eventError.classList.add('hidden');
            
            // If scanner is running, stop it temporarily
            const wasScanning = html5QrCode && html5QrCode.isScanning;
            if (wasScanning) {
                stopScanner();
            }
            
            // Make AJAX request to process the QR code
            fetch('{{ route("officer.scan.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    qr_code: qrCode,
                    event_id: eventId
                })
            })
            .then(response => response.json())
            .then(data => {
                displayResult(data);
                
                // If successful, also record in Firebase for real-time updates
                if (data.success && window.firebaseAttendance) {
                    window.firebaseAttendance.recordAttendance(
                        data.user_id,
                        data.event_id,
                        data.status,
                        data.approved_by
                    );
                }
                
                if (wasScanning) {
                    // Restart the scanner after a brief pause
                    setTimeout(() => {
                        startScanner();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing QR code. Please try again.');
                if (wasScanning) {
                    startScanner();
                }
            });
        }
        
        function displayResult(data) {
            // Show results container
            noResults.classList.add('hidden');
            resultsList.classList.remove('hidden');
            
            // Create result item
            const resultItem = document.createElement('div');
            resultItem.className = 'py-4';
            
            // Set success or error styling
            let statusClass = data.success ? 'text-green-600' : 'text-red-600';
            let statusIcon = data.success ? '✓' : '✗';
            
            // Format timestamp
            const timestamp = new Date().toLocaleTimeString();
            
            // Set HTML content
            resultItem.innerHTML = `
                <div class="flex items-start">
                    <div class="${statusClass} text-2xl font-bold mr-3">${statusIcon}</div>
                    <div class="flex-1">
                        <p class="${statusClass} font-semibold">${data.message}</p>
                        ${data.user ? `<p class="text-sm">User: ${data.user}</p>` : ''}
                        ${data.event ? `<p class="text-sm">Event: ${data.event}</p>` : ''}
                        ${data.status ? `<p class="text-sm">Status: ${data.status}</p>` : ''}
                        <p class="text-xs text-gray-500 mt-1">${timestamp}</p>
                    </div>
                </div>
            `;
            
            // Add to results list
            resultsList.prepend(resultItem);
        }
        
        // Initialize
        initScanner();
        
        // Event listeners
        startButton.addEventListener('click', startScanner);
        stopButton.addEventListener('click', stopScanner);
        submitManual.addEventListener('click', function() {
            const qrCode = manualInput.value.trim();
            if (qrCode) {
                processQrCode(qrCode);
                manualInput.value = '';
            }
        });
        
        // Handle Enter key in manual input
        manualInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                submitManual.click();
            }
        });
    });
</script>
@endpush
@endsection
