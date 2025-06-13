// Fix for profile photos on edit page
(function() {
    // Function to fix profile photos
    function fixProfilePhotos() {
        console.log("Running profile photo fix script...");
        
        // Find all profile photo images
        var profilePhotos = document.querySelectorAll('img');
        
        profilePhotos.forEach(function(img) {
            // Check if the image is a profile photo
            if (img.src && (
                img.src.includes('profile-photos') || 
                img.classList.contains('profile-user-img') ||
                img.alt && (img.alt.includes('profile photo') || img.alt.includes('profile picture')) ||
                img.parentElement && img.parentElement.classList.contains('rounded-full')
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
                        console.log("Storage path failed, trying public path...");
                        this.src = '/profile-photos/' + filename;
                        console.log("Trying public path:", this.src);
                        
                        // Add another error handler for final fallback
                        this.onerror = function() {
                            console.log("Public path failed, using default image");
                            this.src = '/img/kofa.png';
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
    setTimeout(fixProfilePhotos, 500);
    
    // And run again after a longer delay to catch any late-loading images
    setTimeout(fixProfilePhotos, 2000);
    
    // Add a MutationObserver to watch for dynamically added images
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes && mutation.addedNodes.length > 0) {
                // Check if any of the added nodes are images or contain images
                for (var i = 0; i < mutation.addedNodes.length; i++) {
                    var node = mutation.addedNodes[i];
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'IMG') {
                            // If it's an image, check if it needs fixing
                            if (node.src && node.src.includes('ckpkofa-network.ct.ws/profile-photos')) {
                                fixProfilePhotos();
                                break;
                            }
                        } else if (node.querySelectorAll) {
                            // If it's another element, check if it contains images
                            var images = node.querySelectorAll('img');
                            if (images.length > 0) {
                                fixProfilePhotos();
                                break;
                            }
                        }
                    }
                }
            }
        });
    });
    
    // Start observing the document with the configured parameters
    observer.observe(document.body, { childList: true, subtree: true });
})();