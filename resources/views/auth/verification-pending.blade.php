<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Pending - KofA AMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            background-color: #991b1b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .verification-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 30px;
        }
        
        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo-container img {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .logo-text {
            color: #eab308;
            font-weight: bold;
            font-size: 20px;
            margin-top: 10px;
        }
        
        .page-title {
            color: #991b1b;
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .success-icon {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .success-icon svg {
            width: 50px;
            height: 50px;
            color: #059669;
            background-color: #d1fae5;
            padding: 10px;
            border-radius: 50%;
        }
        
        .message {
            text-align: center;
            color: #333;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .notice {
            background-color: #fff7ed;
            border-left: 4px solid #f59e0b;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .notice-title {
            display: flex;
            align-items: center;
            margin-bottom: 6px;
            color: #92400e;
            font-weight: bold;
        }
        
        .notice-title svg {
            width: 16px;
            height: 16px;
            margin-right: 6px;
            color: #f59e0b;
        }
        
        .status-area {
            min-height: 60px;
            margin-bottom: 20px;
        }
        
        .status-box {
            border-radius: 4px;
            padding: 12px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }
        
        .status-box svg {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            flex-shrink: 0;
        }
        
        .status-box.checking {
            background-color: #eff6ff;
            color: #1e40af;
        }
        
        .status-box.approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-box.rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .status-box.error {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .button-group {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: #991b1b;
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #7f1d1d;
        }
        
        .btn-secondary {
            background-color: #2563eb;
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-secondary:hover {
            background-color: #1d4ed8;
        }
        
        .btn-secondary svg {
            width: 14px;
            height: 14px;
            margin-right: 6px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .animate-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="logo-container">
            <img src="/kofa.png" alt="KofA Logo">
            <div class="logo-text">AMS</div>
        </div>
        
        <h2 class="page-title">Registration Successful!</h2>
        
        <div class="success-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        
        <div class="message">
            Your account has been created and is pending approval by an administrator. You will not be able to log in until your account has been approved.
        </div>
        
        <div class="notice">
            <div class="notice-title">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>Important</span>
            </div>
            Please check back later or contact the administrator for assistance.
        </div>
        
        <div id="status-area" class="status-area">
            <!-- Status will be added here dynamically -->
        </div>
        
        <div class="button-group">
            <a href="{{ route('login') }}" class="btn btn-primary">
                Return to Login
            </a>
            <button id="check-status-btn" type="button" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Check Status
            </button>
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <button id="diagnostic-btn" type="button" style="background: transparent; border: 1px dashed #ccc; padding: 5px 10px; border-radius: 4px; color: #999; font-size: 12px;">
                Diagnostic Check
            </button>
            <div id="diagnostic-results" style="margin-top: 10px; font-size: 12px; color: #666; text-align: left; padding: 8px; background: #f8f8f8; border-radius: 4px; display: none;"></div>
        </div>
    </div>

    <!-- Templates -->
    <template id="checking-template">
        <div class="status-box checking fade-in">
            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Checking your approval status...</span>
        </div>
    </template>
    
    <template id="approved-template">
        <div class="status-box approved fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>Great news! Your account has been approved. You will be redirected to the login page shortly.</span>
        </div>
    </template>
    
    <template id="rejected-template">
        <div class="status-box rejected fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <span>Your account registration has been rejected. Please contact the administrator for more information.</span>
        </div>
    </template>
    
    <template id="error-template">
        <div class="status-box error fade-in">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <span id="error-message">There was an error checking your status. Please try again later.</span>
        </div>
    </template>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const email = "{{ $email ?? '' }}";
            const statusArea = document.getElementById('status-area');
            const checkStatusBtn = document.getElementById('check-status-btn');
            
            // Templates
            const checkingTemplate = document.getElementById('checking-template');
            const approvedTemplate = document.getElementById('approved-template');
            const rejectedTemplate = document.getElementById('rejected-template');
            const errorTemplate = document.getElementById('error-template');
            
            // Helper function to show a status message
            function showStatus(template, customMessage = null) {
                // Clear previous status
                statusArea.innerHTML = '';
                
                // Clone the template content and add it to the status area
                const content = template.content.cloneNode(true);
                const element = content.firstElementChild;
                
                // If this is an error message and we have a custom message, update it
                if (template === errorTemplate && customMessage) {
                    const messageSpan = element.querySelector('#error-message');
                    if (messageSpan) {
                        messageSpan.textContent = customMessage;
                    }
                }
                
                statusArea.appendChild(element);
                return element;
            }
            
            function checkApprovalStatus() {
                // Skip if no email is available
                if (!email) {
                    showStatus(errorTemplate, "No email address available for status check. Please try logging in again.");
                    return;
                }
                
                // Show checking status
                showStatus(checkingTemplate);
                
                // Disable button while checking
                checkStatusBtn.disabled = true;
                checkStatusBtn.classList.add('opacity-70', 'cursor-not-allowed');
                
                console.log('Checking status for email:', email);
                
                // Add a random cache-busting parameter to avoid cached responses
                const cacheBuster = new Date().getTime();
                
                // Get the CSRF token from meta tag
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                console.log('CSRF Token for status check:', token ? 'Present (length: ' + token.length + ')' : 'Missing');
                
                fetch(`/check-approval-status?_=${cacheBuster}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Cache-Control': 'no-cache, no-store, must-revalidate',
                        'Pragma': 'no-cache',
                        'Expires': '0',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    
                    if (data.status === 'success') {
                        console.log('Status check successful, approval_status:', data.approval_status);
                        
                        if (data.approval_status === 'approved') {
                            console.log('User is approved, showing approved message');
                            showStatus(approvedTemplate);
                            setTimeout(() => {
                                console.log('Redirecting to:', data.redirect || '/login');
                                window.location.href = data.redirect || '/login';
                            }, 3000);
                        } else if (data.approval_status === 'rejected') {
                            console.log('User is rejected, showing rejected message');
                            showStatus(rejectedTemplate);
                        } else {
                            // Still pending, clear status area
                            console.log('User is still pending, clearing status area');
                            statusArea.innerHTML = '';
                        }
                    } else {
                        console.error('Status check failed:', data.message);
                        showStatus(errorTemplate, data.message || "Error checking status. Please try again.");
                    }
                    
                    // Re-enable button
                    checkStatusBtn.disabled = false;
                    checkStatusBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                    let errorMessage = "Connection error. Please check your internet connection and try again.";
                    showStatus(errorTemplate, errorMessage);
                    checkStatusBtn.disabled = false;
                    checkStatusBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                });
            }
            
            // Setup button click handler
            checkStatusBtn.addEventListener('click', checkApprovalStatus);
            
            // Auto check status every 30 seconds ONLY if we have an email
            let intervalId = null;
            if (email) {
                intervalId = setInterval(checkApprovalStatus, 30000);
                
                // Initial check on page load with slight delay
                setTimeout(checkApprovalStatus, 1000);
            } else {
                // Show an error message if no email is available
                showStatus(errorTemplate, "No email address available. Please try registering again.");
            }
            
            // Clean up interval on page unload
            window.addEventListener('beforeunload', function() {
                if (intervalId) {
                    clearInterval(intervalId);
                }
            });
            
            // Add diagnostic button for development environments
            const diagnosticBtn = document.getElementById('diagnostic-btn');
            const diagnosticResults = document.getElementById('diagnostic-results');
            
            if (diagnosticBtn) {
                diagnosticBtn.addEventListener('click', function() {
                    diagnosticBtn.disabled = true;
                    diagnosticBtn.textContent = 'Checking...';
                    
                    // Get the CSRF token from meta tag
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    console.log('CSRF Token:', token ? 'Present (length: ' + token.length + ')' : 'Missing');
                    
                    fetch('/check-user-exists', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            email: email
                        })
                    })
                    .then(response => {
                        console.log('Diagnostic response status:', response.status);
                        // Check if the response is JSON
                        const contentType = response.headers.get('content-type');
                        console.log('Content-Type:', contentType);
                        
                        if (contentType && contentType.includes('application/json')) {
                            return response.json().then(data => {
                                return { 
                                    isJson: true, 
                                    data: data 
                                };
                            });
                        } else {
                            // If not JSON, get as text
                            return response.text().then(text => {
                                return { 
                                    isJson: false, 
                                    data: text 
                                };
                            });
                        }
                    })
                    .then(result => {
                        if (result.isJson) {
                            console.log('Diagnostic data (JSON):', result.data);
                            
                            // Format the JSON nicely
                            const formattedJson = JSON.stringify(result.data, null, 2);
                            diagnosticResults.innerHTML = `<pre>${formattedJson}</pre>`;
                        } else {
                            console.log('Diagnostic data (Text):', result.data.substring(0, 100) + '...');
                            
                            // Display the HTML response or first 500 characters
                            diagnosticResults.innerHTML = `<div>
                                <strong>Unexpected HTML response instead of JSON:</strong><br>
                                <pre style="max-height: 200px; overflow: auto;">${escapeHtml(result.data.substring(0, 500))}...</pre>
                            </div>`;
                        }
                        
                        diagnosticResults.style.display = 'block';
                        diagnosticBtn.disabled = false;
                        diagnosticBtn.textContent = 'Diagnostic Check';
                    })
                    .catch(error => {
                        console.error('Diagnostic error:', error);
                        diagnosticResults.innerHTML = `<pre>Error: ${error.message}</pre>`;
                        diagnosticResults.style.display = 'block';
                        
                        diagnosticBtn.disabled = false;
                        diagnosticBtn.textContent = 'Diagnostic Check';
                    });
                });
            }
            
            // Helper function to escape HTML
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }
        });
    </script>
</body>
</html>
