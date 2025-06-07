<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ElectionSetting;
use App\Models\ElectionPosition;
use App\Models\ElectionCandidate;
use App\Models\User;
use App\Models\Role;

class CreateTestCandidates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'election:create-test-candidates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test candidates for election voting';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current active election setting
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Enable election and set to voting period
        $electionSetting->is_enabled = true;
        $electionSetting->status = 'voting';
        $electionSetting->save();
        
        $this->info('Election set to voting period');
        
        // Make sure we have roles
        $roles = ['Admin', 'Officer', 'Secretary', 'Member'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
        
        $this->info('Roles created');
        
        // Create positions if needed
        $positions = [
            [
                'title' => 'KofA Leader',
                'description' => 'Lead the organization and coordinate all activities.',
                'eligible_roles' => ['Officer', 'Member'],
                'max_votes_per_voter' => 1,
                'election_settings_id' => $electionSetting->id,
            ],
            [
                'title' => 'KofA Coordinator',
                'description' => 'Coordinate various activities and assist the leader.',
                'eligible_roles' => ['Officer', 'Member'],
                'max_votes_per_voter' => 1,
                'election_settings_id' => $electionSetting->id,
            ],
            [
                'title' => 'KofA Adviser',
                'description' => 'Provide guidance and advice to the organization.',
                'eligible_roles' => ['Officer', 'Member'],
                'max_votes_per_voter' => 1,
                'election_settings_id' => $electionSetting->id,
            ],
        ];
        
        $createdPositions = [];
        
        foreach ($positions as $position) {
            $pos = ElectionPosition::firstOrCreate(
                ['title' => $position['title'], 'election_settings_id' => $electionSetting->id],
                $position
            );
            $createdPositions[] = $pos;
        }
        
        $this->info('Positions created: ' . count($createdPositions));
        
        // Get some existing users
        $users = User::take(10)->get();
        
        if ($users->isEmpty()) {
            $this->error('No users found in the database');
            return 1;
        }
        
        $this->info('Found ' . $users->count() . ' users');
        
        // Delete existing candidates
        ElectionCandidate::query()->delete();
        
        // Create candidates
        $candidatesCreated = 0;
        
        foreach ($createdPositions as $index => $position) {
            // Try to get two different users for each position
            $userIndex1 = ($index * 2) % $users->count();
            $userIndex2 = ($index * 2 + 1) % $users->count();
            
            // Create first candidate
            ElectionCandidate::create([
                'user_id' => $users[$userIndex1]->user_id,
                'position_id' => $position->id,
                'platform' => 'Platform for ' . $position->title . ' - Candidate 1',
                'qualifications' => 'Qualifications for ' . $position->title . ' - Candidate 1',
                'status' => 'approved',
            ]);
            $candidatesCreated++;
            
            // Create second candidate if we have enough users
            if ($userIndex1 != $userIndex2) {
                ElectionCandidate::create([
                    'user_id' => $users[$userIndex2]->user_id,
                    'position_id' => $position->id,
                    'platform' => 'Platform for ' . $position->title . ' - Candidate 2',
                    'qualifications' => 'Qualifications for ' . $position->title . ' - Candidate 2',
                    'status' => 'approved',
                ]);
                $candidatesCreated++;
            }
        }
        
        $this->info('Created ' . $candidatesCreated . ' approved candidates for testing');
        
        return 0;
    }
} 