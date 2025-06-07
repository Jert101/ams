<?php

namespace App\Console\Commands;

use App\Models\ElectionSetting;
use Illuminate\Console\Command;

class UpdateElectionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status of all elections based on their configured date ranges';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating election statuses...');
        
        $updatedCount = ElectionSetting::updateAllElectionStatuses();
        
        if ($updatedCount > 0) {
            $this->info("Successfully updated status for {$updatedCount} election(s).");
        } else {
            $this->info('No election statuses needed to be updated.');
        }
        
        return 0;
    }
} 