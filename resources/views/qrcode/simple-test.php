<?php
// This is a simple PHP file to test rendering without Blade
// Access using http://127.0.0.1:8000/qrcode/simple-test.php if PHP files are directly accessible

if (isset($user)) {
    echo "<h1>User Data</h1>";
    echo "<p>Name: " . ($user->name ?? 'N/A') . "</p>";
    echo "<p>ID: " . ($user->user_id ?? 'N/A') . "</p>";
    echo "<p>Email: " . ($user->email ?? 'N/A') . "</p>";
} else {
    echo "<h1>User not set</h1>";
    echo "<p>This file is meant to be included from a controller with a user variable.</p>";
}
?> 