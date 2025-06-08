<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupRejectedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:cleanup-rejected';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all users with rejected approval status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = User::where('approval_status', 'rejected')->count();
        
        if ($count > 0) {
            $this->info("Found {$count} rejected users to delete.");
            
            $deleted = User::where('approval_status', 'rejected')->delete();
            
            $this->info("Successfully deleted {$deleted} rejected users.");
        } else {
            $this->info("No rejected users found in the database.");
        }
        
        return Command::SUCCESS;
    }
} 