<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ElectionCandidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position_id',
        'status',
        'platform',
        'qualifications',
        'rejection_reason'
    ];

    protected $casts = [
        'platform' => 'array',
        'qualifications' => 'array'
    ];

    /**
     * The "booted" method of the model.
     * 
     * Applications are now set to pending by default and require admin approval,
     * unless auto-approval is enabled in election settings.
     */
    protected static function booted()
    {
        // Set candidates to pending or approved status when created based on settings
        static::creating(function ($candidate) {
            try {
                $electionSetting = ElectionSetting::getActiveOrCreate();
                
                // Explicitly check if auto_approve_candidates is true
                // Default to pending if the attribute doesn't exist or is not true
                if (isset($electionSetting->auto_approve_candidates) && $electionSetting->auto_approve_candidates === true) {
                    $candidate->status = 'approved';
                } else {
                    $candidate->status = 'pending';
                }
                
                // Log the decision for debugging purposes
                \Log::info('Candidate application status set', [
                    'status' => $candidate->status,
                    'auto_approve_setting' => $electionSetting->auto_approve_candidates ?? 'not set',
                    'user_id' => $candidate->user_id,
                    'position_id' => $candidate->position_id
                ]);
            } catch (\Exception $e) {
                // If there's any error, default to pending status for safety
                \Log::error('Error in ElectionCandidate booted method: ' . $e->getMessage());
                $candidate->status = 'pending';
            }
        });
    }

    /**
     * Get the user that is a candidate.
     */
    public function user(): BelongsTo
    {
        // Fixed relationship to match the actual database foreign key constraint
        // election_candidates.user_id references users.id (NOT users.user_id)
        return $this->belongsTo(User::class, 'user_id', 'id')
            ->withDefault([
                'name' => 'Unknown User',
                'email' => 'No Email Available'
            ]);
    }

    /**
     * Get the position that the candidate is running for.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(ElectionPosition::class, 'position_id');
    }

    /**
     * Get the votes for this candidate.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ElectionVote::class, 'candidate_id');
    }

    /**
     * Get all candidates with votes count.
     */
    public static function getResultsByPosition()
    {
        return self::with(['user', 'position'])
            ->withCount('votes')
            ->orderBy('position_id')
            ->orderByDesc('votes_count')
            ->get()
            ->groupBy('position_id');
    }

    /**
     * Get the candidate's name.
     * This is a helper accessor that safely gets the user's name or returns 'Unknown'
     */
    public function getCandidateNameAttribute()
    {
        // First try the standard relationship
        if ($this->user) {
            return $this->user->name;
        }
        
        // If that fails, try to load it
        if (!$this->relationLoaded('user')) {
            try {
                $this->load('user');
                if ($this->user) {
                    return $this->user->name;
                }
            } catch (\Exception $e) {
                // Silently ignore relationship loading errors
            }
        }
        
        // If that still fails, try a direct database query
        try {
            // Updated to query the correct column - we want to find the user by 'id', not 'user_id'
            $user = \DB::table('users')->where('id', $this->user_id)->first();
            if ($user) {
                return $user->name;
            }
        } catch (\Exception $e) {
            // Silently ignore database errors
        }
        
        // Default fallback
        return 'Unknown';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // New: Check if user has already applied in current election
    public static function hasExistingApplication($userId)
    {
        $currentElection = ElectionSetting::getActiveOrCreate();
        return self::whereHas('position', function($query) use ($currentElection) {
                $query->where('election_settings_id', $currentElection->id);
            })
            ->where('user_id', $userId)
            ->exists();
    }

    // New: Validate if position has reached maximum candidates
    public function validateMaxCandidates()
    {
        if ($this->position->max_candidates > 0) {
            $currentCandidates = self::where('position_id', $this->position_id)
                ->where('status', 'approved')
                ->count();
            return $currentCandidates < $this->position->max_candidates;
        }
        return true;
    }

    // New: Check if user meets minimum membership date requirement
    public function validateMembershipDate()
    {
        if ($this->position->minimum_member_since_date) {
            return $this->user->member_since_date <= $this->position->minimum_member_since_date;
        }
        return true;
    }
} 