<?php

namespace App\Console\Commands;

use App\Events\ElectionStatusChanged;
use App\Models\ElectionSetting;
use Illuminate\Console\Command;

class ForceElectionStatusCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:force-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Force a check of election status and broadcast the current status regardless of changes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Forcing election status check and broadcast...');
        
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // First, check if there should be an automatic status update
        $updated = $electionSetting->updateStatusBasedOnDate();
        
        if ($updated) {
            $this->info('Status was automatically updated to: ' . $electionSetting->status);
        } else {
            $this->info('No status change needed. Current status: ' . $electionSetting->status);
            
            // Even if no change occurred, broadcast the current status
            event(new ElectionStatusChanged($electionSetting));
            $this->info('Broadcast event sent with current status.');
        }
        
        return 0;
    }
} 