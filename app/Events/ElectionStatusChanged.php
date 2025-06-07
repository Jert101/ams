<?php

namespace App\Events;

use App\Models\ElectionSetting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElectionStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $electionSetting;

    /**
     * Create a new event instance.
     */
    public function __construct(ElectionSetting $electionSetting)
    {
        $this->electionSetting = $electionSetting;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('election-status'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->electionSetting->id,
            'status' => $this->electionSetting->status,
            'is_enabled' => $this->electionSetting->is_enabled,
            'ignore_automatic_updates' => $this->electionSetting->ignore_automatic_updates,
            'candidacy_start_date' => $this->electionSetting->candidacy_start_date ? $this->electionSetting->candidacy_start_date->toISOString() : null,
            'candidacy_end_date' => $this->electionSetting->candidacy_end_date ? $this->electionSetting->candidacy_end_date->toISOString() : null,
            'voting_start_date' => $this->electionSetting->voting_start_date ? $this->electionSetting->voting_start_date->toISOString() : null,
            'voting_end_date' => $this->electionSetting->voting_end_date ? $this->electionSetting->voting_end_date->toISOString() : null,
            'updated_at' => $this->electionSetting->updated_at->toISOString(),
        ];
    }
} 