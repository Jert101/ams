<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add unique constraint to user_id if it's not already unique
        Schema::table('users', function (Blueprint $table) {
            $indexes = DB::select("SHOW INDEXES FROM users WHERE Column_name = 'user_id' AND Non_unique = 0");
            if (empty($indexes)) {
                $table->unique('user_id');
            }
        });

        // Get all users ordered by ID
        $users = DB::table('users')->orderBy('id')->get();
        
        // Start user_id from 110523
        $nextId = 110523;
        
        // Update each user with a new user_id if it's null
        foreach ($users as $user) {
            if (empty($user->user_id)) {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['user_id' => $nextId]);
                $nextId++;
            }
        }
        
        // Make user_id required after populating data
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't remove the unique constraint as it might break foreign keys
        // Just make the field nullable again
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }
};
