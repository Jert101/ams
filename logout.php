<?php

// Bootstrap the Laravel application
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Log out the user
auth()->logout();
session()->flush();
session()->regenerate();

echo "You have been logged out successfully!";
echo "\n\nYou can now visit http://localhost/ams to see the landing page with login/register buttons."; 