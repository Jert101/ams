<?php
// Generate a direct fix for profile photos on the edit page

// Create JS directory if needed
if (!file_exists(__DIR__ . '/js')) {
    mkdir(__DIR__ . '/js', 0755, true);
}

// Create the JS fix
$jsContent = <<<'JS'
// Direct fix for profile photos on edit page
(function() {
    console.log("Running profile photo fix");
    
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
JS;

file_put_contents(__DIR__ . '/js/edit-fix.js', $jsContent);

// Create CSS fix
$cssContent = <<<'CSS'
/* Fix for profile photos on edit page */
.profile-photo-container {
    display: block !important;
    width: 100px !important;
    height: 100px !important;
    border-radius: 50% !important;
    overflow: hidden !important;
    margin: 10px 0 !important;
    background-color: #f0f0f0 !important;
}

.profile-photo-container img {
    display: block !important;
    visibility: visible !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
}
CSS;

// Create CSS directory if needed
if (!file_exists(__DIR__ . '/css')) {
    mkdir(__DIR__ . '/css', 0755, true);
}

file_put_contents(__DIR__ . '/css/edit-fix.css', $cssContent);

// Create bookmarklet
$bookmarklet = "javascript:(function(){var s=document.createElement('script');s.src='/js/edit-fix.js?v='+new Date().getTime();document.head.appendChild(s);var c=document.createElement('link');c.rel='stylesheet';c.href='/css/edit-fix.css?v='+new Date().getTime();document.head.appendChild(c);})();";

// Create HTML snippet
$htmlSnippet = <<<HTML
<!-- Add this to your layout files to fix profile photos -->
<link rel="stylesheet" href="{{ asset('css/edit-fix.css') }}?v={{ time() }}">
<script src="{{ asset('js/edit-fix.js') }}?v={{ time() }}"></script>
HTML;

file_put_contents(__DIR__ . '/edit-fix-snippet.html', $htmlSnippet);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Page Fix Generator</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 0;
        }
        .code {
            background-color: #f5f5f5;
            padding: 10px;
            border-radius: 4px;
            font-family: monospace;
            white-space: pre-wrap;
        }
        h1 {
            color: #333;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Edit Page Fix Generator</h1>
    
    <p class="success">✅ Created JavaScript fix: /js/edit-fix.js</p>
    <p class="success">✅ Created CSS fix: /css/edit-fix.css</p>
    <p class="success">✅ Created HTML snippet: /edit-fix-snippet.html</p>
    
    <h2>How to Use</h2>
    
    <h3>Option 1: Bookmarklet (Easiest)</h3>
    <p>Drag this link to your bookmarks bar, then click it when you're on the edit page:</p>
    <p><a href="<?php echo htmlspecialchars($bookmarklet); ?>" class="button">Fix Edit Page Photos</a></p>
    
    <h3>Option 2: Add to Your Layout</h3>
    <p>Add this code to your layout files just before the <code>&lt;/head&gt;</code> tag:</p>
    <div class="code">&lt;link rel="stylesheet" href="{{ asset('css/edit-fix.css') }}?v={{ time() }}"&gt;
&lt;script src="{{ asset('js/edit-fix.js') }}?v={{ time() }}"&gt;&lt;/script&gt;</div>
    
    <h3>Option 3: Test Now</h3>
    <p>Open the edit page in a new tab:</p>
    <p><a href="/admin/users" target="_blank" class="button">Go to Users Page</a></p>
    
    <h2>Important Notes</h2>
    <ol>
        <li>Make sure your profile photos are in the <code>public/profile-photos/</code> directory</li>
        <li>The database paths should be <code>profile-photos/filename.jpg</code> (not including "public/")</li>
        <li>If the fix doesn't work, try clearing your browser cache</li>
    </ol>
</body>
</html> 