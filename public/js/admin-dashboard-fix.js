/**
 * Admin Dashboard Fix
 * This script ensures the dashboard displays correctly
 */
document.addEventListener('DOMContentLoaded', function() {
    // Make sure the dashboard content is visible
    const dashboardContent = document.getElementById('admindashboard-content');
    if (dashboardContent) {
        dashboardContent.style.display = 'block';
    }
    
    // Apply consistent styling to profile images
    const profileImages = document.querySelectorAll('.flex-shrink-0 img');
    profileImages.forEach(function(img) {
        img.onerror = function() {
            this.src = '/img/kofa.png';
        };
        
        // Check if already loaded but broken
        if (img.complete && (img.naturalWidth === 0 || img.naturalHeight === 0)) {
            img.src = '/img/kofa.png';
        }
    });
}); 