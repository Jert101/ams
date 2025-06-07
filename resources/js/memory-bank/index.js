/**
 * Memory Bank - Main Index
 * Initializes and exports all memory bank modules
 */

import ProjectProgress from './project-progress';
import UserSessions from './user-sessions';
import AttendanceTracker from './attendance-tracker';
import MemoryCleaner from './memory-cleaner';

/**
 * Initialize the memory bank system
 */
function initMemoryBank() {
  try {
    if (!window.MemoryBank) {
      console.error('Memory Bank core not initialized');
      return false;
    }
    
    console.log('Initializing Memory Bank modules...');
    
    // Register memory bank modules
    window.MemoryBank.modules = {
      ProjectProgress,
      UserSessions,
      AttendanceTracker,
      MemoryCleaner
    };
    
    // Record memory bank initialization
    window.MemoryBank.set('memoryBankInitialized', {
      timestamp: new Date().toISOString(),
      version: '1.0.0'
    });
    
    console.log('Memory Bank modules initialized successfully');
    return true;
  } catch (error) {
    console.error('Error initializing Memory Bank:', error);
    return false;
  }
}

// Create main Memory Bank API
const MemoryBankAPI = {
  init: initMemoryBank,
  ProjectProgress,
  UserSessions,
  AttendanceTracker,
  MemoryCleaner,
  
  /**
   * Get memory bank system status
   * @returns {object} System status
   */
  getStatus() {
    try {
      if (!window.MemoryBank) {
        return { initialized: false };
      }
      
      return {
        initialized: true,
        initTime: window.MemoryBank.get('memoryBankInitialized')?.timestamp,
        entryCount: Object.keys(localStorage)
          .filter(key => key.startsWith('ams_'))
          .length,
        availableModules: Object.keys(window.MemoryBank.modules || {})
      };
    } catch (error) {
      console.error('Error getting Memory Bank status:', error);
      return { initialized: false, error: error.message };
    }
  },
  
  /**
   * Clear all memory bank data
   * @returns {boolean} Success status
   */
  resetAll() {
    try {
      if (!window.MemoryBank) {
        console.error('Memory Bank not initialized');
        return false;
      }
      
      if (confirm('Are you sure you want to reset all memory bank data? This cannot be undone.')) {
        // Clear all data
        window.MemoryBank.clear();
        
        // Re-initialize
        initMemoryBank();
        
        alert('Memory Bank has been reset successfully.');
        return true;
      }
      
      return false;
    } catch (error) {
      console.error('Error resetting Memory Bank:', error);
      return false;
    }
  },
  
  /**
   * Perform automatic cleanup of old data
   * @param {number} olderThanDays - Days threshold for cleaning old data (default: 30)
   * @returns {object} Cleanup results
   */
  autoCleanup(olderThanDays = 30) {
    try {
      return MemoryCleaner.cleanExpiredData(olderThanDays);
    } catch (error) {
      console.error('Error during Memory Bank cleanup:', error);
      return { success: false, error: error.message };
    }
  }
};

// Initialize when loaded
if (typeof window !== 'undefined') {
  // Initialize on DOMContentLoaded to ensure MemoryBank core is loaded
  document.addEventListener('DOMContentLoaded', () => {
    try {
      // Give the core Memory Bank a moment to initialize
      setTimeout(() => {
        console.log('Initializing Memory Bank API...');
        const result = initMemoryBank();
        console.log('Memory Bank initialization result:', result);
        
        // Auto-clean old data (older than 90 days)
        setTimeout(() => {
          try {
            MemoryBankAPI.autoCleanup(90);
          } catch (error) {
            console.error('Error during auto cleanup:', error);
          }
        }, 5000);
      }, 100);
    } catch (error) {
      console.error('Error in Memory Bank initialization:', error);
    }
  });
  
  // Make available globally
  window.MemoryBankAPI = MemoryBankAPI;
}

// Export for module use
export default MemoryBankAPI; 