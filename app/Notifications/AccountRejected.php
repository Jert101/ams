<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRejected extends Notification
{
    use Queueable;

    /**
     * The rejection reason.
     *
     * @var string
     */
    protected $rejectionReason;

    /**
     * Create a new notification instance.
     *
     * @param string $rejectionReason
     */
    public function __construct(string $rejectionReason)
    {
        $this->rejectionReason = $rejectionReason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('KofA Attendance Monitoring System - Account Registration Status')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We regret to inform you that your account registration for the KofA Attendance Monitoring System has been rejected.')
            ->line('Reason for rejection: ' . $this->rejectionReason)
            ->line('If you believe this is a mistake or would like to provide additional information, please contact the administrator.')
            ->action('Register Again', url('/register'))
            ->line('Thank you for your understanding.')
            ->salutation('Regards, KofA Admin Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
