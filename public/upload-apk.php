<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set a password to protect the upload form
$password = "ckpkofa2023"; // Change this to a secure password

// Check if the downloads directory exists, if not create it
$downloadsDir = __DIR__ . '/downloads';
if (!is_dir($downloadsDir)) {
    mkdir($downloadsDir, 0755, true);
}

$message = '';
$passwordError = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify password
    if (!isset($_POST['password']) || $_POST['password'] !== $password) {
        $passwordError = "Incorrect password. Please try again.";
    } else {
        // Check if file was uploaded without errors
        if (isset($_FILES['apk_file']) && $_FILES['apk_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedFile = $_FILES['apk_file']['tmp_name'];
            $destination = $downloadsDir . '/ckp-kofa-app.apk';
            
            // Move the uploaded file to the destination
            if (move_uploaded_file($uploadedFile, $destination)) {
                $message = "File uploaded successfully! Size: " . filesize($destination) . " bytes";
            } else {
                $message = "Error: Failed to move uploaded file. Check permissions.";
            }
        } else {
            $message = "Error: " . $_FILES['apk_file']['error'];
        }
    }
}

// HTML for the upload form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Upload APK File</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="password"], input[type="file"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>
    <h1>Upload APK File</h1>
    
    <p>Use this form to upload the APK file to the server.</p>
    
    <?php if ($message): ?>
        <div class="<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <?php if ($passwordError): ?>
                <div class="error"><?php echo $passwordError; ?></div>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="apk_file">APK File:</label>
            <input type="file" id="apk_file" name="apk_file" accept=".apk" required>
        </div>
        
        <input type="submit" value="Upload APK">
    </form>
    
    <h2>Current Status</h2>
    <p>Downloads directory: <?php echo is_dir($downloadsDir) ? "Exists" : "Not found"; ?></p>
    
    <?php if (is_dir($downloadsDir)): ?>
        <h3>Files in downloads directory:</h3>
        <ul>
            <?php 
            $files = scandir($downloadsDir);
            $found = false;
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    echo "<li>$file (" . filesize("$downloadsDir/$file") . " bytes)</li>";
                    $found = true;
                }
            }
            if (!$found) {
                echo "<li>No files found</li>";
            }
            ?>
        </ul>
    <?php endif; ?>
</body>
</html> 