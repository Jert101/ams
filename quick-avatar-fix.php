<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Quick Avatar Fix</h1>";

// Function to generate SVG content for a user
function generateSvgForUser($name, $userId) {
    // Generate initials from name
    $initials = '';
    $words = explode(' ', $name);
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
    }
    $initials = substr($initials, 0, 2); // Limit to 2 characters
    
    // Generate a consistent color based on the user ID
    $bgColor = '#' . substr(md5($userId), 0, 6);
    
    // Create SVG
    return '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="' . $bgColor . '"/><text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">' . $initials . '</text></svg>';
}

// Get database credentials from .env file
$envFile = __DIR__ . '/.env';
$dbHost = '';
$dbName = '';
$dbUsername = '';
$dbPassword = '';

if (file_exists($envFile)) {
    $envContents = file_get_contents($envFile);
    
    // Extract database credentials
    preg_match('/DB_HOST=([^\n]+)/', $envContents, $hostMatches);
    preg_match('/DB_DATABASE=([^\n]+)/', $envContents, $dbMatches);
    preg_match('/DB_USERNAME=([^\n]+)/', $envContents, $userMatches);
    preg_match('/DB_PASSWORD=([^\n]+)/', $envContents, $passMatches);
    
    $dbHost = isset($hostMatches[1]) ? trim($hostMatches[1]) : 'localhost';
    $dbName = isset($dbMatches[1]) ? trim($dbMatches[1]) : '';
    $dbUsername = isset($userMatches[1]) ? trim($userMatches[1]) : '';
    $dbPassword = isset($passMatches[1]) ? trim($passMatches[1]) : '';
    
    echo "<p>Found database credentials in .env file</p>";
} else {
    echo "<p style='color:orange;'>⚠️ .env file not found. Enter credentials manually.</p>";
}

// Allow manual override of database credentials
if (isset($_POST['db_submit'])) {
    $dbHost = $_POST['db_host'];
    $dbUsername = $_POST['db_username'];
    $dbPassword = $_POST['db_password'];
    $dbName = $_POST['db_name'];
    echo "<p style='color:green;'>✅ Database credentials updated</p>";
}

