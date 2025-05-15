// Firebase configuration
// Replace these values with your actual Firebase project details from the Firebase Console
const firebaseConfig = {
  apiKey: "AIzaSyD1Bv1juq5vO6o6JFokqFyJRh7ID3djgUE",
    authDomain: "kofa-ams.firebaseapp.com",
    projectId: "kofa-ams",
    storageBucket: "kofa-ams.firebasestorage.app",
    messagingSenderId: "658395081690",
    appId: "1:658395081690:web:f15ef7128d58f3fc05aee3",
    measurementId: "G-Q3B93PY9KP"
};

// Initialize Firebase
const app = firebase.initializeApp(firebaseConfig);
const db = firebase.firestore();
const auth = firebase.auth();
const rtdb = firebase.database();

// Collection references
const attendancesRef = db.collection('attendances');
const eventsRef = db.collection('events');
const usersRef = db.collection('users');

// Realtime Database references
const rtAttendancesRef = rtdb.ref('attendances');
const rtEventsRef = rtdb.ref('events');
const rtUsersRef = rtdb.ref('users');

// Export the Firebase services for use in other scripts
window.db = db;
window.auth = auth;
window.rtdb = rtdb;
window.attendancesRef = attendancesRef;
window.eventsRef = eventsRef;
window.usersRef = usersRef;
window.rtAttendancesRef = rtAttendancesRef;
window.rtEventsRef = rtEventsRef;
window.rtUsersRef = rtUsersRef;

// Helper functions for attendance management
window.firebaseAttendance = {
  // Record attendance in Firebase
  recordAttendance: function(userId, eventId, status = 'present', approvedBy = null) {
    const timestamp = new Date().toISOString();
    const attendanceData = {
      user_id: userId,
      event_id: eventId,
      status: status,
      approved_by: approvedBy,
      approved_at: timestamp,
      created_at: timestamp,
      updated_at: timestamp
    };
    
    // Generate a unique ID
    const newAttendanceRef = rtAttendancesRef.push();
    const newId = newAttendanceRef.key;
    attendanceData.id = newId;
    
    // Save to Firestore
    attendancesRef.doc(newId).set(attendanceData);
    
    // Save to Realtime Database
    newAttendanceRef.set(attendanceData);
    
    return attendanceData;
  },
  
  // Update attendance status
  updateAttendance: function(attendanceId, status, remarks = null, approvedBy = null) {
    const timestamp = new Date().toISOString();
    const updates = {
      status: status,
      updated_at: timestamp
    };
    
    if (remarks !== null) {
      updates.remarks = remarks;
    }
    
    if (approvedBy !== null) {
      updates.approved_by = approvedBy;
      updates.approved_at = timestamp;
    }
    
    // Update in Firestore
    attendancesRef.doc(attendanceId).update(updates);
    
    // Update in Realtime Database
    rtAttendancesRef.child(attendanceId).update(updates);
    
    return updates;
  },
  
  // Listen for real-time attendance updates for an event
  listenToEventAttendance: function(eventId, callback) {
    return rtAttendancesRef.orderByChild('event_id').equalTo(eventId)
      .on('value', function(snapshot) {
        const attendances = [];
        snapshot.forEach(function(childSnapshot) {
          attendances.push(childSnapshot.val());
        });
        callback(attendances);
      });
  },
  
  // Stop listening to updates
  stopListening: function(eventId) {
    rtAttendancesRef.orderByChild('event_id').equalTo(eventId).off();
  }
};

// Helper functions for event management
window.firebaseEvents = {
  // Create or update an event
  saveEvent: function(eventData) {
    const timestamp = new Date().toISOString();
    eventData.updated_at = timestamp;
    
    if (!eventData.id) {
      // New event
      eventData.created_at = timestamp;
      const newEventRef = rtEventsRef.push();
      const newId = newEventRef.key;
      eventData.id = newId;
      
      // Save to Firestore
      eventsRef.doc(newId).set(eventData);
      
      // Save to Realtime Database
      newEventRef.set(eventData);
    } else {
      // Update existing event
      const eventId = eventData.id;
      
      // Update in Firestore
      eventsRef.doc(eventId).update(eventData);
      
      // Update in Realtime Database
      rtEventsRef.child(eventId).update(eventData);
    }
    
    return eventData;
  },
  
  // Get active events
  getActiveEvents: function(callback) {
    return rtEventsRef.orderByChild('is_active').equalTo(true)
      .on('value', function(snapshot) {
        const events = [];
        snapshot.forEach(function(childSnapshot) {
          events.push(childSnapshot.val());
        });
        callback(events);
      });
  }
};