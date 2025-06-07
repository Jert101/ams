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
        // Settings for the election system
        Schema::create('election_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->enum('status', ['inactive', 'candidacy', 'voting', 'completed'])->default('inactive');
            $table->dateTime('candidacy_start_date')->nullable();
            $table->dateTime('candidacy_end_date')->nullable();
            $table->dateTime('voting_start_date')->nullable();
            $table->dateTime('voting_end_date')->nullable();
            $table->boolean('ignore_automatic_updates')->default(false);
            $table->timestamps();
        });

        // Positions that can be contested in the election
        Schema::create('election_positions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->json('eligible_roles'); // Which roles can apply for this position
            $table->integer('max_votes_per_voter')->default(1);
            $table->integer('election_settings_id');
            $table->timestamps();
        });

        // Candidates for the election
        Schema::create('election_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained('election_positions')->onDelete('cascade');
            $table->text('platform')->nullable();
            $table->text('qualifications')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });

        // Votes cast by users
        Schema::create('election_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('election_candidates')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('election_positions')->onDelete('cascade');
            $table->timestamps();
        });

        // Archive of past elections
        Schema::create('election_archives', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->json('results'); // Structured JSON with positions, candidates, and vote counts
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('election_votes');
        Schema::dropIfExists('election_candidates');
        Schema::dropIfExists('election_positions');
        Schema::dropIfExists('election_settings');
        Schema::dropIfExists('election_archives');
    }
}; 