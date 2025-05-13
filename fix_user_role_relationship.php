<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

// Check if users table has role_id column
if (!Schema::hasColumn('users', 'role_id')) {
    echo "The 'role_id' column is missing from users table. Adding it...\n";
    
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('role_id')->nullable()->constrained();
    });
    
    echo "Added 'role_id' column to users table.\n";
} else {
    echo "The 'role_id' column exists in users table.\n";
    
    // Check if there are users with null role_id
    $usersWithoutRole = DB::table('users')->whereNull('role_id')->count();
    
    if ($usersWithoutRole > 0) {
        echo "Found {$usersWithoutRole} users without a role. Assigning default roles...\n";
        
        // Get the member role ID (default role)
        $memberRoleId = DB::table('roles')->where('name', 'Member')->value('id');
        
        if ($memberRoleId) {
            // Update users without a role to have the member role
            DB::table('users')->whereNull('role_id')->update(['role_id' => $memberRoleId]);
            echo "Assigned Member role to users without a role.\n";
        } else {
            echo "Could not find Member role in roles table.\n";
        }
    } else {
        echo "All users have a role assigned.\n";
    }
}

// Check if any users have invalid role_id (references non-existent role)
$validRoleIds = DB::table('roles')->pluck('id')->toArray();
$usersWithInvalidRole = DB::table('users')
    ->whereNotNull('role_id')
    ->whereNotIn('role_id', $validRoleIds)
    ->count();

if ($usersWithInvalidRole > 0) {
    echo "Found {$usersWithInvalidRole} users with invalid role_id. Fixing...\n";
    
    // Get the member role ID (default role)
    $memberRoleId = DB::table('roles')->where('name', 'Member')->value('id');
    
    if ($memberRoleId) {
        // Update users with invalid role_id to have the member role
        DB::table('users')
            ->whereNotNull('role_id')
            ->whereNotIn('role_id', $validRoleIds)
            ->update(['role_id' => $memberRoleId]);
        echo "Fixed users with invalid role_id.\n";
    } else {
        echo "Could not find Member role in roles table.\n";
    }
} else {
    echo "All users have valid role_id.\n";
}

// Display sample user data with roles
$users = DB::table('users')
    ->select('users.id', 'users.name', 'users.email', 'users.role_id', 'roles.name as role_name')
    ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
    ->limit(5)
    ->get();

echo "\nSample user data with roles:\n";
foreach ($users as $user) {
    echo "User ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Role ID: {$user->role_id}, Role Name: {$user->role_name}\n";
}

echo "\nUser-role relationship check and fix completed.\n";
