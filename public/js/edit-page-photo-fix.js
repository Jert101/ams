// Fix for profile photos on edit page
(function() {
    console.log("Running edit page profile photo fix");
    
    // Function to fix profile photos
    function fixEditPagePhotos() {
        // Find the profile photo section
        var profileSections = Array.from(document.querySelectorAll("label, div"))
            .filter(function(el) {
                return el.textContent.includes("Profile Photo");
            });
        
        console.log("Found profile sections:", profileSections.length);
        
        profileSections.forEach(function(section) {
            // Look for parent container
            var parent = section.parentElement;
            
            // Look for the circular container
            var circleContainer = parent.querySelector(".profile-photo-container");
            
            if (circleContainer) {
                console.log("Found circle container");
                
                // Style the container
                circleContainer.style.display = "block";
                circleContainer.style.width = "100px";
                circleContainer.style.height = "100px";
                circleContainer.style.borderRadius = "50%";
                circleContainer.style.overflow = "hidden";
                circleContainer.style.backgroundColor = "#f0f0f0";
                
                // Check if it already has a visible image
                var existingImages = Array.from(circleContainer.querySelectorAll("img"))
                    .filter(function(img) {
                        return window.getComputedStyle(img).display !== "none";
                    });
                
                if (existingImages.length === 0) {
                    console.log("Adding image to container");
                    
                    // Create new image
                    var img = document.createElement("img");
                    img.src = "/img/kofa.png";
                    img.alt = "Profile Photo";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    // Add to container
                    circleContainer.appendChild(img);
                } else {
                    // Make sure existing image is visible and properly styled
                    existingImages.forEach(function(img) {
                        img.style.display = "block";
                        img.style.visibility = "visible";
                        img.style.width = "100%";
                        img.style.height = "100%";
                        img.style.objectFit = "cover";
                        
                        // Fix path if needed
                        if (img.src.includes("storage/app/public/profile-photos")) {
                            var filename = img.src.split("/").pop();
                            img.src = "/profile-photos/" + filename;
                        }
                        
                        // Add error handler
                        img.onerror = function() {
                            this.src = "/img/kofa.png";
                        };
                    });
                }
            } else {
                console.log("No container found, looking for empty div");
                
                // Look for any empty div that might be the container
                var emptyDiv = parent.querySelector("div:empty");
                
                if (emptyDiv) {
                    console.log("Found empty div, converting to container");
                    
                    // Style the div
                    emptyDiv.className = "profile-photo-container";
                    emptyDiv.style.display = "block";
                    emptyDiv.style.width = "100px";
                    emptyDiv.style.height = "100px";
                    emptyDiv.style.borderRadius = "50%";
                    emptyDiv.style.overflow = "hidden";
                    emptyDiv.style.backgroundColor = "#f0f0f0";
                    
                    // Add image
                    var img = document.createElement("img");
                    img.src = "/img/kofa.png";
                    img.alt = "Profile Photo";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    emptyDiv.appendChild(img);
                } else {
                    console.log("Creating new container");
                    
                    // Create a container
                    var newContainer = document.createElement("div");
                    newContainer.className = "profile-photo-container";
                    newContainer.style.display = "block";
                    newContainer.style.width = "100px";
                    newContainer.style.height = "100px";
                    newContainer.style.borderRadius = "50%";
                    newContainer.style.overflow = "hidden";
                    newContainer.style.backgroundColor = "#f0f0f0";
                    newContainer.style.margin = "10px 0";
                    
                    // Create image
                    var img = document.createElement("img");
                    img.src = "/img/kofa.png";
                    img.alt = "Profile Photo";
                    img.style.width = "100%";
                    img.style.height = "100%";
                    img.style.objectFit = "cover";
                    
                    // Add image to container
                    newContainer.appendChild(img);
                    
                    // Add container after the section
                    parent.appendChild(newContainer);
                }
            }
        });
    }
    
    // Run the fix
    fixEditPagePhotos();
    
    // Also run after DOM loaded and with a delay
    document.addEventListener("DOMContentLoaded", fixEditPagePhotos);
    setTimeout(fixEditPagePhotos, 500);
})(); 