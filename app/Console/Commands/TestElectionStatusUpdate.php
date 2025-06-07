<?php

namespace App\Console\Commands;

use App\Models\ElectionSetting;
use Illuminate\Console\Command;

class TestElectionStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:test-status {status=candidacy : The status to set (inactive, candidacy, voting, completed)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the real-time election status updates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->argument('status');
        
        if (!in_array($status, ['inactive', 'candidacy', 'voting', 'completed'])) {
            $this->error('Invalid status. Must be one of: inactive, candidacy, voting, completed');
            return 1;
        }
        
        $electionSetting = ElectionSetting::getActiveOrCreate();
        $previousStatus = $electionSetting->status;
        
        $electionSetting->status = $status;
        $electionSetting->save();
        
        $this->info("Election status changed from {$previousStatus} to {$status}");
        $this->info("Event should have been broadcast to the election-status channel");
        
        return 0;
    }
} 