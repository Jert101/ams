<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserApprovalStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:approve-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set all users approval_status to approved';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating all users to approved status...');
        
        // Get total count of users
        $totalUsers = User::count();
        $this->info("Found {$totalUsers} total users");
        
        // Update all users to approved status regardless of current status
        $updated = User::query()
            ->update(['approval_status' => 'approved']);
        
        $this->info("Successfully updated {$updated} users to approved status");
        
        return Command::SUCCESS;
    }
}
