@extends('layouts.officer-app')

@section('content')
<!-- Include face-api.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.2/dist/face-api.min.js"></script>
<!-- Include QR scanner library -->
<script src="https://unpkg.com/html5-qrcode"></script>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-red-700">Attendance Scanner</h1>
        <a href="{{ route('officer.dashboard') }}" class="bg-red-50 text-red-700 px-4 py-2 rounded-lg hover:bg-red-100 flex items-center transition duration-150 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">        
        <div>
            <!-- Event Selection -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6 border border-red-100">
                <h2 class="text-xl font-semibold mb-4 text-red-700">Select Event</h2>
                <div class="mb-4">
                    <label for="event_id" class="block text-red-700 text-sm font-bold mb-2">Event:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <select id="event_id" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500">
                            <option value="">Select an event</option>
                            @foreach($activeEvents as $event)
                                @php
                                    $massInfo = '';
                                    if ($event->massSchedule) {
                                        $massType = $event->massSchedule->type === 'sunday_mass' ? 'Sunday Mass' : 'Special Mass';
                                        $massOrder = $event->massSchedule->mass_order ? ucfirst($event->massSchedule->mass_order) . ' Mass' : '';
                                        $massInfo = " - {$massType}" . ($massOrder ? " ({$massOrder})" : "");
                                    }
                                @endphp
                                <option value="{{ $event->id }}" {{ $event->massSchedule && !$event->isAttendanceAllowed() ? 'disabled' : '' }}>
                                    {{ $event->name }} - {{ \Carbon\Carbon::parse($event->time)->format('h:i A') }}{{ $massInfo }}
                                    {{ $event->massSchedule && !$event->isAttendanceAllowed() ? ' (Attendance Closed)' : '' }}
                                </option>
                            @endforeach
                            <option value="other">+ Add Other Mass</option>
                        </select>
                    </div>
                    <div id="event-error" class="hidden mt-2 text-red-600 text-sm">Please select an event before scanning</div>
                </div>
                <div class="flex space-x-4">
                    <!-- QR Code Scanner button removed -->
                </div>
            </div>
            
            <!-- QR Scanner Section -->
            <div id="qrScannerSection" class="bg-white shadow-md rounded-lg p-6 mb-6 border border-red-100">
                <h2 class="text-xl font-semibold mb-4 text-red-700">Scan User ID</h2>
                <div class="flex flex-col items-center">
                    <div id="reader" class="w-full border-2 border-red-100 rounded"></div>
                    <div class="mt-4 text-center">
                        <button id="startButton" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2 transition duration-150 ease-in-out flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Start Scanner
                        </button>
                        <button id="stopButton" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded hidden transition duration-150 ease-in-out flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z" />
                            </svg>
                            Stop Scanner
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Manual Input -->
            <div id="manualInputSection" class="bg-white shadow-md rounded-lg p-6 border border-red-100">
                <h2 class="text-xl font-semibold mb-4 text-red-700">Manual Input</h2>
                <div class="mb-4">
                    <label for="qr_code" class="block text-red-700 text-sm font-bold mb-2">User ID:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <input type="text" id="qr_code" class="pl-10 shadow appearance-none border border-red-200 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-red-500" placeholder="Enter user ID">
                    </div>
                </div>
                <button type="button" id="processManualBtn" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out flex items-center inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Process User ID
                </button>
                <div id="debug-info" class="mt-2 text-xs text-gray-500"></div>
            </div>
        </div>
        
        <!-- Results Section -->
        <div>
            <div class="bg-white shadow-md rounded-lg p-6 h-full border border-red-100">
                <h2 class="text-xl font-semibold mb-4 text-red-700">Scan Results</h2>
                <div id="firebase-status" class="mb-4 p-2 bg-red-50 text-red-700 rounded hidden">
                    <p class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Real-time sync with Firebase is active
                    </p>
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

