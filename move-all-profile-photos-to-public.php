<?php
// move-all-profile-photos-to-public.php
// Run this script from the project root

$storageDir = __DIR__ . '/storage/app/public/profile-photos/';
$publicDir = __DIR__ . '/public/profile-photos/';

if (!is_dir($publicDir)) {
    mkdir($publicDir, 0777, true);
}

$files = glob($storageDir . '*');
if (!$files) {
    echo "No files found in $storageDir\n";
    exit;
}

foreach ($files as $file) {
    $filename = basename($file);
    $dest = $publicDir . $filename;
    if (!file_exists($dest)) {
        if (copy($file, $dest)) {
            echo "[OK] Moved $filename to public/profile-photos/\n";
        } else {
            echo "[FAIL] Could not move $filename\n";
        }
    } else {
        echo "[SKIP] $filename already exists in public/profile-photos/\n";
    }
}

echo "\nDone. All files from storage/app/public/profile-photos/ are now in public/profile-photos/.\n"; 