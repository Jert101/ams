<?php
// Simple diagnostic page to test file uploads for profile pictures

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Profile Upload Diagnostic Tool</h1>";

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Request Received</h2>";
    
    // Output POST data
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Output FILES data
    echo "<h3>FILES Data:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    // Check for file upload
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        echo "<p style='color: green;'>File was uploaded successfully.</p>";
        
        // Get file details
        $fileName = $_FILES['profile_photo']['name'];
        $fileType = $_FILES['profile_photo']['type'];
        $fileSize = $_FILES['profile_photo']['size'];
        $fileTmpName = $_FILES['profile_photo']['tmp_name'];
        
        echo "<p>File Name: $fileName</p>";
        echo "<p>File Type: $fileType</p>";
        echo "<p>File Size: $fileSize bytes</p>";
        echo "<p>Temporary Location: $fileTmpName</p>";
        
        // Try to save the file
        $uploadDir = __DIR__ . '/../storage/app/public/profile-photos/';
        
        // Check if directory exists and is writable
        if (!file_exists($uploadDir)) {
            echo "<p style='color: red;'>Upload directory does not exist!</p>";
            echo "<p>Attempting to create directory...</p>";
            
            if (mkdir($uploadDir, 0755, true)) {
                echo "<p style='color: green;'>Directory created successfully.</p>";
            } else {
                echo "<p style='color: red;'>Failed to create directory!</p>";
            }
        }
        
        if (!is_writable($uploadDir)) {
            echo "<p style='color: red;'>Upload directory is not writable!</p>";
            echo "<p>Directory path: $uploadDir</p>";
            echo "<p>Directory permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";
        } else {
            echo "<p style='color: green;'>Upload directory is writable.</p>";
        }
        
        // Attempt to save the file
        $newFileName = time() . '-test-' . $fileName;
        $destination = $uploadDir . $newFileName;
        
        echo "<p>Attempting to save to: $destination</p>";
        
        if (move_uploaded_file($fileTmpName, $destination)) {
            echo "<p style='color: green;'>File saved successfully!</p>";
            echo "<p>Saved to: $destination</p>";
            
            // Check if the file is readable after upload
            if (is_readable($destination)) {
                echo "<p style='color: green;'>File is readable after upload.</p>";
            } else {
                echo "<p style='color: red;'>File is NOT readable after upload!</p>";
            }
            
            // Check the permissions of the saved file
            echo "<p>File permissions: " . substr(sprintf('%o', fileperms($destination)), -4) . "</p>";
        } else {
            echo "<p style='color: red;'>Failed to save file!</p>";
            echo "<p>PHP error: " . error_get_last()['message'] . "</p>";
        }
    } else if (isset($_FILES['profile_photo'])) {
        echo "<p style='color: red;'>File upload error: " . uploadErrorToString($_FILES['profile_photo']['error']) . "</p>";
    } else {
        echo "<p style='color: red;'>No file was uploaded in the request.</p>";
    }
}

// Convert upload error code to string
function uploadErrorToString($errorCode) {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "The uploaded file exceeds the upload_max_filesize directive in php.ini";
        case UPLOAD_ERR_FORM_SIZE:
            return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
        case UPLOAD_ERR_PARTIAL:
            return "The uploaded file was only partially uploaded";
        case UPLOAD_ERR_NO_FILE:
            return "No file was uploaded";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "Missing a temporary folder";
        case UPLOAD_ERR_CANT_WRITE:
            return "Failed to write file to disk";
        case UPLOAD_ERR_EXTENSION:
            return "A PHP extension stopped the file upload";
        default:
            return "Unknown upload error";
    }
}

// Show the test form
echo "<h2>Test Upload Form</h2>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<div>";
echo "<label for='name'>Name:</label>";
echo "<input type='text' name='name' id='name' value='Test User'>";
echo "</div><br>";
echo "<div>";
echo "<label for='email'>Email:</label>";
echo "<input type='email' name='email' id='email' value='test@example.com'>";
echo "</div><br>";
echo "<div>";
echo "<label for='profile_photo'>Profile Photo:</label>";
echo "<input type='file' name='profile_photo' id='profile_photo'>";
echo "</div><br>";
echo "<div>";
echo "<button type='submit'>Submit</button>";
echo "</div>";
echo "</form>";

// System information
echo "<h2>System Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Upload Max Filesize: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Post Max Size: " . ini_get('post_max_size') . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Check storage directories
echo "<h2>Storage Directory Check</h2>";
$storageAppPublic = __DIR__ . '/../storage/app/public';
$storageAppPublicProfilePhotos = $storageAppPublic . '/profile-photos';
$publicStorage = __DIR__ . '/storage';

echo "<p>Storage app/public directory: ";
echo file_exists($storageAppPublic) ? "<span style='color: green;'>Exists</span>" : "<span style='color: red;'>Does NOT exist</span>";
echo "</p>";

echo "<p>Profile photos directory: ";
echo file_exists($storageAppPublicProfilePhotos) ? "<span style='color: green;'>Exists</span>" : "<span style='color: red;'>Does NOT exist</span>";
echo "</p>";

echo "<p>Public storage symlink: ";
echo file_exists($publicStorage) ? "<span style='color: green;'>Exists</span>" : "<span style='color: red;'>Does NOT exist</span>";
echo "</p>";

if (file_exists($publicStorage)) {
    echo "<p>Public storage is " . (is_link($publicStorage) ? "a symlink" : "NOT a symlink") . "</p>";
    if (is_link($publicStorage)) {
        echo "<p>Symlink target: " . readlink($publicStorage) . "</p>";
    }
} 