<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ElectionPosition;
use App\Models\ElectionCandidate;
use App\Models\ElectionSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current active election setting
        $electionSetting = ElectionSetting::getActiveOrCreate();
        
        // Ensure auto_approve_candidates is set to true for seeding
        if (!isset($electionSetting->auto_approve_candidates)) {
            $electionSetting->auto_approve_candidates = true;
            $electionSetting->save();
        }
        
        // Make sure we have roles
        $this->createRolesIfNeeded();
        
        // Make sure we have users
        $this->createUsersIfNeeded();
        
        // Make sure we have positions
        $this->createPositionsIfNeeded($electionSetting);
        
        // Create candidates
        $this->createCandidates();
    }
    
    /**
     * Create roles if they don't exist
     */
    private function createRolesIfNeeded()
    {
        $roles = ['Admin', 'Officer', 'Secretary', 'Member'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }
    }
    
    /**
     * Create users if we don't have enough
     */
    private function createUsersIfNeeded()
    {
        $userCount = User::count();
        
        if ($userCount < 6) {
            // Get roles
            $memberRole = Role::where('name', 'Member')->first();
            $officerRole = Role::where('name', 'Officer')->first();
            
            if (!$memberRole || !$officerRole) {
                $this->command->error('Member or Officer role not found.');
                return;
            }
            
            // Create test users
            $usersToCreate = [
                [
                    'name' => 'Test User 1',
                    'email' => 'test1@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $memberRole->id,
                    'mobile_number' => '1111111111',
                    'user_id' => 120001,
                    'approval_status' => 'approved',
                ],
                [
                    'name' => 'Test User 2',
                    'email' => 'test2@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $memberRole->id,
                    'mobile_number' => '2222222222',
                    'user_id' => 120002,
                    'approval_status' => 'approved',
                ],
                [
                    'name' => 'Test User 3',
                    'email' => 'test3@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $memberRole->id,
                    'mobile_number' => '3333333333',
                    'user_id' => 120003,
                    'approval_status' => 'approved',
                ],
                [
                    'name' => 'Test User 4',
                    'email' => 'test4@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $officerRole->id,
                    'mobile_number' => '4444444444',
                    'user_id' => 120004,
                    'approval_status' => 'approved',
                ],
                [
                    'name' => 'Test User 5',
                    'email' => 'test5@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $officerRole->id,
                    'mobile_number' => '5555555555',
                    'user_id' => 120005,
                    'approval_status' => 'approved',
                ],
                [
                    'name' => 'Test User 6',
                    'email' => 'test6@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $memberRole->id,
                    'mobile_number' => '6666666666',
                    'user_id' => 120006,
                    'approval_status' => 'approved',
                ],
            ];
            
            foreach ($usersToCreate as $userData) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    $userData
                );
            }
        }
    }
    
    /**
     * Create positions if needed
     */
    private function createPositionsIfNeeded($electionSetting)
    {
        // Create positions if none exist
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
        
        foreach ($positions as $position) {
            ElectionPosition::updateOrCreate(
                ['title' => $position['title'], 'election_settings_id' => $electionSetting->id],
                $position
            );
        }
    }
    
    /**
     * Create candidates
     */
    private function createCandidates()
    {
        try {
            // Clear existing candidates first
            ElectionCandidate::query()->delete();
            
            // Get all positions
            $positions = ElectionPosition::all();
            
            // Get users
            $users = User::whereIn('email', [
                'test1@example.com', 'test2@example.com', 'test3@example.com',
                'test4@example.com', 'test5@example.com', 'test6@example.com'
            ])->get();
            
            if ($users->count() < 6 || $positions->count() < 3) {
                $this->command->warn('Not enough users or positions in the database.');
                $this->command->info('Found ' . $users->count() . ' users and ' . $positions->count() . ' positions.');
                return;
            }
            
            // Create 2 candidates for each position
            foreach ($positions as $index => $position) {
                if ($index >= 3) break; // Limit to 3 positions
                
                $user1 = $users->get($index * 2);
                $user2 = $users->get($index * 2 + 1);
                
                if (!$user1 || !$user2) {
                    $this->command->warn('Not enough users for position: ' . $position->title);
                    continue;
                }
                
                // Log user data for debugging
                Log::info('Creating candidate with user data:', [
                    'position' => $position->title,
                    'user1' => [
                        'id' => $user1->user_id,
                        'name' => $user1->name,
                        'email' => $user1->email
                    ],
                    'user2' => [
                        'id' => $user2->user_id,
                        'name' => $user2->name,
                        'email' => $user2->email
                    ]
                ]);
                
                // Create first candidate
                ElectionCandidate::create([
                    'user_id' => $user1->user_id,
                    'position_id' => $position->id,
                    'platform' => 'Platform for position ' . $position->title . ' - Candidate 1',
                    'qualifications' => 'Qualifications for position ' . $position->title . ' - Candidate 1',
                    'status' => 'approved',
                ]);
                
                // Create second candidate
                ElectionCandidate::create([
                    'user_id' => $user2->user_id,
                    'position_id' => $position->id,
                    'platform' => 'Platform for position ' . $position->title . ' - Candidate 2',
                    'qualifications' => 'Qualifications for position ' . $position->title . ' - Candidate 2',
                    'status' => 'approved',
                ]);
            }
            
            $this->command->info('Election candidates created successfully!');
        } catch (\Exception $e) {
            $this->command->error('Error creating candidates: ' . $e->getMessage());
            Log::error('Error in CandidateSeeder:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}