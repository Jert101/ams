<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionPosition extends Model
{
    protected $fillable = [
        'title',
        'description',
        'eligible_roles',
        'max_votes_per_voter',
        'election_settings_id',
    ];

    protected $casts = [
        'eligible_roles' => 'array',
        'max_votes_per_voter' => 'integer',
    ];

    /**
     * Get the election settings that this position belongs to.
     */
    public function electionSetting(): BelongsTo
    {
        return $this->belongsTo(ElectionSetting::class, 'election_settings_id');
    }

    /**
     * Get the candidates for this position.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(ElectionCandidate::class, 'position_id');
    }

    /**
     * Get the votes for this position.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'position_id');
    }

    /**
     * Check if a user is eligible to apply for this position.
     */
    public function isUserEligible(User $user): bool
    {
        if (!$user->role) {
            return false;
        }

        // Proper role-based eligibility check
        if (empty($this->eligible_roles)) {
            // If no roles are specified, no one is eligible
            return false;
        }
        
        // Check if user's role is in the eligible roles list
        return in_array($user->role->name, $this->eligible_roles);
        
        // Special handling for specific positions can be added here if needed
        // For example, for Adviser position, check if user was previously a Leader or Coordinator
    }

    /**
     * Check if a user has already voted for this position.
     */
    public function hasUserVoted(User $user): bool
    {
        return ElectionVote::where('user_id', $user->id)
            ->where('position_id', $this->id)
            ->exists();
    }

    /**
     * Get the number of votes a user has cast for this position.
     */
    public function userVoteCount(User $user): int
    {
        return ElectionVote::where('user_id', $user->id)
            ->where('position_id', $this->id)
            ->count();
    }

    /**
     * Check if a user can vote for more candidates in this position.
     */
    public function canUserVoteMore(User $user): bool
    {
        return $this->userVoteCount($user) < $this->max_votes_per_voter;
    }

    /**
     * Get the winner(s) for this position.
     */
    public function getWinners()
    {
        $candidates = $this->candidates()->withCount('votes')->orderByDesc('votes_count')->get();
        
        if ($candidates->isEmpty()) {
            return collect();
        }
        
        $highestVotes = $candidates->first()->votes_count;
        
        return $candidates->filter(function ($candidate) use ($highestVotes) {
            return $candidate->votes_count === $highestVotes;
        });
    }
} 