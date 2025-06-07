<?php

namespace App\Services;

use App\Mail\ElectionWinnerNotification;
use App\Models\ElectionCandidate;
use App\Models\ElectionPosition;
use App\Models\ElectionSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ElectionService
{
    /**
     * Send email notifications to all winners of the election
     *
     * @param ElectionSetting $election
     * @return array Information about sent notifications
     */
    public function sendWinnerNotifications(ElectionSetting $election)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        if (!$election->isCompleted()) {
            $results['errors'][] = 'Cannot send winner notifications because election is not completed';
            return $results;
        }

        // Get all positions in this election
        $positions = $election->positions;

        foreach ($positions as $position) {
            // Get the winners for this position
            $winners = $position->getWinners();

            foreach ($winners as $winner) {
                // Skip if the candidate doesn't have a user
                if (!$winner->user) {
                    $results['failed']++;
                    $results['errors'][] = "Winner for position {$position->title} has no associated user";
                    continue;
                }

                // Skip if the user doesn't have an email
                if (!$winner->user->email) {
                    $results['failed']++;
                    $results['errors'][] = "Winner {$winner->user->name} has no email address";
                    continue;
                }

                try {
                    // Get vote count
                    $voteCount = $winner->votes()->count();

                    // Send the email
                    Mail::to($winner->user->email)
                        ->send(new ElectionWinnerNotification($winner, $position, $voteCount));

                    $results['success']++;
                    Log::info("Sent winner notification to {$winner->user->name} for position {$position->title}");
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Failed to send email to {$winner->user->name}: {$e->getMessage()}";
                    Log::error("Failed to send winner notification: {$e->getMessage()}", [
                        'winner' => $winner->user->name,
                        'position' => $position->title,
                        'exception' => $e
                    ]);
                }
            }
        }

        return $results;
    }


} 