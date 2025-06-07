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
        Schema::create('mass_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['sunday_mass', 'special_mass'])->default('sunday_mass');
            $table->enum('mass_order', ['first', 'second', 'third', 'fourth', 'special'])->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->time('attendance_start_time');
            $table->time('attendance_end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mass_schedules');
    }
};
