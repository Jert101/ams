<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = \App\Models\User::select('id', 'name', 'email', 'profile_photo_path')->get();

foreach ($users as $user) {
    echo "ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Photo Path: " . 
         ($user->profile_photo_path ?? 'NULL') . "\n";
} 