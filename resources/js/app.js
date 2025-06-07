import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Memory Bank for storing user progress
const MemoryBank = {
  // Get data from localStorage
  get: function(key, defaultValue = null) {
    try {
      const item = localStorage.getItem(`ams_${key}`);
      return item ? JSON.parse(item) : defaultValue;
    } catch (e) {
      console.error('Error retrieving from memory bank:', e);
      return defaultValue;
    }
  },
  
  // Save data to localStorage
  set: function(key, value) {
    try {
      localStorage.setItem(`ams_${key}`, JSON.stringify(value));
      return true;
    } catch (e) {
      console.error('Error saving to memory bank:', e);
      return false;
    }
  },
  
  // Remove item from localStorage
  remove: function(key) {
    try {
      localStorage.removeItem(`ams_${key}`);
      return true;
    } catch (e) {
      console.error('Error removing from memory bank:', e);
      return false;
    }
  },
  
  // Clear all app data
  clear: function() {
    try {
      Object.keys(localStorage).forEach(key => {
        if (key.startsWith('ams_')) {
          localStorage.removeItem(key);
        }
      });
      return true;
    } catch (e) {
      console.error('Error clearing memory bank:', e);
      return false;
    }
  }
};

// Make memory bank available globally
window.MemoryBank = MemoryBank;

// Import memory bank modules
import MemoryBankAPI from './memory-bank';

console.log('App.js loaded - initializing React components');

// Disable React DevTools to prevent Chrome transport errors
if (typeof window !== 'undefined') {
  window.__REACT_DEVTOOLS_GLOBAL_HOOK__ = { isDisabled: true };
}

// Create a pure vanilla JS fallback renderer
function createVanillaFallback() {
  console.log('Using vanilla JS fallback for dashboards');
  
  // For each dashboard type, render basic HTML content directly
  document.querySelectorAll('[data-react-root]').forEach(container => {
    try {
      const componentName = container.dataset.component;
      const props = JSON.parse(container.dataset.props || '{}');
      
      // Skip if the container already has content
      if (container.children.length > 0) return;
      
      // Show the fallback content immediately
      const fallbackId = `${componentName.toLowerCase()}-fallback-content`;
      const fallbackContent = document.getElementById(fallbackId);
      if (fallbackContent) {
        console.log(`Showing fallback content for ${componentName}`);
        fallbackContent.style.display = 'block';
      }
      
      // Hide debug panel
      const debugId = `${componentName.toLowerCase()}-dashboard-debug`;
      const debugElement = document.getElementById(debugId);
      if (debugElement) debugElement.style.display = 'none';
      
      // Member Dashboard
      if (componentName === 'MemberDashboard') {
        // We already have a pure HTML fallback in the blade template
        console.log('Member dashboard has a direct HTML fallback');
      } 
      // Admin Dashboard
      else if (componentName === 'AdminDashboard') {
        const stats = props.stats || {};
        const html = `
          <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Admin Dashboard</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div class="p-4 bg-blue-100 rounded-lg">
                <p class="text-sm font-medium text-blue-700">Total Users</p>
                <p class="text-2xl font-bold">${stats.totalUsers || 0}</p>
              </div>
              <div class="p-4 bg-green-100 rounded-lg">
                <p class="text-sm font-medium text-green-700">Total Events</p>
                <p class="text-2xl font-bold">${stats.totalEvents || 0}</p>
              </div>
              <div class="p-4 bg-purple-100 rounded-lg">
                <p class="text-sm font-medium text-purple-700">Total Attendances</p>
                <p class="text-2xl font-bold">${stats.totalAttendances || 0}</p>
              </div>
              <div class="p-4 bg-yellow-100 rounded-lg">
                <p class="text-sm font-medium text-yellow-700">Notifications</p>
                <p class="text-2xl font-bold">${stats.totalNotifications || 0}</p>
              </div>
            </div>
            <p class="text-center text-gray-500">React components failed to load. Using basic fallback dashboard.</p>
          </div>
        `;
        container.innerHTML = html;
      } 
      // Officer Dashboard
      else if (componentName === 'OfficerDashboard') {
        const stats = props.stats || {};
        const html = `
          <div class="p-6 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-4">Officer Dashboard</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
              <div class="p-4 bg-blue-100 rounded-lg">
                <p class="text-sm font-medium text-blue-700">Total Events</p>
                <p class="text-2xl font-bold">${stats.totalEvents || 0}</p>
              </div>
              <div class="p-4 bg-green-100 rounded-lg">
                <p class="text-sm font-medium text-green-700">Total Attendances</p>
                <p class="text-2xl font-bold">${stats.totalAttendances || 0}</p>
              </div>
              <div class="p-4 bg-green-100 rounded-lg">
                <p class="text-sm font-medium text-green-700">Present Today</p>
                <p class="text-2xl font-bold">${stats.presentToday || 0}</p>
              </div>
              <div class="p-4 bg-yellow-100 rounded-lg">
                <p class="text-sm font-medium text-yellow-700">Pending Verifications</p>
                <p class="text-2xl font-bold">${stats.pendingVerifications || 0}</p>
              </div>
            </div>
            <p class="text-center text-gray-500">React components failed to load. Using basic fallback dashboard.</p>
          </div>
        `;
        container.innerHTML = html;
      }
    } catch (error) {
      console.error('Error creating vanilla fallback:', error);
    }
  });
}

// Initialize React components
document.addEventListener('DOMContentLoaded', function() {
  console.log('DOM loaded, initializing React components');
  
  // Initialize Memory Bank API
  MemoryBankAPI.init();
  
  try {
    // Load React components
    import('./react/index.jsx')
      .then(module => {
        console.log('React components loaded successfully');
        module.default(); // Call the default exported function
        
        // Record successful React initialization in memory bank
        MemoryBank.set('reactInitialized', {
          timestamp: new Date().toISOString(),
          success: true
        });
      })
      .catch(error => {
        console.error('Could not load React components:', error);
        console.log('Falling back to HTML-only content');
        
        // Record failed React initialization
        MemoryBank.set('reactInitialized', {
          timestamp: new Date().toISOString(),
          success: false,
          error: error.message
        });
        
        // Show fallback content
        document.querySelectorAll('[id$="-fallback-content"]').forEach(el => {
          el.style.display = 'block';
        });
        
        // Create vanilla fallbacks for components without explicit fallback content
        createVanillaFallback();
      });
  } catch (error) {
    console.error('Error initializing React:', error);
    
    // Record error in memory bank
    MemoryBank.set('reactInitError', {
      timestamp: new Date().toISOString(),
      error: error.message
    });
    
    // Show fallback content
    createVanillaFallback();
  }

  // Make sure Bootstrap is loaded
  if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap not loaded! Loading it again...');
    var script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
    document.head.appendChild(script);
  } else {
    console.log('Bootstrap loaded successfully');
  }
});

// Setup React error handling
window.onerror = function(message, source, lineno, colno, error) {
  if (message.includes('React') || message.includes('react') || source.includes('react')) {
    console.error('React error caught:', message, error);
    createVanillaFallback(); // Create fallback only for React errors
    return false; // Don't prevent default error handling
  }
  return false;
};
