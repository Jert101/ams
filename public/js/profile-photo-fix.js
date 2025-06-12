// Fix for profile photos on edit page
(function() {
    // Function to fix profile photos
    function fixProfilePhotos() {
        // Find the profile photo section
        var profileSections = Array.from(document.querySelectorAll("label, div"))
            .filter(function(el) {
                return el.textContent.includes("Profile Photo");
            });
        
        profileSections.forEach(function(section) {
            // Find the container
            var parent = section.parentElement;
            var container = parent.querySelector(".profile-photo-container");
            
            if (container) {
                // Check for existing images
                var img = container.querySelector("img");
                if (img) {
                    // Make sure image is visible
                    img.style.display = "block";
                    img.style.visibility = "visible";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    // Add error handler
                    img.onerror = function() {
                        this.src = "/img/kofa.png";
                    };
                } else {
                    // Create default image
                    var newImg = document.createElement("img");
                    newImg.src = "/img/kofa.png";
                    newImg.alt = "Profile Photo";
                    newImg.style.width = "100%";
                    newImg.style.height = "100%";
                    newImg.style.objectFit = "cover";
                    
                    // Add to container
                    container.appendChild(newImg);
                }
                
                // Make container visible
                container.style.display = "block";
                container.style.width = "100px";
                container.style.height = "100px";
                container.style.borderRadius = "50%";
                container.style.overflow = "hidden";
            }
        });
    }
    
    // Run the fix
    fixProfilePhotos();
    
    // Also run after DOM loaded and with a delay
    document.addEventListener("DOMContentLoaded", fixProfilePhotos);
    setTimeout(fixProfilePhotos, 500);
})();