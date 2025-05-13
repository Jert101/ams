// Firebase data synchronization utilities
document.addEventListener('DOMContentLoaded', function() {
    // Check if we need to sync data
    const syncContainer = document.getElementById('firebase-sync-container');
    if (!syncContainer) return;
    
    // Initialize sync status
    const syncStatus = {
        lastSync: localStorage.getItem('lastFirebaseSync') || null,
        inProgress: false
    };
    
    // Setup sync button if it exists
    const syncButton = document.getElementById('firebase-sync-button');
    if (syncButton) {
        syncButton.addEventListener('click', function() {
            if (!syncStatus.inProgress) {
                syncData();
            }
        });
    }
    
    // Auto-sync on page load if it's been more than 30 minutes
    if (!syncStatus.lastSync || isOlderThan(syncStatus.lastSync, 30)) {
        syncData();
    }
    
    // Function to sync data between Laravel and Firebase
    function syncData() {
        syncStatus.inProgress = true;
        updateSyncUI('syncing');
        
        // Fetch data from your Laravel backend
        fetch('/api/firebase/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update local storage with sync time
                const now = new Date().toISOString();
                localStorage.setItem('lastFirebaseSync', now);
                syncStatus.lastSync = now;
                
                // Update UI
                updateSyncUI('success', data.message);
                
                // If we have data to sync to Firebase, do it
                if (data.events && data.events.length > 0) {
                    syncEventsToFirebase(data.events);
                }
                
                if (data.attendances && data.attendances.length > 0) {
                    syncAttendancesToFirebase(data.attendances);
                }
                
                if (data.users && data.users.length > 0) {
                    syncUsersToFirebase(data.users);
                }
            } else {
                updateSyncUI('error', data.message || 'Sync failed');
            }
        })
        .catch(error => {
            console.error('Sync error:', error);
            updateSyncUI('error', 'Failed to sync with server');
        })
        .finally(() => {
            syncStatus.inProgress = false;
        });
    }
    
    // Function to update the sync UI
    function updateSyncUI(status, message = '') {
        const statusElement = document.getElementById('firebase-sync-status');
        if (!statusElement) return;
        
        statusElement.className = 'sync-status';
        statusElement.classList.add(`sync-${status}`);
        
        switch (status) {
            case 'syncing':
                statusElement.innerHTML = '<i class="fas fa-sync fa-spin"></i> Syncing data...';
                break;
            case 'success':
                statusElement.innerHTML = '<i class="fas fa-check"></i> ' + (message || 'Sync completed');
                break;
            case 'error':
                statusElement.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + (message || 'Sync failed');
                break;
            default:
                statusElement.innerHTML = message;
        }
    }
    
    // Function to sync events to Firebase
    function syncEventsToFirebase(events) {
        events.forEach(event => {
            firebaseEvents.saveEvent(event);
        });
    }
    
    // Function to sync attendances to Firebase
    function syncAttendancesToFirebase(attendances) {
        attendances.forEach(attendance => {
            // Save to Firestore
            attendancesRef.doc(attendance.id.toString()).set(attendance);
            
            // Save to Realtime Database
            rtAttendancesRef.child(attendance.id.toString()).set(attendance);
        });
    }
    
    // Function to sync users to Firebase
    function syncUsersToFirebase(users) {
        users.forEach(user => {
            // Save to Firestore
            usersRef.doc(user.id.toString()).set(user);
            
            // Save to Realtime Database
            rtUsersRef.child(user.id.toString()).set(user);
        });
    }
    
    // Helper function to check if a timestamp is older than X minutes
    function isOlderThan(timestamp, minutes) {
        if (!timestamp) return true;
        
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMinutes = diffMs / (1000 * 60);
        
        return diffMinutes > minutes;
    }
});