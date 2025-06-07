<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->enum('type', ['sunday_mass', 'special_mass', 'other'])->default('other')->after('name');
            $table->enum('mass_order', ['first', 'second', 'third', 'fourth', 'special'])->nullable()->after('type');
            $table->time('end_time')->nullable()->after('time');
            $table->time('attendance_start_time')->nullable()->after('end_time');
            $table->time('attendance_end_time')->nullable()->after('attendance_start_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('mass_order');
            $table->dropColumn('end_time');
            $table->dropColumn('attendance_start_time');
            $table->dropColumn('attendance_end_time');
        });
    }
};
