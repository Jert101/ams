<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApproved extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // Constructor is empty as we don't need to pass any data
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
            ->subject('KofA Attendance Monitoring System - Account Approved')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account registration has been approved by an administrator.')
            ->line('You can now log in to the KofA Attendance Monitoring System using your email and password.')
            ->action('Login Now', url('/login'))
            ->line('Thank you for joining the Knights of the Altar community!')
            ->salutation('God bless you!');
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