<!-- Add Mass Modal -->
<div id="addMassModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-red-700">Add Mass for Today</h3>
            <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="quickMassForm">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="massType">
                    Mass Type
                </label>
                <select id="massType" name="massType" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="special_mass">Special Mass</option>
                    <option value="other">Other Event</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="massName">
                    Mass Name
                </label>
                <input type="text" id="massName" name="massName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., Wedding Mass - Johnson Family">
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="startTime">
                        Start Time
                    </label>
                    <input type="time" id="startTime" name="startTime" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="endTime">
                        End Time
                    </label>
                    <input type="time" id="endTime" name="endTime" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="attendanceStartTime">
                        Attendance Start
                    </label>
                    <input type="time" id="attendanceStartTime" name="attendanceStartTime" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="attendanceEndTime">
                        Attendance End
                    </label>
                    <input type="time" id="attendanceEndTime" name="attendanceEndTime" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                    Location
                </label>
                <input type="text" id="location" name="location" value="Church" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
            
            <div class="flex items-center justify-end">
                <button type="button" id="cancelMass" class="text-gray-600 hover:text-gray-800 mr-4">
                    Cancel
                </button>
                <button type="button" id="saveMass" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-150 ease-in-out">
                    Save Mass
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success and Error sounds -->
<script>
    // Create success and error sounds dynamically
    document.addEventListener('DOMContentLoaded', function() {
        // Success sound - short beep
        const successSound = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLHPM+N2fRxUHVrf6/d0vB0Jpl+Tu7T0Qf2Oj3PD0VhmDWozO8fp7JKFOa7/q/qU2rTZYrur/yElBA0yR5v/pYF8HLXfZ//9/gxMRS8n9/4mLKRFNvfn/fGw1FFKm9P9mUDkoXJ3t/1A8RjVog+b/zMxkPn1t0v8AzHBHj1+9/wCId0uZSqj/AJmFIyxQof8AnzopLEuS/wCGWDI6TX7/AG9hPkZRZf8AV2FCT1VL/wA/YkVXWS//ACZhRllcFP8ABFpEWV39/wDrU0BXXub/AMRTPVZf0P8AmlA3UWC5/wBwSzFLYaP/AEhGK0RikP8AI0IlPGN+/wAAOx81ZG3/ABk0GS5lXf8AHy4UJ2VM/wAoKA8hZjz/AC8jCxtmLP8ANCEIFmYd/wA1HwURZg7/ADUdAg1m//8AMhz/CGbx/wAtGvwEZuP/ACcZ+QBm1f8AIBj3/GXH/wAYF/X4Zbr/ABAX8/RlrP8ACRbx8GWf/wACFu/sZZP/AP8V7ehlhv8A/RXr5WV6/wD7Fejhrm7/APoV5t5lYf8A+RXk22VV/wD4FeHYZUj/APcV39VlPP8A9RXd0WUw/wD0FdrOZST/APMV2MtlGP8A8hXWyGUM/wDxFdXFZQD/APAV0sNk9f8A7xXQwGTp/wDuFc69ZN7/AO0Vy7tl0/8A7BXJuWXI/wDrFca2Zb3/AOoVxLRlsv8A6RXCsmWn/wDoFcCwZZz/AOcUvq5lkf8A5hS8rGWH/wDlFLqqZXz/AOQUuKdlcv8A4xS2pWVo/wDjFLSjZV7/AOIUsqFlVP8A4RSvoGVK/wDgFK2eZUD/AN8Uq5xlNv8A3hSpmmUs/wDdFKeYZSP/ANwUpZZlGf8A3BRnlWUP/wDbFGOTZQX/ANsUYJJl+/8A2hRckWXx/wDZFFmPZef/ANkUVo5l3f8A2BRTjGXT/wDXFLmLZcn/ANcU5IplwP8A1hRRiGW2/wDVFE+GZaz/ANUUTIVlov8A1BRKg2WY/wDTFEeDZY7/ANMURoJlhP8A0hREgGV7/wDRFEJ/ZXH/ANEUQHxlaP8A0BQ+e2Ve/wDPFDx6ZVT/AM8UOnllSv8AzhQ4d2VB/wDNFDZ2ZTf/AM0UNHRlLf8AzBQyc2Uj/wDLFDByZRr/AMsULnBlEP8AyhQtb2UH/wDJFCttZf3+AMkUKWxl8/4AyBQna2Xp/gDHFCVqZeD+AMYUIWhk1v4AxhQfZ2TN/gDFFB1mZMP+AMQUGmRkuv4AwxQYY2Sx/gDDFBZiZKj+AMIUE2BknvwAwRURX2SV/ADBFKleZIz8AMAUpl1kgvwAvxSkXGR5/AC+FKFbZHD8AL4UnlpkZ/wAvRScWWRe/AC8FJlYZFX8ALsUl1dkTPwAuxSVVmRC+wC6FJJUZDn7ALoUkFNkMPsAuRSOUmQn+wC5FIxRZB77ALgUiVBkFfsAtxSHT2QM+wC3FIVOZATyALYUg01k++8AthSBTGTz7wC1FH9LZOrvALQUfEpk4u8AtBR6SWTa7wCzFHhIZNLvALMUdkdk/+4AshR0RmT37gCxFHFFZO/uALEUb0Rk5+4AsBRtQ2Tf7gCvFGtCZNfuAK8UaUFkz+4ArhRnQGTH7gCuFGU/ZL/uAK0UYz5kt+4ArBRhPWTLrQCsFEQ8ZLmtAKsUSjtksq0AqhRIOmSqrQCpFEY5ZKKtAKkUQzhkmq0AqBRAOWSSrQCnFD44ZIqtAKcUPDdkgq0AphQ6NmR6rQClFDg1ZHKtAKUUNjRkaq0ApBQzM2RjrQCjFDEyZIicAKMULzFkgJwAohQtMGR4nACsFGEuZGucAKsUYC1kYw==');
        successSound.id = 'success-sound';
        document.body.appendChild(successSound);
        
        // Error sound - short low tone
        const errorSound = new Audio('data:audio/wav;base64,UklGRnQFAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YVAFAAAAAAABAAIAAwAEAAUABgAHAAgACQAKAAsADAAOAA8AEAARABMAFAAVABcAGAAZABsAHAAdAB8AIAAiACMAJQAmACgAKQArACwALgAvADEAMwA0ADYANwA5ADsAPAA+AEAAQQBDAEUARgBIAEoASwBNAE8AUQBSAFQAVgBYAFkAWwBdAF8AYQBjAGQAZgBoAGoAbABuAHAAcgB0AHYAeAB6AHwAfgCAAIIAhACGAIgAigCMAI4AkQCTAJUAlwCZAJsAnwChAKMAoQCkAKYAqACrAK0ArwCxALQAtgC4ALsAvQC/AMIAxADHAMkAzADOANEA0wDWANgA2wDdAOAA4wDlAOgA6gDtAO8A8gD1APcA+gD9AP8AAgEFAQcBCgENARABEwEVARgBGwEeASEBJAEmASkBLAEvATIBNQE4ATsBPgFBAUQBRwFKAU0BUAFTAVYBWQFcAV8BYgFlAWgBbAFvAXIBdQF4AXsBfwGCAYUBiQGMAY8BkwGWAZkBnQGgAaMBpwGqAa4BsQG1AbgBvAG/AcMBxgHKAc4B0QHVAdkB3AHgAeQB6AHrAe8B8wH3AfoB/gECAgYCCgIOAhICFgIaAh4CIgImAioCLgIyAjcCOwI/AkMCSAJMAk8CUwJXAl0CYQJlAmkCbAJxAnUCeQJ9AoIChgKKAo8CkwKXApwCoAKkAqkCrQKyArYCuwK/AsQCyALNAtEC1gLbAt8C5ALpAu0C8gL3AvwDAQMFAwoDDwMUAxkDHgMjAygDLQMyAzcDPANBA0YDSgNQA1UDWgNfA2QDaQNuA3QDeQN+A4MDiAOOA5MDmAOdA6IDqAOtA7IDuAO9A8IDyAPNA9ID2APdA+ID6APtA/ID+AQABA8EFQQZBB8EJAQpBC8ENAQ5BD8ERQRKBFAEVQRWBF0EYgRoBG0EcwR4BH4EgwSJBI4ElASaBJ8EpQSqBLAEtQS7BMEExgTMBNEE1wTdBOIE6ATuBPME+QT/BQQFCgUQBRUFGwUhBScFLAUyBTgFPgVDBUkFTwVVBVoFYAVmBWwFcgV4BX4FgwWJBY8FlQWbBaEFpwWtBbMFuQW/BcUFywXRBdYF3AXiBegF7gX0BfoGAAYGBgwGEgYYBh4GJAYqBjAGNgY8BkIGSAZOBlQGWgZgBmYGbAZyBngGfgaEBooGkAaWBpwGogaoBq4GtAa6BsAGxgbMBtIG2AbeBuQG6gbwBvYG/AcCBwgHDgcUBxoHIAcmBywHMgc4Bz4HRAcFBwsHEQdHB0wHUgdYB14HZAdrB3AHdgd8B4IHiAeOB5QHmgegB6YHrAeyB7gHvgfEB8oH0AfWB9wH4gfoB+4H9Af6CAAIAA==');
        errorSound.id = 'error-sound';
        document.body.appendChild(errorSound);
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // QR Scanner logic
    let html5QrCode = null;
    let isScanning = false;
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const reader = document.getElementById('reader');
    const eventSelect = document.getElementById('event_id');
    const eventError = document.getElementById('event-error');
    const processManualBtn = document.getElementById('processManualBtn');
    const qrCodeInput = document.getElementById('qr_code');
    const debugInfo = document.getElementById('debug-info');
    const resultsList = document.getElementById('results-list');
    const noResults = document.getElementById('no-results');

    function showResult(result, success = true) {
        noResults.classList.add('hidden');
        resultsList.classList.remove('hidden');
        const div = document.createElement('div');
        div.className = 'p-2';
        div.innerHTML = `<span class='${success ? 'text-green-700' : 'text-red-700'} font-bold'>${result}</span>`;
        resultsList.prepend(div);
        // Play sound
        try {
            document.getElementById(success ? 'success-sound' : 'error-sound').play();
        } catch (e) {}
    }

    function processUserId(userId) {
        if (!eventSelect.value) {
            eventError.classList.remove('hidden');
            showResult('Please select an event before scanning.', false);
            return;
        }
        eventError.classList.add('hidden');
        // AJAX to backend (adjust URL as needed)
        fetch('/officer/scan/process', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']').getAttribute('content')
            },
            body: JSON.stringify({
                user_id: userId,
                event_id: eventSelect.value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showResult(data.message || 'Attendance recorded!', true);
            } else {
                showResult(data.message || 'Error processing attendance.', false);
            }
        })
        .catch(() => showResult('Server error.', false));
    }

    // Start Scanner
    startButton.addEventListener('click', function() {
        if (!eventSelect.value) {
            eventError.classList.remove('hidden');
            showResult('Please select an event before scanning.', false);
            return;
        }
        eventError.classList.add('hidden');
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('reader');
        }
        startButton.classList.add('hidden');
        stopButton.classList.remove('hidden');
        html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            (decodedText, decodedResult) => {
                console.log('Scan callback fired!');
                alert('Scanned: ' + decodedText);
                if (isScanning) return; // Prevent double scan
                isScanning = true;
                html5QrCode.stop().then(() => {
                    startButton.classList.remove('hidden');
                    stopButton.classList.add('hidden');
                    isScanning = false;
                });
                processUserId(decodedText);
            },
            (errorMessage) => {
                console.log('Scan error:', errorMessage);
            }
        ).catch(err => {
            showResult('Camera error: ' + err, false);
            alert('Camera error: ' + err);
            startButton.classList.remove('hidden');
            stopButton.classList.add('hidden');
        });
    });

    // Stop Scanner
    stopButton.addEventListener('click', function() {
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                startButton.classList.remove('hidden');
                stopButton.classList.add('hidden');
            });
        }
    });

    // Manual Input
    processManualBtn.addEventListener('click', function() {
        const userId = qrCodeInput.value.trim();
        if (!userId) {
            debugInfo.textContent = 'Please enter a user ID.';
            return;
        }
        debugInfo.textContent = '';
        processUserId(userId);
    });

    // QR Code Scanner button logic removed
});
</script>
@endsection
