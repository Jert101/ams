<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionCandidate extends Model
{
    protected $fillable = [
        'user_id',
        'position_id',
        'platform',
        'qualifications',
        'status',
        'rejection_reason',
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
                
                // Check if the auto_approve_candidates attribute exists
                if (isset($electionSetting->auto_approve_candidates)) {
                    $candidate->status = $electionSetting->auto_approve_candidates ? 'approved' : 'pending';
                } else {
                    // Default to approved for backward compatibility
                    $candidate->status = 'approved';
                }
            } catch (\Exception $e) {
                // If there's any error, default to approved status
                \Log::error('Error in ElectionCandidate booted method: ' . $e->getMessage());
                $candidate->status = 'approved';
            }
        });
    }

    /**
     * Get the user that is a candidate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
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
            $user = \DB::table('users')->where('user_id', $this->user_id)->first();
            if ($user) {
                return $user->name;
            }
        } catch (\Exception $e) {
            // Silently ignore database errors
        }
        
        // Default fallback
        return 'Unknown';
    }
} 