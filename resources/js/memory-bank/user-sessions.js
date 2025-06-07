/**
 * User Sessions Manager
 * Tracks and manages user session data in the memory bank
 */

// Imports the main memory bank (if needed)
// import { MemoryBank } from '../app';

const UserSessions = {
  /**
   * Start a new user session
   * @param {object} userData - User data including id, name, role
   * @returns {string} Session ID
   */
  startSession(userData) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return null;
    }
    
    // Create new session
    const sessionId = 'session_' + Date.now();
    const sessionData = {
      id: sessionId,
      userId: userData.id,
      userName: userData.name,
      userRole: userData.role,
      startTime: new Date().toISOString(),
      lastActivity: new Date().toISOString(),
      pageViews: [],
      actions: []
    };
    
    // Save to memory bank
    window.MemoryBank.set(sessionId, sessionData);
    
    // Update sessions index
    const activeSessions = window.MemoryBank.get('activeSessions', []);
    activeSessions.push(sessionId);
    window.MemoryBank.set('activeSessions', activeSessions);
    
    console.log(`Started new user session: ${sessionId}`);
    return sessionId;
  },
  
  /**
   * Record user activity in the current session
   * @param {string} sessionId - The session ID
   * @param {string} activityType - Type of activity
   * @param {object} activityData - Activity details
   * @returns {boolean} Success status
   */
  recordActivity(sessionId, activityType, activityData = {}) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Get session data
    const sessionData = window.MemoryBank.get(sessionId);
    if (!sessionData) {
      console.error(`Session ${sessionId} not found`);
      return false;
    }
    
    // Record activity
    const activity = {
      type: activityType,
      timestamp: new Date().toISOString(),
      data: activityData
    };
    
    sessionData.actions.push(activity);
    sessionData.lastActivity = new Date().toISOString();
    
    // Save updated session
    window.MemoryBank.set(sessionId, sessionData);
    
    return true;
  },
  
  /**
   * Record a page view
   * @param {string} sessionId - The session ID
   * @param {string} page - Page URL or identifier
   * @returns {boolean} Success status
   */
  recordPageView(sessionId, page) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Get session data
    const sessionData = window.MemoryBank.get(sessionId);
    if (!sessionData) {
      console.error(`Session ${sessionId} not found`);
      return false;
    }
    
    // Record page view
    const pageView = {
      url: page,
      timestamp: new Date().toISOString()
    };
    
    sessionData.pageViews.push(pageView);
    sessionData.lastActivity = new Date().toISOString();
    
    // Save updated session
    window.MemoryBank.set(sessionId, sessionData);
    
    return true;
  },
  
  /**
   * End a user session
   * @param {string} sessionId - The session ID
   * @returns {boolean} Success status
   */
  endSession(sessionId) {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Get session data
    const sessionData = window.MemoryBank.get(sessionId);
    if (!sessionData) {
      console.error(`Session ${sessionId} not found`);
      return false;
    }
    
    // Update session end time
    sessionData.endTime = new Date().toISOString();
    
    // Calculate session duration
    const startTime = new Date(sessionData.startTime);
    const endTime = new Date(sessionData.endTime);
    const durationMs = endTime - startTime;
    sessionData.durationSeconds = Math.floor(durationMs / 1000);
    
    // Move from active to completed sessions
    const activeSessions = window.MemoryBank.get('activeSessions', []);
    const completedSessions = window.MemoryBank.get('completedSessions', []);
    
    const updatedActiveSessions = activeSessions.filter(id => id !== sessionId);
    completedSessions.push(sessionId);
    
    // Save all updates
    window.MemoryBank.set(sessionId, sessionData);
    window.MemoryBank.set('activeSessions', updatedActiveSessions);
    window.MemoryBank.set('completedSessions', completedSessions);
    
    console.log(`Ended user session: ${sessionId}`);
    return true;
  },
  
  /**
   * Get session statistics
   * @returns {object} Session statistics
   */
  getSessionStats() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return null;
    }
    
    const activeSessions = window.MemoryBank.get('activeSessions', []);
    const completedSessions = window.MemoryBank.get('completedSessions', []);
    
    return {
      activeSessions: activeSessions.length,
      completedSessions: completedSessions.length,
      totalSessions: activeSessions.length + completedSessions.length
    };
  },
  
  /**
   * Clear all session data
   * @returns {boolean} Success status
   */
  clearAllSessions() {
    if (!window.MemoryBank) {
      console.error('Memory Bank not available');
      return false;
    }
    
    // Get all session IDs
    const activeSessions = window.MemoryBank.get('activeSessions', []);
    const completedSessions = window.MemoryBank.get('completedSessions', []);
    const allSessions = [...activeSessions, ...completedSessions];
    
    // Remove each session
    allSessions.forEach(sessionId => {
      window.MemoryBank.remove(sessionId);
    });
    
    // Reset session lists
    window.MemoryBank.set('activeSessions', []);
    window.MemoryBank.set('completedSessions', []);
    
    console.log('Cleared all session data');
    return true;
  }
};

// Export the UserSessions manager
export default UserSessions;

// Make available globally for console debugging
if (typeof window !== 'undefined') {
  window.UserSessions = UserSessions;
} 