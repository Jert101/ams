<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check the current column type to understand the issue
        $columnInfo = DB::select("SHOW COLUMNS FROM attendances WHERE Field = 'status'");
        
        if (count($columnInfo) > 0) {
            $currentType = $columnInfo[0]->Type;
            
            // If the enum values don't include 'pending', modify the column
            if (!str_contains($currentType, "'pending'")) {
                // Drop the old status column and recreate it with correct enum values
                Schema::table('attendances', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
                
                Schema::table('attendances', function (Blueprint $table) {
                    $table->enum('status', ['present', 'absent', 'excused', 'pending'])->default('absent')->after('event_id');
                });
                
                // Log the change to the laravel.log file
                \Log::info("Fixed status column in attendances table: Changed from {$currentType} to enum('present','absent','excused','pending')");
            } else {
                \Log::info("Status column already has the correct enum values: {$currentType}");
            }
        } else {
            \Log::error("Could not find status column in attendances table");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Since we're fixing a bug, there's no need to revert this change
        // But if needed, we could revert to the original column definition
    }
};
