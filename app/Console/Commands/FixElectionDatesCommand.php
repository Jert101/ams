<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElectionSetting;

class FixElectionDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:fix-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set default dates for the election to avoid null values';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        $this->info('Current election status: ' . $electionSetting->status);
        $this->info('Is enabled: ' . ($electionSetting->is_enabled ? 'Yes' : 'No'));
        
        // Set default dates if they are null
        $now = now();
        $hasChanges = false;
        
        if ($electionSetting->candidacy_start_date === null) {
            $electionSetting->candidacy_start_date = $now->copy()->subDay();
            $hasChanges = true;
            $this->info('Setting candidacy start date to: ' . $electionSetting->candidacy_start_date->format('Y-m-d H:i:s'));
        }
        
        if ($electionSetting->candidacy_end_date === null) {
            $electionSetting->candidacy_end_date = $now->copy();
            $hasChanges = true;
            $this->info('Setting candidacy end date to: ' . $electionSetting->candidacy_end_date->format('Y-m-d H:i:s'));
        }
        
        if ($electionSetting->voting_start_date === null) {
            $electionSetting->voting_start_date = $now->copy();
            $hasChanges = true;
            $this->info('Setting voting start date to: ' . $electionSetting->voting_start_date->format('Y-m-d H:i:s'));
        }
        
        if ($electionSetting->voting_end_date === null) {
            $electionSetting->voting_end_date = $now->copy()->addDays(7);
            $hasChanges = true;
            $this->info('Setting voting end date to: ' . $electionSetting->voting_end_date->format('Y-m-d H:i:s'));
        }
        
        if ($hasChanges) {
            $electionSetting->save();
            $this->info('Election dates have been updated successfully!');
        } else {
            $this->info('No changes needed. All dates are already set.');
        }
        
        return 0;
    }
} 