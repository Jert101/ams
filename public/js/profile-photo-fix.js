// Fix for profile photos on edit page
(function() {
    // Function to fix profile photos
    function fixProfilePhotos() {
        // Find all profile photo images
        var profilePhotos = document.querySelectorAll('img');
        
        profilePhotos.forEach(function(img) {
            // Check if the image is a profile photo
            if (img.src && (
                img.src.includes('profile-photos') || 
                img.classList.contains('profile-user-img') ||
                img.alt && img.alt.includes('profile photo')
            )) {
                console.log("Found profile photo:", img.src);
                
                // Fix external domain URLs
                if (img.src.includes('ckpkofa-network.ct.ws/profile-photos')) {
                    // Extract filename from URL
                    var filename = img.src.split('/').pop().split('?')[0];
                    
                    // First try to load from local storage path
                    img.src = '/storage/profile-photos/' + filename;
                    console.log("Fixed image path to storage:", img.src);
                    
                    // Add error handler to try public path if storage fails
                    img.onerror = function() {
                        this.src = '/profile-photos/' + filename;
                        console.log("Trying public path:", this.src);
                        
                        // Add another error handler for final fallback
                        this.onerror = function() {
                            this.src = '/img/kofa.png';
                            console.log("Using default image");
                        };
                    };
                }
                
                // Make sure image is visible
                img.style.display = "block";
                img.style.visibility = "visible";
            }
        });
    }
    
    // Run the fix when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', fixProfilePhotos);
    } else {
        fixProfilePhotos();
    }
    
    // Also run after a short delay to catch dynamically loaded images
    setTimeout(fixProfilePhotos, 1000);
})();