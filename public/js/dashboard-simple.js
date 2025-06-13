/**
 * Simple Dashboard Script
 * Ensures images load properly
 */
document.addEventListener('DOMContentLoaded', function() {
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