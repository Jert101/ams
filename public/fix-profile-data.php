<?php
// This script fixes issues with profile_photo_path in the database
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Security check - don't allow direct public access
if (!isset($_GET['key']) || $_GET['key'] !== '8e24905d78c97be5c5a9a5c7237c8afa') {
    http_response_code(403);
    echo "Access denied";
    exit;
}

// Load Laravel bootstrap
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<h1>Profile Photo Path Database Fix</h1>';

// Function to show messages
function show_msg($message, $is_error = false) {
    echo '<p style="' . ($is_error ? 'color:red;' : 'color:green;') . '">' . $message . '</p>';
}

// Check if we're performing an update
$perform_update = isset($_GET['update']) && $_GET['update'] === 'true';

// Use Laravel DB facade to get users with incorrect profile_photo_path
try {
    $users_with_issues = DB::table('users')
        ->whereIn('profile_photo_path', ['0', 0, '', 'NULL', 'null'])
        ->orWhereNull('profile_photo_path')
        ->get();
    
    show_msg("Found " . count($users_with_issues) . " users with problematic profile_photo_path values");
    
    // Show the problematic users
    if (count($users_with_issues) > 0) {
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse; margin-bottom: 20px;">';
        echo '<tr><th>User ID</th><th>Name</th><th>Current Profile Photo Path</th><th>Action</th></tr>';
        
        foreach ($users_with_issues as $user) {
            echo '<tr>';
            echo '<td>' . $user->user_id . '</td>';
            echo '<td>' . $user->name . '</td>';
            echo '<td>' . ($user->profile_photo_path ?? 'NULL') . '</td>';
            
            if ($perform_update) {
                // Update the user's profile_photo_path to default
                $updated = DB::table('users')
                    ->where('user_id', $user->user_id)
                    ->update(['profile_photo_path' => 'kofa.png']);
                
                if ($updated) {
                    echo '<td style="color:green;">Fixed - Set to kofa.png</td>';
                } else {
                    echo '<td style="color:red;">Failed to update</td>';
                }
            } else {
                echo '<td>Will be fixed when update is performed</td>';
            }
            
            echo '</tr>';
        }
        
        echo '</table>';
        
        if (!$perform_update) {
            echo '<p><a href="?key=8e24905d78c97be5c5a9a5c7237c8afa&update=true" style="padding: 10px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px;">Fix All Issues</a></p>';
            echo '<p>Click the button above to set all problematic profile_photo_path values to "kofa.png"</p>';
        } else {
            show_msg("All problematic values have been updated to 'kofa.png'");
        }
    }
} catch (Exception $e) {
    show_msg("Error accessing database: " . $e->getMessage(), true);
}

// Additional manual fix form for specific user
echo '<h2>Fix Specific User</h2>';
echo '<form method="get">';
echo '<input type="hidden" name="key" value="8e24905d78c97be5c5a9a5c7237c8afa">';
echo '<p>User ID: <input type="text" name="user_id" required></p>';
echo '<p>New Profile Photo Path: <input type="text" name="photo_path" value="kofa.png" required></p>';
echo '<p><input type="submit" name="fix_user" value="Fix User"></p>';
echo '</form>';

// Process manual fix
if (isset($_GET['fix_user']) && isset($_GET['user_id']) && isset($_GET['photo_path'])) {
    $user_id = $_GET['user_id'];
    $photo_path = $_GET['photo_path'];
    
    try {
        $user = DB::table('users')->where('user_id', $user_id)->first();
        
        if ($user) {
            show_msg("Found user: " . $user->name);
            show_msg("Current profile photo path: " . ($user->profile_photo_path ?? 'NULL'));
            
            $updated = DB::table('users')
                ->where('user_id', $user_id)
                ->update(['profile_photo_path' => $photo_path]);
            
            if ($updated) {
                show_msg("Successfully updated profile photo path to: " . $photo_path);
            } else {
                show_msg("Failed to update profile photo path", true);
            }
        } else {
            show_msg("User not found with ID: " . $user_id, true);
        }
    } catch (Exception $e) {
        show_msg("Error updating user: " . $e->getMessage(), true);
    }
}

// Final note
echo '<h2>After Fixing</h2>';
echo '<p>After fixing the database values, you should:</p>';
echo '<ol>';
echo '<li>Clear Laravel cache: <code>php artisan cache:clear</code></li>';
echo '<li>Try updating profile photos again</li>';
echo '<li>If issues persist, check if the storage symlink is properly set up with <a href="quick-storage-fix.php?key=8e24905d78c97be5c5a9a5c7237c8afa">the quick fix tool</a></li>';
echo '</ol>';
?> 