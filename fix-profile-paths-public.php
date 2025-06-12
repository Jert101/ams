<?php
// 1. Update all profile_photo_path values to start with 'public/profile-photos/'
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Update profile_photo_path to public/profile-photos/</h1>";

require_once 'vendor/autoload.php';
if (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$database = $_ENV['DB_DATABASE'] ?? 'ams';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

try {
    $db = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update all paths that do not already start with public/profile-photos/
    $users = $db->query("SELECT id, profile_photo_path FROM users WHERE profile_photo_path IS NOT NULL AND profile_photo_path != ''")->fetchAll(PDO::FETCH_ASSOC);
    $updated = 0;
    foreach ($users as $user) {
        $path = $user['profile_photo_path'];
        if (strpos($path, 'public/profile-photos/') !== 0) {
            // Remove any leading 'profile-photos/' or similar
            $filename = basename($path);
            $newPath = 'public/profile-photos/' . $filename;
            $stmt = $db->prepare("UPDATE users SET profile_photo_path = ? WHERE id = ?");
            if ($stmt->execute([$newPath, $user['id']])) {
                echo "<p style='color:green;'>Updated user {$user['id']} to $newPath</p>";
                $updated++;
            }
        }
    }
    echo "<p style='color:green;'>Total updated: $updated</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>DB error: {$e->getMessage()}</p>";
}

// 2. Update User model to strip 'public/' from the URL
$userModelPath = 'app/Models/User.php';
if (file_exists($userModelPath)) {
    $userModel = file_get_contents($userModelPath);
    $pattern = '/public function getProfilePhotoUrlAttribute\s*\([^)]*\)\s*\{[^}]*\}/s';
    $replacement = <<<PHP
public function getProfilePhotoUrlAttribute()
{
    $path = $this->profile_photo_path;
    if ($path && strpos($path, 'public/') === 0) {
        $path = substr($path, 7);
    }
    return $path ? asset($path) : asset('profile-photos/kofa.png');
}
PHP;
    $fixedModel = preg_replace($pattern, $replacement, $userModel);
    if ($fixedModel !== $userModel) {
        file_put_contents($userModelPath, $fixedModel);
        echo "<p style='color:green;'>User model updated to strip 'public/' from URL</p>";
    } else {
        // If method doesn't exist, add it
        if (strpos($userModel, 'getProfilePhotoUrlAttribute') === false) {
            $fixedModel = preg_replace(
                '/class User extends Authenticatable \{/',
                "class User extends Authenticatable {\n\n    public function getProfilePhotoUrlAttribute()\n    {\n        $path = $this->profile_photo_path;\n        if ($path && strpos($path, 'public/') === 0) {\n            $path = substr($path, 7);\n        }\n        return $path ? asset($path) : asset('profile-photos/kofa.png');\n    }\n",
                $userModel
            );
            file_put_contents($userModelPath, $fixedModel);
            echo "<p style='color:green;'>User model method added and strips 'public/' from URL</p>";
        } else {
            echo "<p style='color:orange;'>User model already up to date</p>";
        }
    }
} else {
    echo "<p style='color:red;'>User model not found at $userModelPath</p>";
}

echo "<h2>Done!</h2>";
?> 