<?php

namespace App\Models;

use App\Events\ElectionStatusChanged;
use App\Services\ElectionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ElectionSetting extends Model
{
    protected $fillable = [
        'is_enabled',
        'status',
        'candidacy_start_date',
        'candidacy_end_date',
        'voting_start_date',
        'voting_end_date',
        'ignore_automatic_updates',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'candidacy_start_date' => 'datetime',
        'candidacy_end_date' => 'datetime',
        'voting_start_date' => 'datetime',
        'voting_end_date' => 'datetime',
        'ignore_automatic_updates' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // Broadcast an event when the election settings are updated
        static::updated(function ($electionSetting) {
            event(new ElectionStatusChanged($electionSetting));
        });
    }

    /**
     * Get the positions for this election.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(ElectionPosition::class, 'election_settings_id');
    }

    /**
     * Check if the election is in candidacy period.
     */
    public function isCandidacyPeriod(): bool
    {
        return $this->status === 'candidacy';
    }

    /**
     * Check if the election is in voting period.
     */
    public function isVotingPeriod(): bool
    {
        return $this->status === 'voting';
    }

    /**
     * Check if the election has been completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get the current active election settings or create a default one.
     */
    public static function getActiveOrCreate()
    {
        $settings = self::orderBy('id', 'desc')->first();
        
        if (!$settings) {
            $settings = self::create([
                'is_enabled' => false,
                'status' => 'inactive',
            ]);
        }
        
        return $settings;
    }

    /**
     * Update status based on current date
     */
    public function updateStatusBasedOnDate(): bool
    {
        // Don't update if automatic updates are ignored
        if ($this->ignore_automatic_updates) {
            return false;
        }
        
        $now = now();
        $changed = false;
        $previousStatus = $this->status;
        
        // If the election system is disabled, set status to inactive
        if (!$this->is_enabled && $this->status !== 'inactive') {
            $this->status = 'inactive';
            $changed = true;
        }
        // Only check date-based status if the system is enabled
        elseif ($this->is_enabled) {
            // Check if it should be in candidacy period
            if ($this->candidacy_start_date && 
                $this->candidacy_end_date && 
                $now->between($this->candidacy_start_date, $this->candidacy_end_date) && 
                $this->status !== 'candidacy') {
                $this->status = 'candidacy';
                $changed = true;
            }
            // Check if it should be in voting period
            elseif ($this->voting_start_date && 
                $this->voting_end_date && 
                $now->between($this->voting_start_date, $this->voting_end_date) && 
                $this->status !== 'voting') {
                $this->status = 'voting';
                $changed = true;
            }
            // Check if voting period has ended and should be completed
            elseif ($this->voting_end_date && 
                $now->greaterThan($this->voting_end_date) && 
                $this->status !== 'completed') {
                $this->status = 'completed';
                $changed = true;
            }
            // Check if candidacy period hasn't started yet
            elseif ($this->candidacy_start_date && 
                $now->lessThan($this->candidacy_start_date) && 
                $this->status !== 'inactive') {
                $this->status = 'inactive';
                $changed = true;
            }
        }
        
        if ($changed) {
            $this->save();
            
            // If status changed to completed, trigger archive creation and send winner notifications
            if ($this->status === 'completed' && $previousStatus !== 'completed') {
                try {
                    \DB::transaction(function () {
                        \App\Models\ElectionArchive::createFromCurrentElection($this);
                    });
                    
                    // Send notifications to winners
                    $electionService = new ElectionService();
                    $electionService->sendWinnerNotifications($this);
                } catch (\Exception $e) {
                    \Log::error('Failed to complete election processes: ' . $e->getMessage());
                }
            }
            
            // Broadcast event (this is already handled by the model's booted method when save() is called)
        }
        
        return $changed;
    }

    /**
     * Update status for all active elections
     */
    public static function updateAllElectionStatuses(): int
    {
        $updatedCount = 0;
        $activeElections = self::where('is_enabled', true)
            ->where('ignore_automatic_updates', false)
            ->get();
        
        foreach ($activeElections as $election) {
            if ($election->updateStatusBasedOnDate()) {
                $updatedCount++;
            }
        }
        
        return $updatedCount;
    }
} 