// Fix for profile photos on edit page
(function() {
    console.log("Running edit page profile photo fix");
    
    // Function to fix profile photos
    function fixEditPagePhotos() {
        // Find the profile photo section by looking for the label
        var profileLabels = Array.from(document.querySelectorAll("label"))
            .filter(function(label) {
                return label.textContent.includes("Profile Photo");
            });
        
        if (profileLabels.length > 0) {
            console.log("Found profile photo labels:", profileLabels.length);
            
            profileLabels.forEach(function(label) {
                // Find the parent container
                var parent = label.parentElement;
                
                // Look for the profile photo container
                var container = parent.querySelector(".profile-photo-container");
                
                if (!container) {
                    // Look for empty divs that might be the container
                    var emptyDivs = Array.from(parent.querySelectorAll("div"))
                        .filter(function(div) {
                            return div.children.length === 0 || 
                                  (div.children.length === 1 && 
                                   div.children[0].tagName === "IMG" && 
                                   window.getComputedStyle(div.children[0]).display === "none");
                        });
                    
                    if (emptyDivs.length > 0) {
                        container = emptyDivs[0];
                        container.className = "profile-photo-container";
                    } else {
                        // Create a new container
                        container = document.createElement("div");
                        container.className = "profile-photo-container";
                        parent.appendChild(container);
                    }
                }
                
                // Style the container
                container.style.display = "block";
                container.style.width = "100px";
                container.style.height = "100px";
                container.style.borderRadius = "50%";
                container.style.overflow = "hidden";
                container.style.backgroundColor = "#f0f0f0";
                container.style.margin = "10px 0";
                
                // Check for existing images
                var images = container.querySelectorAll("img");
                var visibleImages = Array.from(images)
                    .filter(function(img) {
                        return window.getComputedStyle(img).display !== "none";
                    });
                
                if (visibleImages.length === 0) {
                    // If there's a hidden image, make it visible
                    if (images.length > 0) {
                        var img = images[0];
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
                    } else {
                        // Create a new image
                        var newImg = document.createElement("img");
                        newImg.src = "/img/kofa.png";
                        newImg.alt = "Profile Photo";
                        newImg.style.width = "100%";
                        newImg.style.height = "100%";
                        newImg.style.objectFit = "cover";
                        container.appendChild(newImg);
                    }
                }
            });
        } else {
            console.log("No profile photo labels found, looking for alternative elements");
            
            // Try to find the profile photo section by other means
            var profileSections = Array.from(document.querySelectorAll("div"))
                .filter(function(div) {
                    return div.textContent.includes("Profile Photo");
                });
            
            if (profileSections.length > 0) {
                console.log("Found profile sections:", profileSections.length);
                
                profileSections.forEach(function(section) {
                    // Look for empty circular divs
                    var emptyDivs = Array.from(section.querySelectorAll("div"))
                        .filter(function(div) {
                            return div.children.length === 0 || 
                                  (div.children.length === 1 && 
                                   div.children[0].tagName === "IMG" && 
                                   window.getComputedStyle(div.children[0]).display === "none");
                        });
                    
                    if (emptyDivs.length > 0) {
                        var container = emptyDivs[0];
                        container.className = "profile-photo-container";
                        container.style.display = "block";
                        container.style.width = "100px";
                        container.style.height = "100px";
                        container.style.borderRadius = "50%";
                        container.style.overflow = "hidden";
                        container.style.backgroundColor = "#f0f0f0";
                        
                        // Add image
                        var img = document.createElement("img");
                        img.src = "/img/kofa.png";
                        img.alt = "Profile Photo";
                        img.style.width = "100%";
                        img.style.height = "100%";
                        img.style.objectFit = "cover";
                        container.appendChild(img);
                    }
                });
            }
        }
    }
    
    // Run the fix
    fixEditPagePhotos();
    
    // Also run after DOM loaded and with a delay
    document.addEventListener("DOMContentLoaded", fixEditPagePhotos);
    setTimeout(fixEditPagePhotos, 500);
    setTimeout(fixEditPagePhotos, 1000);
})(); 