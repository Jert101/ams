import React from 'react';

/**
 * ErrorBoundary component to catch JavaScript errors anywhere in the component tree
 * and display a fallback UI instead of crashing the entire app
 */
class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null, errorInfo: null };
  }

  static getDerivedStateFromError(error) {
    // Update state so the next render will show the fallback UI
    return { hasError: true, error };
  }

  componentDidCatch(error, errorInfo) {
    // You can also log the error to an error reporting service
    console.error('React Error Boundary caught error:', error, errorInfo);
    this.setState({ errorInfo });
  }

  render() {
    if (this.state.hasError) {
      // You can render any custom fallback UI
      if (this.props.fallback) {
        return this.props.fallback;
      }
      
      return (
        <div className="p-4 border border-red-500 bg-red-50 rounded-lg">
          <h2 className="text-lg font-bold text-red-800 mb-2">Something went wrong</h2>
          <p className="text-red-700 mb-2">{this.state.error?.message || 'Unknown error'}</p>
          {this.state.errorInfo && (
            <details className="mt-2 text-sm">
              <summary className="cursor-pointer text-red-600">View technical details</summary>
              <pre className="mt-2 p-2 bg-gray-100 rounded overflow-auto text-xs">
                {this.state.errorInfo.componentStack}
              </pre>
            </details>
          )}
        </div>
      );
    }

    return this.props.children;
  }
}

export default ErrorBoundary; 