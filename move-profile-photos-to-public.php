<?php
// move-profile-photos-to-public.php
// Run this script from the project root (where artisan is)

use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel (if not already)
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get all users with a profile photo path set (not kofa.png)
$users = DB::table('users')
    ->whereNotNull('profile_photo_path')
    ->where('profile_photo_path', '!=', 'kofa.png')
    ->get();

$storageDir = __DIR__ . '/storage/app/public/profile-photos/';
$publicDir = __DIR__ . '/public/profile-photos/';

if (!is_dir($publicDir)) {
    mkdir($publicDir, 0777, true);
}

foreach ($users as $user) {
    $originalPath = $user->profile_photo_path;
    $filename = basename($originalPath);
    $source = $storageDir . $filename;
    $dest = $publicDir . $filename;

    if (!file_exists($source)) {
        echo "[SKIP] Source not found for user {$user->id}: $source\n";
        continue;
    }

    if (!file_exists($dest)) {
        if (copy($source, $dest)) {
            echo "[OK] Copied $source to $dest\n";
        } else {
            echo "[FAIL] Could not copy $source to $dest\n";
            continue;
        }
    } else {
        echo "[EXISTS] $dest already exists\n";
    }

    // Update DB if needed
    if ($user->profile_photo_path !== $filename) {
        DB::table('users')->where('id', $user->id)->update(['profile_photo_path' => $filename]);
        echo "[DB] Updated user {$user->id} profile_photo_path to $filename\n";
    }
}

echo "\nDone. All user profile photos should now be in public/profile-photos/ and DB values updated.\n"; 