// Connect to database to show current users
$users = [];
if (!empty($dbUsername) && !empty($dbName)) {
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users
        $stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<p style='color:green;'>✅ Connected to database, found " . count($users) . " users</p>";
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Create avatars directory
if (isset($_POST['create_avatar_dir'])) {
    $profilePhotoDir = __DIR__ . '/public/profile-photos';
    
    if (!file_exists($profilePhotoDir)) {
        if (mkdir($profilePhotoDir, 0777, true)) {
            echo "<p style='color:green;'>✅ Created profile-photos directory: $profilePhotoDir</p>";
            chmod($profilePhotoDir, 0777);
        } else {
            echo "<p style='color:red;'>❌ Failed to create profile-photos directory</p>";
            $error = error_get_last();
            echo "<p>Error: " . ($error ? $error['message'] : 'Unknown error') . "</p>";
        }
    } else {
        echo "<p style='color:green;'>✅ Profile-photos directory already exists</p>";
        chmod($profilePhotoDir, 0777);
    }
    
    // Create a test file
    $testFile = $profilePhotoDir . '/test.svg';
    $svgContent = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200"><rect width="200" height="200" fill="#ff0000"/><text x="100" y="115" font-family="Arial, sans-serif" font-size="80" font-weight="bold" text-anchor="middle" fill="#ffffff">OK</text></svg>';
    
    if (file_put_contents($testFile, $svgContent)) {
        echo "<p style='color:green;'>✅ Created test SVG file: $testFile</p>";
        chmod($testFile, 0644);
        echo "<p>Test image URL: <a href='/profile-photos/test.svg' target='_blank'>/profile-photos/test.svg</a></p>";
        echo "<div style='width:100px; height:100px; border:1px solid #ccc;'>" . $svgContent . "</div>";
    } else {
        echo "<p style='color:red;'>❌ Failed to create test SVG file</p>";
    }
}

// Fix a single user's avatar
if (isset($_POST['fix_single_user']) && !empty($_POST['user_id']) && !empty($dbUsername) && !empty($dbName)) {
    try {
        $userId = (int)$_POST['user_id'];
        
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Create directories if needed
            $profilePhotoDir = __DIR__ . '/public/profile-photos';
            if (!file_exists($profilePhotoDir)) {
                mkdir($profilePhotoDir, 0777, true);
                chmod($profilePhotoDir, 0777);
            }
            
            // Create SVG content
            $svgContent = generateSvgForUser($user['name'], $user['id']);
            
            // Create avatar file
            $avatarFile = $profilePhotoDir . '/user-' . $userId . '.svg';
            if (file_put_contents($avatarFile, $svgContent)) {
                echo "<p style='color:green;'>✅ Created avatar file: $avatarFile</p>";
                chmod($avatarFile, 0644);
                
                // Update user record with the new path
                $avatarPath = 'profile-photos/user-' . $userId . '.svg';
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                $updateStmt->bindParam(':path', $avatarPath);
                $updateStmt->bindParam(':id', $userId);
                
                if ($updateStmt->execute()) {
                    echo "<p style='color:green;'>✅ Updated user record with new avatar path</p>";
                    echo "<p>Avatar URL: <a href='/$avatarPath' target='_blank'>/$avatarPath</a></p>";
                    echo "<div style='width:100px; height:100px; border:1px solid #ccc;'>" . $svgContent . "</div>";
                } else {
                    echo "<p style='color:red;'>❌ Failed to update user record</p>";
                }
            } else {
                echo "<p style='color:red;'>❌ Failed to create avatar file</p>";
            }
        } else {
            echo "<p style='color:red;'>❌ User not found with ID: $userId</p>";
        }
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Fix all users' avatars
if (isset($_POST['fix_all_users']) && !empty($dbUsername) && !empty($dbName)) {
    try {
        $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Get all users
        $stmt = $conn->prepare("SELECT id, name, email, profile_photo_path FROM users");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Create directories if needed
        $profilePhotoDir = __DIR__ . '/public/profile-photos';
        if (!file_exists($profilePhotoDir)) {
            mkdir($profilePhotoDir, 0777, true);
            chmod($profilePhotoDir, 0777);
        }
        
        $count = 0;
        foreach ($users as $user) {
            // Create SVG content
            $svgContent = generateSvgForUser($user['name'], $user['id']);
            
            // Create avatar file
            $avatarFile = $profilePhotoDir . '/user-' . $user['id'] . '.svg';
            if (file_put_contents($avatarFile, $svgContent)) {
                chmod($avatarFile, 0644);
                
                // Update user record with the new path
                $avatarPath = 'profile-photos/user-' . $user['id'] . '.svg';
                $updateStmt = $conn->prepare("UPDATE users SET profile_photo_path = :path WHERE id = :id");
                $updateStmt->bindParam(':path', $avatarPath);
                $updateStmt->bindParam(':id', $user['id']);
                
                if ($updateStmt->execute()) {
                    $count++;
                }
            }
        }
        
        echo "<p style='color:green;'>✅ Updated $count out of " . count($users) . " users with new avatar paths</p>";
    } catch(PDOException $e) {
        echo "<p style='color:red;'>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// HTML Form
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Avatar Fix</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; }
        h1, h2, h3 { color: #3498db; }
        .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .btn { background-color: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #2980b9; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .avatar { width: 40px; height: 40px; border-radius: 50%; }
    </style>
</head>
<body>

<h2>Database Connection</h2>
<form method="post" action="">
    <div class="form-group">
        <label for="db_host">Database Host:</label>
        <input type="text" id="db_host" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
    </div>
    <div class="form-group">
        <label for="db_name">Database Name:</label>
        <input type="text" id="db_name" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
    </div>
    <div class="form-group">
        <label for="db_username">Database Username:</label>
        <input type="text" id="db_username" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
    </div>
    <div class="form-group">
        <label for="db_password">Database Password:</label>
        <input type="password" id="db_password" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
    </div>
    <button type="submit" name="db_submit" class="btn">Update Database Credentials</button>
</form>

<h2>Fix Avatar Display</h2>

<div class="card">
    <h3>Step 1: Create Profile Photos Directory</h3>
    <p>Create a public directory for avatar storage and test write access.</p>
    <form method="post" action="">
        <button type="submit" name="create_avatar_dir" class="btn">Create Profile Photos Directory</button>
    </form>
</div>

<div class="card">
    <h3>Step 2: Fix Single User Avatar</h3>
    <p>Create an SVG avatar for a specific user and update their database record.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <div class="form-group">
            <label for="user_id">User ID:</label>
            <input type="text" id="user_id" name="user_id" value="110007">
        </div>
        <button type="submit" name="fix_single_user" class="btn">Fix Single User Avatar</button>
    </form>
</div>

<div class="card">
    <h3>Step 3: Fix All User Avatars</h3>
    <p>Create SVG avatars for all users and update their database records.</p>
    <form method="post" action="">
        <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
        <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
        <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
        <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
        <button type="submit" name="fix_all_users" class="btn">Fix All User Avatars</button>
    </form>
</div>

<?php if (count($users) > 0): ?>
<h2>Current Users</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Avatar</th>
            <th>Name</th>
            <th>Email</th>
            <th>Profile Photo Path</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']); ?></td>
            <td>
                <?php
                $avatarUrl = '/profile-photos/user-' . $user['id'] . '.svg';
                echo '<div style="width:40px; height:40px; border-radius:50%; overflow:hidden;">' . generateSvgForUser($user['name'], $user['id']) . '</div>';
                ?>
            </td>
            <td><?php echo htmlspecialchars($user['name']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['profile_photo_path']); ?></td>
            <td>
                <form method="post" action="" style="display:inline;">
                    <input type="hidden" name="db_host" value="<?php echo htmlspecialchars($dbHost); ?>">
                    <input type="hidden" name="db_name" value="<?php echo htmlspecialchars($dbName); ?>">
                    <input type="hidden" name="db_username" value="<?php echo htmlspecialchars($dbUsername); ?>">
                    <input type="hidden" name="db_password" value="<?php echo htmlspecialchars($dbPassword); ?>">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                    <button type="submit" name="fix_single_user" class="btn" style="padding:4px 8px; font-size:12px;">Fix</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<h2>How to Test</h2>
<p>After fixing the avatars, try viewing the user profile:</p>
<p><a href="/admin/users/110007/edit?nocache=<?php echo time(); ?>" target="_blank">View User (with cache busting)</a></p>

<h2>Browser Cache</h2>
<p>Also clear your browser cache or try in a private/incognito window.</p>

</body>
</html> 