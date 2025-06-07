<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// Get the first user
$user = User::first();
echo "User: {$user->name}\n";
echo "Profile Photo Path: " . ($user->profile_photo_path ?? 'null') . "\n";

// Get the profile photo URL directly
$url = '';
if ($user->profile_photo_path) {
    if ($user->profile_photo_path === 'kofa.png') {
        $url = asset('kofa.png');
    } else {
        $url = Storage::disk('public')->url($user->profile_photo_path);
    }
} else {
    $url = 'Default URL';
}

echo "Direct Profile Photo URL: {$url}\n";
echo "Accessor Profile Photo URL: {$user->profile_photo_url}\n";

// Test if kofa.png exists
echo "kofa.png exists in public: " . (file_exists(public_path('kofa.png')) ? 'Yes' : 'No') . "\n";
echo "kofa.png full path: " . public_path('kofa.png') . "\n";

// Test if the storage link is set up
echo "Storage link exists: " . (file_exists(public_path('storage')) ? 'Yes' : 'No') . "\n";
echo "Storage link full path: " . public_path('storage') . "\n";

// Test if the profile-photos directory exists
echo "profile-photos directory exists: " . (is_dir(storage_path('app/public/profile-photos')) ? 'Yes' : 'No') . "\n";
echo "profile-photos full path: " . storage_path('app/public/profile-photos') . "\n";

// Test if the user's profile photo exists
if ($user->profile_photo_path && $user->profile_photo_path !== 'kofa.png') {
    echo "User's profile photo exists: " . (Storage::disk('public')->exists($user->profile_photo_path) ? 'Yes' : 'No') . "\n";
    echo "User's profile photo full path: " . storage_path('app/public/' . $user->profile_photo_path) . "\n";
} 