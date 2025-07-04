/**
 * Profile Photo Fix
 * This script handles profile photo loading errors and attempts to find the image in different locations
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all profile photo images
    const profileImages = document.querySelectorAll('img[src*="profile_photo"]');
    
    // Add error handler to each image
    profileImages.forEach(function(img) {
        img.addEventListener('error', function() {
            // Try to get the user ID from the alt attribute or parent element
            const userName = img.alt;
            const userElement = img.closest('[data-user-id]');
            const userId = userElement ? userElement.dataset.userId : null;
            
            console.log(`Attempting to fix profile photo for ${userName || 'unknown user'}`);
            
            // Try different paths
            const originalSrc = img.src;
            
            // If it's using profile_photo_url but failed, try direct paths
            if (originalSrc.includes('profile_photo_url') || originalSrc.includes('profile-photos')) {
                // Extract filename if possible
                const pathParts = originalSrc.split('/');
                const filename = pathParts[pathParts.length - 1].split('?')[0];
                
                // Try different paths in sequence
                const paths = [
                    `/storage/profile-photos/${filename}`,
                    `/profile-photos/${filename}`,
                    `/img/${filename}`,
                    `/img/kofa.png` // Final fallback
                ];
                
                // Try each path
                tryNextPath(img, paths, 0);
            } else {
                // Just use default
                img.src = '/img/kofa.png';
            }
        });
    });
    
    // Function to try loading image from different paths
    function tryNextPath(img, paths, index) {
        if (index >= paths.length) {
            // All paths failed, use default
            img.src = '/img/kofa.png';
            return;
                }
                
        const newSrc = paths[index];
        const testImg = new Image();
        
        testImg.onload = function() {
            // This path works, use it
            img.src = newSrc;
        };
        
        testImg.onerror = function() {
            // Try next path
            tryNextPath(img, paths, index + 1);
        };
        
        // Add cache buster
        testImg.src = `${newSrc}?v=${Date.now()}`;
    }
});
