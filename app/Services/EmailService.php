<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    /**
     * Instance of PHPMailer
     * 
     * @var PHPMailer
     */
    protected $mail;

    /**
     * Create a new EmailService instance.
     * 
     * @return void
     */
    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        // Server settings
        $this->mail->isSMTP();
        $this->mail->Host = config('mail.mailers.smtp.host');
        $this->mail->SMTPAuth = true;
        $this->mail->Username = config('mail.mailers.smtp.username');
        $this->mail->Password = config('mail.mailers.smtp.password');
        $this->mail->SMTPSecure = config('mail.mailers.smtp.encryption');
        $this->mail->Port = config('mail.mailers.smtp.port');
        
        // Set default sender
        $this->mail->setFrom(
            config('mail.from.address'),
            config('mail.from.name')
        );
    }
    
    /**
     * Send an email to a user.
     * 
     * @param string $to Recipient email address
     * @param string $name Recipient name
     * @param string $subject Email subject
     * @param string $body Email body (HTML)
     * @param string $altBody Plain text alternative (optional)
     * @return bool
     */
    public function sendEmail($to, $name, $subject, $body, $altBody = '')
    {
        try {
            // Reset all recipients
            $this->mail->clearAllRecipients();
            
            // Recipients
            $this->mail->addAddress($to, $name);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            $this->mail->AltBody = $altBody ?: strip_tags($body);
            
            // Send the email
            $this->mail->send();
            
            return true;
        } catch (Exception $e) {
            \Log::error("Email sending failed: {$e->getMessage()}");
            return false;
        }
    }
    
    /**
     * Send an absence notification email to a user.
     * 
     * @param object $user The user to notify
     * @param int $consecutiveAbsences Number of consecutive absences
     * @param string $message Custom message to include
     * @return bool
     */
    public function sendAbsenceNotification($user, $consecutiveAbsences, $message)
    {
        $subject = "Important Notice: {$consecutiveAbsences} Consecutive Absences";
        
        // Create the email body
        $body = $this->getAbsenceEmailTemplate($user, $consecutiveAbsences, $message);
        
        // Send the email
        return $this->sendEmail(
            $user->email,
            $user->name,
            $subject,
            $body
        );
    }
    
    /**
     * Get the HTML email template for absence notifications.
     * 
     * @param object $user The user to notify
     * @param int $consecutiveAbsences Number of consecutive absences
     * @param string $message Custom message to include
     * @return string
     */
    private function getAbsenceEmailTemplate($user, $consecutiveAbsences, $message)
    {
        $actionText = $consecutiveAbsences == 3 
            ? 'you need to undergo counseling' 
            : 'this is a serious matter that requires your immediate attention';
        
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #ddd;
                    border-radius: 5px;
                }
                .header {
                    background-color: #f5f5f5;
                    padding: 15px;
                    text-align: center;
                    border-bottom: 1px solid #ddd;
                }
                .content {
                    padding: 20px;
                }
                .footer {
                    font-size: 12px;
                    text-align: center;
                    margin-top: 20px;
                    color: #777;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Attendance Notification</h2>
                </div>
                <div class="content">
                    <p>Dear {$user->name},</p>
                    
                    <p>Our records indicate that you have been absent for <strong>{$consecutiveAbsences} consecutive Sundays</strong>.</p>
                    
                    <p>{$message}</p>
                    
                    <p>According to our organization's policies, {$actionText}.</p>
                    
                    <p>Please contact the secretary or an officer as soon as possible to discuss this matter.</p>
                    
                    <p>Best regards,<br>
                    The Organization Secretary</p>
                </div>
                <div class="footer">
                    <p>This is an automated message. Please do not reply directly to this email.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }
} 