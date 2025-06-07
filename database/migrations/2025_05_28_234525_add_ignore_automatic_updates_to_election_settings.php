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
        Schema::table('election_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('election_settings', 'ignore_automatic_updates')) {
                $table->boolean('ignore_automatic_updates')->default(false)->after('voting_end_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('election_settings', function (Blueprint $table) {
            if (Schema::hasColumn('election_settings', 'ignore_automatic_updates')) {
                $table->dropColumn('ignore_automatic_updates');
            }
        });
    }
};
