import React from 'react';
import { createRoot } from 'react-dom/client';
import ErrorBoundary from './components/ErrorBoundary';

// Import dashboard components
import MemberDashboard from './components/MemberDashboard';
import AdminDashboard from './components/AdminDashboard';
import OfficerDashboard from './components/OfficerDashboard';

/**
 * Initialize React components with memory bank integration
 * This function finds all React roots in the DOM and renders the appropriate component
 */
export default function initReact() {
  // Check memory bank is available 
  if (!window.MemoryBank) {
    console.error('Memory Bank not initialized');
    return;
  }
  
  // Log memory bank status
  console.log('Memory Bank status:', {
    initialized: true,
    entries: Object.keys(localStorage)
      .filter(key => key.startsWith('ams_'))
      .length
  });
  
  // Find all React root elements
  const reactRoots = document.querySelectorAll('[data-react-root]');
  
  if (reactRoots.length === 0) {
    console.log('No React root elements found');
    return;
  }
  
  console.log(`Found ${reactRoots.length} React root elements`);
  
  // Map of component names to component classes
  const componentMap = {
    'MemberDashboard': MemberDashboard,
    'AdminDashboard': AdminDashboard,
    'OfficerDashboard': OfficerDashboard
  };
  
  // Render each component
  reactRoots.forEach(rootElement => {
    try {
      const componentName = rootElement.dataset.component;
      const componentProps = JSON.parse(rootElement.dataset.props || '{}');
      const Component = componentMap[componentName];
      
      if (!Component) {
        console.error(`Component ${componentName} not found in component map`);
        return;
      }
      
      // Create React root
      const root = createRoot(rootElement);
      
      // Record component load in memory bank
      window.MemoryBank.set('lastComponentLoad', {
        component: componentName,
        timestamp: new Date().toISOString()
      });
      
      // Render component with error boundary
      root.render(
        <ErrorBoundary fallback={
          <div className="p-4 text-center">
            <p className="text-red-600 font-bold">Something went wrong with {componentName}</p>
            <p className="text-gray-600">Using fallback content instead</p>
          </div>
        }>
          <Component {...componentProps} />
        </ErrorBoundary>
      );
      
      console.log(`Rendered ${componentName} component`);
    } catch (error) {
      console.error('Error rendering component:', error);
      
      // Record error in memory bank
      window.MemoryBank.set('lastRenderError', {
        error: error.message,
        timestamp: new Date().toISOString()
      });
      
      // Show fallback content
      const fallbackId = `${rootElement.dataset.component.toLowerCase()}-fallback-content`;
      const fallbackContent = document.getElementById(fallbackId);
      if (fallbackContent) {
        fallbackContent.style.display = 'block';
      }
    }
  });
} 