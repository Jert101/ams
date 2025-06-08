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
     * Applications are now set to pending by default and require admin approval.
     */
    protected static function booted()
    {
        // Set candidates to pending status when created
        static::creating(function ($candidate) {
            $candidate->status = 'pending';
        });
    }

    /**
     * Get the user that is a candidate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
} 