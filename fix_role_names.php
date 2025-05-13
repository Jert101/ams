<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all roles
$roles = DB::table('roles')->get();

echo "Current roles in database:\n";
foreach ($roles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Description: {$role->description}\n";
}

// Fix role names to ensure they match exactly what's expected in the code
$expectedRoles = [
    1 => 'Admin',
    2 => 'Officer',
    3 => 'Secretary',
    4 => 'Member'
];

echo "\nUpdating role names to match expected values...\n";

foreach ($expectedRoles as $id => $name) {
    DB::table('roles')
        ->where('id', $id)
        ->update(['name' => $name]);
    
    echo "Updated role ID {$id} to name '{$name}'\n";
}

// Verify the changes
$updatedRoles = DB::table('roles')->get();

echo "\nUpdated roles in database:\n";
foreach ($updatedRoles as $role) {
    echo "ID: {$role->id}, Name: {$role->name}, Description: {$role->description}\n";
}

echo "\nRole names have been fixed.\n";
