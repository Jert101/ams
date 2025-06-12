<?php
// InfinityFree Edit Page Profile Photo Fix
// This script creates a simple direct fix for profile photos on edit pages

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>InfinityFree Edit Page Fix</h1>";
echo "<p>This tool creates a direct fix for profile photos on edit pages.</p>";

// Step 1: Create CSS file
$cssDir = __DIR__ . '/public/css';
if (!file_exists($cssDir)) {
    if (mkdir($cssDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/css</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/css</p>";
    }
}

$cssPath = $cssDir . '/profile-fix.css';
$cssContent = <<<CSS
/* Profile photo display fixes for InfinityFree hosting */
.profile-photo-container img {
    display: block !important;
    visibility: visible !important;
}

/* Fix for edit page profile photo */
.profile-user-img {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 50%;
}

/* Ensure profile photos are visible */
img[src^="/profile-photos/"] {
    display: block !important;
    visibility: visible !important;
}
CSS;

if (file_put_contents($cssPath, $cssContent)) {
    echo "<p style='color:green;'>✅ Created CSS file: public/css/profile-fix.css</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create CSS file</p>";
}

// Step 2: Create JavaScript file
$jsDir = __DIR__ . '/public/js';
if (!file_exists($jsDir)) {
    if (mkdir($jsDir, 0755, true)) {
        echo "<p style='color:green;'>✅ Created directory: public/js</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create directory: public/js</p>";
    }
}

$jsPath = $jsDir . '/profile-fix.js';
$jsContent = <<<JS
// Fix profile photo display issues
document.addEventListener('DOMContentLoaded', function() {
    // Fix profile photos on edit page
    const profilePhotos = document.querySelectorAll('img[src*="profile-photos"]');
    profilePhotos.forEach(function(img) {
        img.style.display = 'block';
        img.style.visibility = 'visible';
        img.classList.add('profile-user-img');
        
        // Add error handler to use default image if loading fails
        img.onerror = function() {
            this.src = '/img/kofa.png';
        };
    });
    
    // Also look for profile photo containers
    const photoContainers = document.querySelectorAll('.profile-photo-container');
    photoContainers.forEach(function(container) {
        const img = container.querySelector('img');
        if (img) {
            img.style.display = 'block';
            img.style.visibility = 'visible';
        }
    });
});
JS;

if (file_put_contents($jsPath, $jsContent)) {
    echo "<p style='color:green;'>✅ Created JavaScript file: public/js/profile-fix.js</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create JavaScript file</p>";
}

// Step 3: Create HTML include file
$htmlPath = __DIR__ . '/public/profile-fix.html';
$htmlContent = <<<HTML
<!-- Profile photo display fix for InfinityFree hosting -->
<link rel="stylesheet" href="/css/profile-fix.css">
<script src="/js/profile-fix.js"></script>
HTML;

if (file_put_contents($htmlPath, $htmlContent)) {
    echo "<p style='color:green;'>✅ Created HTML include file: public/profile-fix.html</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create HTML include file</p>";
}

// Step 4: Create PHP injector
$injectorPath = __DIR__ . '/public/fix-injector.php';
$injectorContent = <<<PHP
<?php
// This script injects the profile photo fix into the HTML response
ob_start(function(\$html) {
    // Only process HTML responses
    if (strpos(\$html, '<html') !== false) {
        // Add our CSS and JS to the head section
        \$cssLink = '<link rel="stylesheet" href="/css/profile-fix.css">';
        \$jsScript = '<script src="/js/profile-fix.js"></script>';
        
        // Find the head tag and inject our code
        \$html = str_replace('</head>', "\$cssLink\n\$jsScript\n</head>", \$html);
    }
    return \$html;
});
// Include this file in your index.php before any output
PHP;

if (file_put_contents($injectorPath, $injectorContent)) {
    echo "<p style='color:green;'>✅ Created PHP injector: public/fix-injector.php</p>";
} else {
    echo "<p style='color:red;'>❌ Failed to create PHP injector</p>";
}

// Step 5: Try to modify index.php
$indexPath = __DIR__ . '/public/index.php';
if (file_exists($indexPath)) {
    // Create backup
    $backupPath = $indexPath . '.backup.' . time();
    if (copy($indexPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Created backup of index.php</p>";
        
        // Read index.php
        $indexContent = file_get_contents($indexPath);
        
        // Check if injector is already included
        if (strpos($indexContent, 'fix-injector.php') === false) {
            // Find the opening PHP tag
            $phpPos = strpos($indexContent, '<?php');
            if ($phpPos !== false) {
                // Add our include after the opening PHP tag
                $includeCode = "<?php\nrequire_once __DIR__ . '/fix-injector.php';\n\n";
                $newIndexContent = $includeCode . substr($indexContent, $phpPos + 5);
                
                if (file_put_contents($indexPath, $newIndexContent)) {
                    echo "<p style='color:green;'>✅ Updated index.php to include the fix injector</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update index.php</p>";
                }
            } else {
                echo "<p style='color:orange;'>⚠️ Could not find PHP opening tag in index.php</p>";
            }
        } else {
            echo "<p>Fix injector is already included in index.php</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create backup of index.php</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ index.php not found at: " . $indexPath . "</p>";
}

// Step 6: Create a direct fix file
$directFixPath = __DIR__ . '/public/direct-profile-fix.php';
$directFixContent = <<<PHP
<?php
// Direct profile photo fix for InfinityFree

// Get the requested URL
\$requestUrl = \$_SERVER['REQUEST_URI'] ?? '';

// Check if this is a profile photo request
if (strpos(\$requestUrl, 'profile-photos/') !== false) {
    // Extract the filename
    \$parts = explode('/', \$requestUrl);
    \$filename = end(\$parts);
    
    // Check if the file exists in the public directory
    \$filePath = __DIR__ . '/profile-photos/' . \$filename;
    
    if (file_exists(\$filePath)) {
        // Determine content type
        \$extension = pathinfo(\$filename, PATHINFO_EXTENSION);
        \$contentType = 'image/jpeg'; // Default
        
        switch(strtolower(\$extension)) {
            case 'png':
                \$contentType = 'image/png';
                break;
            case 'gif':
                \$contentType = 'image/gif';
                break;
            case 'svg':
                \$contentType = 'image/svg+xml';
                break;
        }
        
        // Output the image
        header('Content-Type: ' . \$contentType);
        readfile(\$filePath);
        exit;
    } else {
        // If the file doesn't exist, serve the default image
        \$defaultPath = __DIR__ . '/img/kofa.png';
        if (file_exists(\$defaultPath)) {
            header('Content-Type: image/png');
            readfile(\$defaultPath);
            exit;
        }
    }
}

// If not a profile photo request or file not found, continue normal execution
require_once 'index.php';
PHP;

if (file_put_contents($directFixPath, $directFixContent)) {
    echo "<p style='color:green;'>✅ Created direct fix file: public/direct-profile-fix.php</p>";
    echo "<p>You can use this as an alternative approach by adding the following to your .htaccess file:</p>";
    echo "<pre>
# Profile photo fix
RewriteCond %{REQUEST_URI} ^/profile-photos/
RewriteRule ^(.*)$ /direct-profile-fix.php [L]
</pre>";
} else {
    echo "<p style='color:red;'>❌ Failed to create direct fix file</p>";
}

// Step 7: Update .htaccess file
$htaccessPath = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPath)) {
    // Create backup
    $backupPath = $htaccessPath . '.backup.' . time();
    if (copy($htaccessPath, $backupPath)) {
        echo "<p style='color:green;'>✅ Created backup of .htaccess</p>";
        
        // Read .htaccess
        $htaccessContent = file_get_contents($htaccessPath);
        
        // Check if profile-photos rule already exists
        if (strpos($htaccessContent, 'profile-photos/') === false) {
            // Find the RewriteEngine On line
            $rewritePos = strpos($htaccessContent, 'RewriteEngine On');
            if ($rewritePos !== false) {
                // Add our rules after RewriteEngine On
                $rulesCode = "\n    # Profile photos access fix\n    RewriteRule ^profile-photos/ - [L]\n";
                $newHtaccessContent = substr($htaccessContent, 0, $rewritePos + 16) . $rulesCode . substr($htaccessContent, $rewritePos + 16);
                
                // Also add rules for image files
                $imageRules = "\n# Allow access to profile photos\n<FilesMatch \"\.(jpg|jpeg|png|gif|svg)$\">\n    Order allow,deny\n    Allow from all\n    Satisfy any\n</FilesMatch>\n";
                $newHtaccessContent .= $imageRules;
                
                if (file_put_contents($htaccessPath, $newHtaccessContent)) {
                    echo "<p style='color:green;'>✅ Updated .htaccess with profile photo rules</p>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update .htaccess</p>";
                }
            } else {
                echo "<p style='color:orange;'>⚠️ Could not find RewriteEngine On in .htaccess</p>";
            }
        } else {
            echo "<p>Profile photos rule already exists in .htaccess</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Failed to create backup of .htaccess</p>";
    }
} else {
    echo "<p style='color:orange;'>⚠️ .htaccess not found at: " . $htaccessPath . "</p>";
}

echo "<h2>Fix Complete</h2>";
echo "<p>The profile photo fix has been applied. Please follow these steps:</p>";
echo "<ol>";
echo "<li>Clear your browser cache or open a private/incognito window</li>";
echo "<li>Visit the user edit page to see if the profile photo now displays correctly</li>";
echo "</ol>";

echo "<p><a href='/admin/users' style='display: inline-block; padding: 10px; background-color: #4CAF50; color: white; text-decoration: none;'>Go to Users Page</a></p>";
?>
