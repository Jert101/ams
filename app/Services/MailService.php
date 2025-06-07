<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    protected function setupMailer()
    {
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = config('mail.mailers.smtp.host');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = config('mail.mailers.smtp.username');
        $this->mailer->Password = config('mail.mailers.smtp.password');
        $this->mailer->SMTPSecure = config('mail.mailers.smtp.encryption');
        $this->mailer->Port = config('mail.mailers.smtp.port');
        $this->mailer->setFrom(config('mail.from.address'), config('mail.from.name'));
        $this->mailer->isHTML(true);
    }

    /**
     * Send attendance confirmation email to user
     *
     * @param string $toEmail
     * @param string $toName
     * @param array $data
     * @return bool
     */
    public function sendAttendanceConfirmation($toEmail, $toName, $data)
    {
        // Build email body
        $body = $this->getEmailBody($toName, $data);
        
        // Try sending with PHPMailer first
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Attendance Confirmation - ' . $data['event_name'];
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags(str_replace(['<br>', '</p>'], ["\n", "\n\n"], $body));
            
            $sent = $this->mailer->send();
            if ($sent) {
                return true;
            }
        } catch (Exception $e) {
            Log::warning('PHPMailer failed to send email: ' . $e->getMessage());
            // Continue to fallback method
        }
        
        // Fallback to Laravel's Mail facade
        try {
            Mail::send([], [], function ($message) use ($toEmail, $toName, $data, $body) {
                $message->to($toEmail, $toName)
                    ->subject('Attendance Confirmation - ' . $data['event_name'])
                    ->html($body);
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to send attendance email: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get the HTML body for the attendance confirmation email
     * 
     * @param string $toName
     * @param array $data
     * @return string
     */
    protected function getEmailBody($toName, $data)
    {
        $body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">';
        $body .= '<h2 style="color: #8B0000;">Attendance Confirmation</h2>';
        $body .= '<p>Hello ' . $toName . ',</p>';
        
        // If verification status is present, this is a verification email
        if (isset($data['verification_status'])) {
            $verificationStatus = $data['verification_status'] === 'approve' ? 'approved' : 'rejected';
            $statusColor = $verificationStatus === 'approved' ? 'green' : 'red';
            
            $body .= '<p>Your attendance submission has been <strong style="color: ' . $statusColor . ';">' . $verificationStatus . '</strong> by an officer.</p>';
            $body .= '<div style="background-color: #f5f5f5; padding: 15px; border-left: 4px solid #8B0000; margin: 15px 0;">';
            $body .= '<p><strong>Event:</strong> ' . $data['event_name'] . '</p>';
            $body .= '<p><strong>Date:</strong> ' . $data['event_date'] . '</p>';
            $body .= '<p><strong>Time:</strong> ' . $data['event_time'] . '</p>';
            $body .= '<p><strong>Location:</strong> ' . $data['event_location'] . '</p>';
            $body .= '<p><strong>Status:</strong> ' . ucfirst($data['attendance_status']) . '</p>';
            $body .= '<p><strong>Verification:</strong> ' . ucfirst($verificationStatus) . '</p>';
            
            if (!empty($data['remarks'])) {
                $body .= '<p><strong>Remarks:</strong> ' . $data['remarks'] . '</p>';
            }
            
            $body .= '<p><strong>Processed at:</strong> ' . $data['recorded_at'] . '</p>';
            $body .= '</div>';
        } else {
            // Regular attendance confirmation email
            $body .= '<p>Your attendance has been successfully recorded for the following event:</p>';
            $body .= '<div style="background-color: #f5f5f5; padding: 15px; border-left: 4px solid #8B0000; margin: 15px 0;">';
            $body .= '<p><strong>Event:</strong> ' . $data['event_name'] . '</p>';
            $body .= '<p><strong>Date:</strong> ' . $data['event_date'] . '</p>';
            $body .= '<p><strong>Time:</strong> ' . $data['event_time'] . '</p>';
            $body .= '<p><strong>Location:</strong> ' . $data['event_location'] . '</p>';
            $body .= '<p><strong>Status:</strong> ' . ucfirst($data['attendance_status']) . '</p>';
            $body .= '<p><strong>Recorded at:</strong> ' . $data['recorded_at'] . '</p>';
            $body .= '</div>';
        }
        
        $body .= '<p>Thank you for your participation.</p>';
        $body .= '<p>Regards,<br>Knights of the Altar Council</p>';
        $body .= '</div>';
        
        return $body;
    }
}
