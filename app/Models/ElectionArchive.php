<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ElectionArchive extends Model
{
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'results',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'results' => 'array',
    ];

    /**
     * Create an archive record from the current election.
     */
    public static function createFromCurrentElection(ElectionSetting $electionSetting)
    {
        $results = [];
        $positions = ElectionPosition::with(['candidates.user', 'candidates.votes'])
            ->where('election_settings_id', $electionSetting->id)
            ->get();

        foreach ($positions as $position) {
            $candidates = [];
            foreach ($position->candidates as $candidate) {
                $candidates[] = [
                    'user_id' => $candidate->user_id,
                    'user_name' => $candidate->user->name,
                    'votes_count' => $candidate->votes->count(),
                    'is_winner' => in_array($candidate->id, $position->getWinners()->pluck('id')->toArray()),
                    'platform' => $candidate->platform,
                    'qualifications' => $candidate->qualifications,
                ];
            }

            $results[] = [
                'position_id' => $position->id,
                'position_title' => $position->title,
                'position_description' => $position->description,
                'candidates' => $candidates,
            ];
        }

        return self::create([
            'title' => 'Election ' . date('Y-m-d'),
            'start_date' => $electionSetting->candidacy_start_date,
            'end_date' => $electionSetting->voting_end_date,
            'results' => $results,
        ]);
    }

    /**
     * Assign roles to winners based on archive results.
     */
    public function assignRolesToWinners()
    {
        foreach ($this->results as $positionResult) {
            foreach ($positionResult['candidates'] as $candidate) {
                if ($candidate['is_winner']) {
                    $user = User::find($candidate['user_id']);
                    
                    if ($user) {
                        // Find or create the role based on position title
                        $roleName = $positionResult['position_title'];
                        $role = Role::firstOrCreate(
                            ['name' => $roleName],
                            ['description' => 'Elected ' . $roleName]
                        );
                        
                        // Update user's role
                        $user->role_id = $role->id;
                        $user->save();
                    }
                }
            }
        }

        return true;
    }
} 