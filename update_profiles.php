<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update all users with NULL profile_photo_path
$updated = \App\Models\User::whereNull('profile_photo_path')
    ->update(['profile_photo_path' => 'kofa.png']);

echo "Updated {$updated} users with default profile photo.\n";

// Verify the changes
$users = \App\Models\User::select('id', 'name', 'email', 'profile_photo_path')->get();

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Photo Path: " . 
         ($user->profile_photo_path ?? 'NULL') . "\n";
} 