/**
 * Attendance Tracker
 * Tracks and analyzes attendance data in the memory bank
 */

const AttendanceTracker = {
  /**
   * Record a new attendance
   * @param {object} attendanceData - Attendance data
   * @returns {boolean} Success status
   */
  recordAttendance(attendanceData) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Get existing attendance records
    const attendanceRecords = window.MemoryBank.get('attendanceRecords', []);
    
    // Create new attendance record
    const newRecord = {
      id: 'attendance_' + Date.now(),
      userId: attendanceData.userId,
      userName: attendanceData.userName,
      eventId: attendanceData.eventId,
      eventName: attendanceData.eventName,
      status: attendanceData.status, // present, absent, excused
      scanTime: new Date().toISOString(),
      notes: attendanceData.notes || '',
      scannedBy: attendanceData.scannedBy || null,
      verifiedBy: attendanceData.verifiedBy || null
    };
    
    // Add to records
    attendanceRecords.push(newRecord);
    
    // Save to memory bank
    window.MemoryBank.set('attendanceRecords', attendanceRecords);
    
    // Update user attendance stats
    this.updateUserStats(attendanceData.userId, attendanceData.status);
    
    // Update event attendance stats
    this.updateEventStats(attendanceData.eventId, attendanceData.status);
    
    console.log(`Recorded attendance: ${newRecord.id}`);
    return true;
  },
  
  /**
   * Update user attendance statistics
   * @param {string|number} userId - User ID
   * @param {string} status - Attendance status
   * @private
   */
  updateUserStats(userId, status) {
    if (!window.MemoryBank) return;
    
    const userStatsKey = `user_attendance_${userId}`;
    const userStats = window.MemoryBank.get(userStatsKey, {
      userId: userId,
      totalEvents: 0,
      present: 0,
      absent: 0,
      excused: 0,
      lastUpdated: null
    });
    
    // Update counts
    userStats.totalEvents += 1;
    if (status === 'present') userStats.present += 1;
    else if (status === 'absent') userStats.absent += 1;
    else if (status === 'excused') userStats.excused += 1;
    
    userStats.lastUpdated = new Date().toISOString();
    
    // Save updated stats
    window.MemoryBank.set(userStatsKey, userStats);
  },
  
  /**
   * Update event attendance statistics
   * @param {string|number} eventId - Event ID
   * @param {string} status - Attendance status
   * @private
   */
  updateEventStats(eventId, status) {
    if (!window.MemoryBank) return;
    
    const eventStatsKey = `event_attendance_${eventId}`;
    const eventStats = window.MemoryBank.get(eventStatsKey, {
      eventId: eventId,
      totalAttendees: 0,
      present: 0,
      absent: 0,
      excused: 0,
      lastUpdated: null
    });
    
    // Update counts
    eventStats.totalAttendees += 1;
    if (status === 'present') eventStats.present += 1;
    else if (status === 'absent') eventStats.absent += 1;
    else if (status === 'excused') eventStats.excused += 1;
    
    eventStats.lastUpdated = new Date().toISOString();
    
    // Save updated stats
    window.MemoryBank.set(eventStatsKey, eventStats);
  },
  
  /**
   * Get user attendance statistics
   * @param {string|number} userId - User ID
   * @returns {object} User attendance stats
   */
  getUserStats(userId) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return null;
    }
    
    const userStatsKey = `user_attendance_${userId}`;
    return window.MemoryBank.get(userStatsKey, null);
  },
  
  /**
   * Get event attendance statistics
   * @param {string|number} eventId - Event ID
   * @returns {object} Event attendance stats
   */
  getEventStats(eventId) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return null;
    }
    
    const eventStatsKey = `event_attendance_${eventId}`;
    return window.MemoryBank.get(eventStatsKey, null);
  },
  
  /**
   * Get attendance records for a user
   * @param {string|number} userId - User ID
   * @returns {array} User attendance records
   */
  getUserAttendance(userId) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return [];
    }
    
    const attendanceRecords = window.MemoryBank.get('attendanceRecords', []);
    return attendanceRecords.filter(record => record.userId === userId);
  },
  
  /**
   * Get attendance records for an event
   * @param {string|number} eventId - Event ID
   * @returns {array} Event attendance records
   */
  getEventAttendance(eventId) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return [];
    }
    
    const attendanceRecords = window.MemoryBank.get('attendanceRecords', []);
    return attendanceRecords.filter(record => record.eventId === eventId);
  },
  
  /**
   * Get overall attendance statistics
   * @returns {object} Overall attendance stats
   */
  getOverallStats() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return null;
    }
    
    const attendanceRecords = window.MemoryBank.get('attendanceRecords', []);
    
    // Calculate totals
    const totalRecords = attendanceRecords.length;
    const presentCount = attendanceRecords.filter(r => r.status === 'present').length;
    const absentCount = attendanceRecords.filter(r => r.status === 'absent').length;
    const excusedCount = attendanceRecords.filter(r => r.status === 'excused').length;
    
    // Calculate rates
    const presentRate = totalRecords > 0 ? (presentCount / totalRecords) * 100 : 0;
    const absentRate = totalRecords > 0 ? (absentCount / totalRecords) * 100 : 0;
    const excusedRate = totalRecords > 0 ? (excusedCount / totalRecords) * 100 : 0;
    
    return {
      totalRecords,
      presentCount,
      absentCount,
      excusedCount,
      presentRate: Math.round(presentRate * 10) / 10, // Round to 1 decimal place
      absentRate: Math.round(absentRate * 10) / 10,
      excusedRate: Math.round(excusedRate * 10) / 10
    };
  },
  
  /**
   * Clear all attendance data
   * @returns {boolean} Success status
   */
  clearAllAttendance() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Clear attendance records
    window.MemoryBank.set('attendanceRecords', []);
    
    // Clear user stats
    const allKeys = Object.keys(localStorage);
    const userStatsKeys = allKeys.filter(key => key.startsWith('ams_user_attendance_'));
    const eventStatsKeys = allKeys.filter(key => key.startsWith('ams_event_attendance_'));
    
    userStatsKeys.forEach(key => {
      const actualKey = key.substring(4); // Remove 'ams_' prefix
      window.MemoryBank.remove(actualKey);
    });
    
    eventStatsKeys.forEach(key => {
      const actualKey = key.substring(4); // Remove 'ams_' prefix
      window.MemoryBank.remove(actualKey);
    });
    
    console.log('Cleared all attendance data');
    return true;
  }
};

// Export the AttendanceTracker
export default AttendanceTracker;

// Make available globally for console debugging
if (typeof window !== 'undefined') {
  window.AttendanceTracker = AttendanceTracker;
} 