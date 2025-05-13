// Wait for the Firebase SDK to be loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Firebase authentication state
    firebase.auth().onAuthStateChanged(function(user) {
        if (user) {
            console.log('User is signed in:', user.email);
        } else {
            console.log('No user is signed in');
            // You can implement anonymous authentication here if needed
            // firebase.auth().signInAnonymously();
        }
    });
    
    // Check if we're on an attendance page
    const attendanceTable = document.getElementById('attendance-table');
    if (!attendanceTable) return;
    
    // Get the event ID from the data attribute
    const eventId = attendanceTable.dataset.eventId;
    if (!eventId) return;
    
    // Listen for real-time updates
    firebaseAttendance.listenToEventAttendance(eventId, function(attendances) {
        updateAttendanceTable(attendances);
    });
});

// Function to update the attendance table with real-time data
function updateAttendanceTable(attendances) {
    const tbody = document.querySelector('#attendance-table tbody');
    if (!tbody) return;
    
    // Get existing attendance IDs in the table
    const existingIds = Array.from(tbody.querySelectorAll('tr[data-attendance-id]'))
        .map(row => row.dataset.attendanceId);
    
    // Process each attendance record
    attendances.forEach(function(attendance) {
        const row = document.querySelector(`tr[data-attendance-id="${attendance.id}"]`);
        
        if (row) {
            // Update existing row
            updateAttendanceRow(row, attendance);
        } else {
            // Add new row if we have user data
            if (attendance.user_name) {
                addNewAttendanceRow(attendance);
            } else {
                // Fetch user data if needed
                fetchUserData(attendance.user_id).then(function(userData) {
                    attendance.user_name = userData ? userData.name : 'Unknown User';
                    addNewAttendanceRow(attendance);
                });
            }
        }
    });
}

// Function to update an existing attendance row
function updateAttendanceRow(row, attendance) {
    const statusCell = row.querySelector('.attendance-status');
    if (statusCell) {
        statusCell.textContent = attendance.status;
        
        // Update status class
        statusCell.classList.remove('status-present', 'status-absent', 'status-excused');
        statusCell.classList.add(`status-${attendance.status}`);
    }
    
    // Update timestamp
    const timestampCell = row.querySelector('.attendance-timestamp');
    if (timestampCell && attendance.approved_at) {
        timestampCell.textContent = formatDateTime(attendance.approved_at);
    }
    
    // Update remarks if present
    const remarksCell = row.querySelector('.attendance-remarks');
    if (remarksCell && attendance.remarks) {
        remarksCell.textContent = attendance.remarks;
    }
}

// Function to add a new attendance row
function addNewAttendanceRow(attendance) {
    const tbody = document.querySelector('#attendance-table tbody');
    if (!tbody) return;
    
    const row = document.createElement('tr');
    row.setAttribute('data-attendance-id', attendance.id);
    
    // Create the row content based on your table structure
    row.innerHTML = `
        <td>${attendance.user_name || 'Unknown User'}</td>
        <td class="attendance-status status-${attendance.status}">${attendance.status}</td>
        <td class="attendance-timestamp">${formatDateTime(attendance.approved_at)}</td>
        <td class="attendance-remarks">${attendance.remarks || ''}</td>
        <td>
            <a href="/officer/attendances/${attendance.id}/edit" class="btn btn-sm btn-primary">Edit</a>
        </td>
    `;
    
    tbody.appendChild(row);
}

// Function to fetch user data
function fetchUserData(userId) {
    return new Promise((resolve, reject) => {
        rtUsersRef.child(userId).once('value')
            .then(snapshot => {
                resolve(snapshot.val());
            })
            .catch(error => {
                console.error('Error fetching user data:', error);
                resolve(null);
            });
    });
}

// Helper function to format date and time
function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return '';
    
    const date = new Date(dateTimeStr);
    return date.toLocaleString();
}

// Function to handle QR code scanning and attendance recording
function handleQrScan(qrCode, eventId) {
    // Send AJAX request to your Laravel backend
    fetch('/officer/scan/process', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_code: qrCode,
            event_id: eventId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Record in Firebase as well for real-time updates
            firebaseAttendance.recordAttendance(
                data.user_id,
                eventId,
                'present',
                data.approved_by
            );
            
            // Show success message
            showNotification('success', data.message);
        } else {
            // Show error message
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error processing QR code:', error);
        showNotification('error', 'Failed to process QR code. Please try again.');
    });
}

// Function to show notification
function showNotification(type, message) {
    // Implement based on your UI framework (Bootstrap, Tailwind, etc.)
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.classList.remove('show');
        setTimeout(() => alertDiv.remove(), 150);
    }, 5000);
}