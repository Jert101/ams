<?php

namespace App\Mail;

use App\Models\ElectionCandidate;
use App\Models\ElectionPosition;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ElectionWinnerNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The winner candidate
     */
    public $candidate;

    /**
     * The position
     */
    public $position;

    /**
     * The vote count
     */
    public $voteCount;

    /**
     * Create a new message instance.
     */
    public function __construct(ElectionCandidate $candidate, ElectionPosition $position, int $voteCount)
    {
        $this->candidate = $candidate;
        $this->position = $position;
        $this->voteCount = $voteCount;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Congratulations! You Have Been Elected as ' . $this->position->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.election-winner',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
