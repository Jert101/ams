<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ElectionWinnerNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $positions;

    public function __construct($positions)
    {
        $this->positions = $positions;
    }

    public function build()
    {
        return $this->subject('KOFA Election Results Announcement')
                    ->markdown('emails.election.winner', [
                        'positions' => $this->positions
                    ]);
    }
}
