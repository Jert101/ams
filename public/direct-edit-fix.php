<?php
// Direct fix for profile photos on edit page

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Direct Edit Page Fix</h1>";

// Step 1: Ensure profile-photos directory exists
if (!file_exists(__DIR__ . '/profile-photos')) {
    if (mkdir(__DIR__ . '/profile-photos', 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: profile-photos</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: profile-photos</p>";
    }
} else {
    echo "<p>✓ Directory exists: profile-photos</p>";
}

// Step 2: Create JavaScript fix
$jsContent = <<<'JS'
// Fix for profile photos on edit page
(function() {
    console.log("Running profile photo fix");
    
    // Function to fix profile photos
    function fixEditPagePhotos() {
        console.log("Looking for profile photo sections");
        
        // Find the profile photo section
        var profileSections = Array.from(document.querySelectorAll("label, div"))
            .filter(function(el) {
                return el.textContent.includes("Profile Photo");
            });
        
        console.log("Found sections:", profileSections.length);
        
        profileSections.forEach(function(section) {
            console.log("Processing section:", section);
            
            // Look for parent container
            var parent = section.parentElement;
            
            // Look for the circular container
            var circleContainer = parent.querySelector(".profile-photo-container");
            
            if (circleContainer) {
                console.log("Found container:", circleContainer);
                
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
                    console.log("No visible images, adding new one");
                    
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
                    console.log("Found existing images:", existingImages.length);
                    
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
                            console.log("Fixed image path:", img.src);
                        }
                        
                        // Add error handler
                        img.onerror = function() {
                            this.src = "/img/kofa.png";
                            console.log("Image error, using default");
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
    setTimeout(fixEditPagePhotos, 1000);
})();
JS;

$jsDir = __DIR__ . '/js';
if (!file_exists($jsDir)) {
    mkdir($jsDir, 0755, true);
}

$jsPath = $jsDir . '/direct-edit-fix.js';
if (file_put_contents($jsPath, $jsContent)) {
    echo "<p style='color:green;'>✅ Created JavaScript fix: js/direct-edit-fix.js</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create JavaScript fix</p>";
}

// Step 3: Create CSS fix
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

$cssDir = __DIR__ . '/css';
if (!file_exists($cssDir)) {
    mkdir($cssDir, 0755, true);
}

$cssPath = $cssDir . '/direct-edit-fix.css';
if (file_put_contents($cssPath, $cssContent)) {
    echo "<p style='color:green;'>✅ Created CSS fix: css/direct-edit-fix.css</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create CSS fix</p>";
}

// Step 4: Create bookmarklet
$bookmarklet = "javascript:(function(){var s=document.createElement('script');s.src='/js/direct-edit-fix.js?v='+new Date().getTime();document.head.appendChild(s);var c=document.createElement('link');c.rel='stylesheet';c.href='/css/direct-edit-fix.css?v='+new Date().getTime();document.head.appendChild(c);})();";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Direct Edit Page Fix</title>
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
    </style>
    <!-- Include the fix on this page -->
    <link rel="stylesheet" href="/css/direct-edit-fix.css?v=<?php echo time(); ?>">
    <script src="/js/direct-edit-fix.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    <h1>Direct Edit Page Fix</h1>
    
    <p>This page provides a fix for profile photos on the edit page.</p>
    
    <h2>How to Use</h2>
    
    <h3>Option 1: Bookmarklet (Easiest)</h3>
    <p>Drag this link to your bookmarks bar, then click it when you're on the edit page:</p>
    <p><a href="<?php echo htmlspecialchars($bookmarklet); ?>" class="button">Fix Edit Page Photos</a></p>
    
    <h3>Option 2: Add to Your Layout</h3>
    <p>Add this code to your layout files just before the <code>&lt;/head&gt;</code> tag:</p>
    <div class="code">&lt;link rel="stylesheet" href="{{ asset('css/direct-edit-fix.css') }}?v={{ time() }}"&gt;
&lt;script src="{{ asset('js/direct-edit-fix.js') }}?v={{ time() }}"&gt;&lt;/script&gt;</div>
    
    <h3>Option 3: Test Now</h3>
    <p>Open the edit page in a new tab:</p>
    <p><a href="/admin/users" target="_blank" class="button">Go to Users Page</a></p>
    
    <h2>What This Fix Does</h2>
    <ol>
        <li>Ensures the profile-photos directory exists</li>
        <li>Creates JavaScript and CSS fixes to display profile photos correctly</li>
        <li>Provides a bookmarklet for easy application of the fix</li>
    </ol>
    
    <h2>Troubleshooting</h2>
    <p>If the fix doesn't work immediately:</p>
    <ol>
        <li>Clear your browser cache</li>
        <li>Make sure your profile photos are in the public/profile-photos/ directory</li>
        <li>Check if your database has the correct paths (should be profile-photos/filename.jpg)</li>
        <li>Try using the bookmarklet while on the edit page</li>
    </ol>
</body>
</html> 