/**
 * Admin Dashboard Fix
 * Ensures the React component loads correctly and handles fallbacks
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if React component loaded successfully
    setTimeout(function() {
        const reactRoot = document.querySelector('[data-react-root]');
        const fallbackContent = document.getElementById('admindashboard-fallback-content');
        
        if (reactRoot && fallbackContent) {
            // Check if React component has rendered content
            if (reactRoot.children.length === 0) {
                console.log('React component failed to load, showing fallback content');
                fallbackContent.style.display = 'block';
            }
        }
    }, 1000); // Wait 1 second for React to render
    
    // Fix profile photos in the dashboard
    function fixDashboardProfilePhotos() {
        const profilePhotos = document.querySelectorAll('img.rounded-full');
        
        profilePhotos.forEach(img => {
            // Add error handler
            img.onerror = function() {
                this.onerror = null;
                this.src = '/img/kofa.png';
                return true;
            };
            
            // Check if already loaded but broken
            if (img.complete && (img.naturalWidth === 0 || img.naturalHeight === 0)) {
                img.src = '/img/kofa.png';
            }
            
            // Fix URLs for profile photos
            const src = img.getAttribute('src');
            if (src && src.includes('/storage/profile-photos/')) {
                const filename = src.split('/').pop().split('?')[0];
                img.setAttribute('src', `/profile-photos/${filename}?v=${Date.now()}`);
            }
        });
    }
    
    // Run the fix immediately and periodically
    fixDashboardProfilePhotos();
    setInterval(fixDashboardProfilePhotos, 2000); // Check every 2 seconds
}); 