/**
 * Memory Bank Cleaner
 * Utilities to clean and maintain the memory bank data
 */

const MemoryCleaner = {
  /**
   * Clear all memory bank data
   * @returns {boolean} Success status
   */
  clearAll() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    try {
      window.MemoryBank.clear();
      console.log('Memory bank completely cleared');
      return true;
    } catch (error) {
      console.error('Error clearing memory bank:', error);
      return false;
    }
  },
  
  /**
   * Clear only specific categories of data
   * @param {Array} categories - Categories to clear (e.g., ['attendance', 'sessions'])
   * @returns {object} Results with counts of deleted items
   */
  clearCategories(categories = []) {
    if (!window.MemoryBank || !Array.isArray(categories)) {
      console.error('Memory Bank not available or invalid categories');
      return { success: false };
    }
    
    const results = {
      success: true,
      deletedCount: 0,
      categories: {}
    };
    
    try {
      const allKeys = Object.keys(localStorage)
        .filter(key => key.startsWith('ams_'));
      
      // Process each category
      categories.forEach(category => {
        let categoryCount = 0;
        
        switch (category) {
          case 'attendance':
            // Clear attendance records
            window.MemoryBank.set('attendanceRecords', []);
            
            // Clear attendance stats
            const attendanceKeys = allKeys.filter(key => 
              key.includes('_attendance_') || 
              key.includes('attendanceRecords')
            );
            
            attendanceKeys.forEach(key => {
              const actualKey = key.substring(4); // Remove 'ams_' prefix
              window.MemoryBank.remove(actualKey);
              categoryCount++;
            });
            break;
            
          case 'sessions':
            // Clear session data
            const sessionKeys = allKeys.filter(key => 
              key.includes('session_') || 
              key === 'ams_activeSessions' || 
              key === 'ams_completedSessions'
            );
            
            sessionKeys.forEach(key => {
              const actualKey = key.substring(4); // Remove 'ams_' prefix
              window.MemoryBank.remove(actualKey);
              categoryCount++;
            });
            
            // Reset session lists
            window.MemoryBank.set('activeSessions', []);
            window.MemoryBank.set('completedSessions', []);
            break;
            
          case 'progress':
            // Clear user progress data
            const progressKeys = allKeys.filter(key => 
              key.includes('userProgress') || 
              key.includes('streak') ||
              key.includes('viewed')
            );
            
            progressKeys.forEach(key => {
              const actualKey = key.substring(4); // Remove 'ams_' prefix
              window.MemoryBank.remove(actualKey);
              categoryCount++;
            });
            break;
            
          case 'scans':
            // Clear scan history
            const scanKeys = allKeys.filter(key => 
              key.includes('scan') || 
              key.includes('officerScanHistory')
            );
            
            scanKeys.forEach(key => {
              const actualKey = key.substring(4); // Remove 'ams_' prefix
              window.MemoryBank.remove(actualKey);
              categoryCount++;
            });
            break;
            
          default:
            console.warn(`Unknown category: ${category}`);
        }
        
        results.categories[category] = categoryCount;
        results.deletedCount += categoryCount;
      });
      
      console.log(`Cleared ${results.deletedCount} items from memory bank in categories:`, categories);
      
    } catch (error) {
      console.error('Error clearing memory bank categories:', error);
      results.success = false;
      results.error = error.message;
    }
    
    return results;
  },
  
  /**
   * Clean expired data from memory bank
   * @param {number} olderThanDays - Delete data older than this many days
   * @returns {object} Results with counts of deleted items
   */
  cleanExpiredData(olderThanDays = 30) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return { success: false };
    }
    
    const results = {
      success: true,
      deletedCount: 0,
      categories: {}
    };
    
    try {
      const now = new Date();
      const cutoffDate = new Date(now);
      cutoffDate.setDate(cutoffDate.getDate() - olderThanDays);
      
      // Clean attendance records
      let attendanceCount = 0;
      const attendanceRecords = window.MemoryBank.get('attendanceRecords', []);
      const newAttendanceRecords = attendanceRecords.filter(record => {
        const recordDate = new Date(record.scanTime);
        if (recordDate < cutoffDate) {
          attendanceCount++;
          return false;
        }
        return true;
      });
      
      if (attendanceRecords.length !== newAttendanceRecords.length) {
        window.MemoryBank.set('attendanceRecords', newAttendanceRecords);
      }
      
      // Clean session data
      let sessionCount = 0;
      const completedSessions = window.MemoryBank.get('completedSessions', []);
      const newCompletedSessions = [];
      
      completedSessions.forEach(sessionId => {
        const session = window.MemoryBank.get(sessionId);
        if (session) {
          const sessionDate = new Date(session.endTime || session.lastActivity);
          if (sessionDate < cutoffDate) {
            window.MemoryBank.remove(sessionId);
            sessionCount++;
          } else {
            newCompletedSessions.push(sessionId);
          }
        }
      });
      
      if (completedSessions.length !== newCompletedSessions.length) {
        window.MemoryBank.set('completedSessions', newCompletedSessions);
      }
      
      results.categories.attendance = attendanceCount;
      results.categories.sessions = sessionCount;
      results.deletedCount = attendanceCount + sessionCount;
      
      console.log(`Cleaned ${results.deletedCount} expired items older than ${olderThanDays} days`);
      
    } catch (error) {
      console.error('Error cleaning expired data:', error);
      results.success = false;
      results.error = error.message;
    }
    
    return results;
  },
  
  /**
   * Get memory usage statistics
   * @returns {object} Memory usage statistics
   */
  getMemoryUsage() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return { available: false };
    }
    
    try {
      const allKeys = Object.keys(localStorage)
        .filter(key => key.startsWith('ams_'));
      
      let totalSize = 0;
      const categorySizes = {
        attendance: 0,
        sessions: 0,
        progress: 0,
        scans: 0,
        other: 0
      };
      
      allKeys.forEach(key => {
        const value = localStorage.getItem(key);
        const size = (value ? value.length : 0) + key.length;
        totalSize += size;
        
        // Categorize
        if (key.includes('_attendance_') || key.includes('attendanceRecords')) {
          categorySizes.attendance += size;
        } else if (key.includes('session_') || key === 'ams_activeSessions' || key === 'ams_completedSessions') {
          categorySizes.sessions += size;
        } else if (key.includes('userProgress') || key.includes('streak')) {
          categorySizes.progress += size;
        } else if (key.includes('scan') || key.includes('officerScanHistory')) {
          categorySizes.scans += size;
        } else {
          categorySizes.other += size;
        }
      });
      
      // Convert to KB
      const toKB = (bytes) => Math.round(bytes / 1024 * 10) / 10;
      
      return {
        available: true,
        totalEntries: allKeys.length,
        totalSizeKB: toKB(totalSize),
        categories: {
          attendance: {
            count: allKeys.filter(k => k.includes('_attendance_') || k.includes('attendanceRecords')).length,
            sizeKB: toKB(categorySizes.attendance)
          },
          sessions: {
            count: allKeys.filter(k => k.includes('session_') || k === 'ams_activeSessions' || k === 'ams_completedSessions').length,
            sizeKB: toKB(categorySizes.sessions)
          },
          progress: {
            count: allKeys.filter(k => k.includes('userProgress') || k.includes('streak')).length,
            sizeKB: toKB(categorySizes.progress)
          },
          scans: {
            count: allKeys.filter(k => k.includes('scan') || k.includes('officerScanHistory')).length,
            sizeKB: toKB(categorySizes.scans)
          },
          other: {
            count: allKeys.filter(k => 
              !k.includes('_attendance_') && 
              !k.includes('attendanceRecords') &&
              !k.includes('session_') && 
              k !== 'ams_activeSessions' && 
              k !== 'ams_completedSessions' &&
              !k.includes('userProgress') && 
              !k.includes('streak') &&
              !k.includes('scan') && 
              !k.includes('officerScanHistory')
            ).length,
            sizeKB: toKB(categorySizes.other)
          }
        },
        remaining: {
          // Approximation based on typical localStorage limits
          remainingSizeKB: toKB(5 * 1024 * 1024 - totalSize),
          percentUsed: Math.round((totalSize / (5 * 1024 * 1024)) * 1000) / 10
        }
      };
      
    } catch (error) {
      console.error('Error getting memory usage:', error);
      return { 
        available: false,
        error: error.message
      };
    }
  }
};

// Export the MemoryCleaner
export default MemoryCleaner;

// Make available globally for console debugging
if (typeof window !== 'undefined') {
  window.MemoryCleaner = MemoryCleaner;
} 