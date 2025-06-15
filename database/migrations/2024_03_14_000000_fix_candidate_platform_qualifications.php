<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ElectionCandidate;

class FixCandidatePlatformQualifications extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all candidates
        $candidates = ElectionCandidate::all();

        foreach ($candidates as $candidate) {
            $updates = [];

            // Fix platform
            if (!is_null($candidate->platform)) {
                if (!is_array($candidate->platform)) {
                    // If it's a string, convert it to an array with one item
                    // Split by newlines if present, otherwise use the whole string
                    $platform = trim($candidate->platform);
                    $updates['platform'] = strpos($platform, "\n") !== false 
                        ? array_filter(explode("\n", $platform))
                        : [$platform];
                }
            } else {
                $updates['platform'] = [];
            }

            // Fix qualifications
            if (!is_null($candidate->qualifications)) {
                if (!is_array($candidate->qualifications)) {
                    // If it's a string, convert it to an array with one item
                    // Split by newlines if present, otherwise use the whole string
                    $qualifications = trim($candidate->qualifications);
                    $updates['qualifications'] = strpos($qualifications, "\n") !== false 
                        ? array_filter(explode("\n", $qualifications))
                        : [$qualifications];
                }
            } else {
                $updates['qualifications'] = [];
            }

            // Update the candidate if we have changes
            if (!empty($updates)) {
                $candidate->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get all candidates
        $candidates = ElectionCandidate::all();

        foreach ($candidates as $candidate) {
            $updates = [];

            // Convert platform array back to string
            if (is_array($candidate->platform)) {
                $updates['platform'] = implode("\n", $candidate->platform);
            }

            // Convert qualifications array back to string
            if (is_array($candidate->qualifications)) {
                $updates['qualifications'] = implode("\n", $candidate->qualifications);
            }

            // Update the candidate if we have changes
            if (!empty($updates)) {
                $candidate->update($updates);
            }
        }
    }
} 