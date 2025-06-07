# Automatic Absence Notification System

This document explains how to set up the automatic email notification system for consecutive Sunday absences.

## Overview

The system automatically checks for members with consecutive Sunday absences and sends different email notifications based on the number of absences:

1. **3 Consecutive Sunday Absences**: Member receives an email notification informing them that they need to undergo counseling at the next meeting.

2. **4+ Consecutive Sunday Absences**: Member receives an urgent email notification informing them that they need to undergo serious counseling on the next Sunday, and if they fail to attend, the council will visit them at their home.

## Setup Instructions

### 1. Email Configuration

Update your `.env` file with the appropriate email settings. You can use Gmail or any other SMTP provider:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="Knights of the Altar"
```

Note: If using Gmail, you will need to generate an "app password" rather than using your regular password.

### 2. Schedule Setup

The system is configured to run every Sunday at 10 PM to check for consecutive absences and send notifications. Make sure your Laravel scheduler is running:

Add the following Cron entry to your server (run `crontab -e`):

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Manual Testing

To manually test the notification system, you can use the following Artisan commands:

```bash
# Check for consecutive absences and send notifications
php artisan absences:check-consecutive

# Send a test notification for 3 consecutive absences
php artisan absences:test-notification user@example.com 3

# Send a test notification for 4+ consecutive absences
php artisan absences:test-notification user@example.com 4
```

## Understanding the Absence Rule

The system considers a member as present for a Sunday if they attend at least one of the four masses on that day. A member is only marked as absent for a Sunday if they miss ALL four masses on that day.

## Customizing Email Templates

The email templates can be customized by editing the following files:

- 3 Consecutive Absences: `resources/views/emails/absence-warning.blade.php`
- 4+ Consecutive Absences: `resources/views/emails/serious-absence-warning.blade.php`

## Troubleshooting

If emails are not being sent:

1. Check your `.env` configuration for mail settings
2. Ensure the Laravel scheduler is running
3. Check the Laravel logs (`storage/logs/laravel.log`) for any errors
4. Test the mail configuration with `php artisan tinker` and `Mail::to('your-email@example.com')->send(new App\Mail\AbsenceWarningMail(App\Models\User::first(), ['2023-01-01', '2023-01-08', '2023-01-15']))` 