<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SeriousAbsenceWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user receiving the notification.
     *
     * @var \App\Models\User
     */
    public $user;

    /**
     * The dates of missed Sundays.
     *
     * @var array
     */
    public $missedDates;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, array $missedDates)
    {
        $this->user = $user;
        $this->missedDates = $missedDates;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'URGENT: Serious Attendance Warning - Immediate Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.serious-absence-warning',
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