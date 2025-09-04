<?php
/**
 * Email Service for KidMate PHP Application
 * Handles all email notifications
 */

class EmailService {
    
    private $smtp_host;
    private $smtp_port;
    private $smtp_username;
    private $smtp_password;
    private $smtp_encryption;
    private $from_email;
    private $from_name;
    
    public function __construct() {
        // Load email configuration from environment or config
        $this->smtp_host = getenv('SMTP_HOST') ?: 'smtp.hostinger.com';
        $this->smtp_port = getenv('SMTP_PORT') ?: 587;
        $this->smtp_username = getenv('SMTP_USERNAME') ?: 'schoolapp@outrankconsult.com';
        $this->smtp_password = getenv('SMTP_PASSWORD') ?: 'Gq]PxrqB#sC2';
        $this->smtp_encryption = getenv('SMTP_ENCRYPTION') ?: 'tls';
        $this->from_email = getenv('FROM_EMAIL') ?: 'schoolapp@outrankconsult.com';
        $this->from_name = getenv('FROM_NAME') ?: 'KidMate';
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendEmail($to, $subject, $body, $html_body = null) {
        try {
            // Check if PHPMailer is available
            if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                // Fallback to PHP mail() function
                return $this->sendEmailFallback($to, $subject, $body, $html_body);
            }
            
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtp_host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp_username;
            $mail->Password = $this->smtp_password;
            $mail->SMTPSecure = $this->smtp_encryption;
            $mail->Port = $this->smtp_port;
            
            // Recipients
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($to);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body ?: $body;
            $mail->AltBody = $body;
            
            $mail->send();
            error_log("Email sent successfully to: " . $to);
            return true;
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Fallback email method using PHP mail() function
     */
    private function sendEmailFallback($to, $subject, $body, $html_body = null) {
        $headers = array();
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . $this->from_name . " <" . $this->from_email . ">";
        $headers[] = "Reply-To: " . $this->from_email;
        $headers[] = "X-Mailer: PHP/" . phpversion();
        
        $message = $html_body ?: $body;
        
        if (mail($to, $subject, $message, implode("\r\n", $headers))) {
            error_log("Email sent successfully to: " . $to);
            return true;
        } else {
            error_log("Email sending failed to: " . $to);
            return false;
        }
    }
    
    /**
     * Send welcome email to new users
     */
    public function sendWelcomeEmail($user_email, $user_name) {
        $subject = "Welcome to KidMate - Your Account is Ready!";
        $body = "Dear $user_name,\n\nWelcome to KidMate! Your account has been successfully created.\n\nWe're excited to help you manage your child's pickup and dropoff services.\n\nIf you have any questions, please don't hesitate to contact our support team.\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Welcome to KidMate!</h2>
            <p>Dear $user_name,</p>
            <p>Welcome to KidMate! Your account has been successfully created.</p>
            <p>We're excited to help you manage your child's pickup and dropoff services.</p>
            <p>If you have any questions, please don't hesitate to contact our support team.</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($user_email, $subject, $body, $html_body);
    }
    
    /**
     * Send pickup notification to parent
     */
    public function sendPickupNotification($parent_email, $parent_name, $child_name, $pickup_person_name, $pickup_time) {
        $subject = "Pickup Update: $child_name is being picked up";
        $body = "Dear $parent_name,\n\nYour child $child_name is being picked up by $pickup_person_name at $pickup_time.\n\nYou can track the journey in real-time through the KidMate app.\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Pickup Update</h2>
            <p>Dear $parent_name,</p>
            <p>Your child <strong>$child_name</strong> is being picked up by <strong>$pickup_person_name</strong> at $pickup_time.</p>
            <p>You can track the journey in real-time through the KidMate app.</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($parent_email, $subject, $body, $html_body);
    }
    
    /**
     * Send dropoff notification to parent
     */
    public function sendDropoffNotification($parent_email, $parent_name, $child_name, $dropoff_location, $dropoff_time) {
        $subject = "Dropoff Complete: $child_name has arrived safely";
        $body = "Dear $parent_name,\n\nYour child $child_name has been safely dropped off at $dropoff_location at $dropoff_time.\n\nThank you for using KidMate!\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Dropoff Complete</h2>
            <p>Dear $parent_name,</p>
            <p>Your child <strong>$child_name</strong> has been safely dropped off at <strong>$dropoff_location</strong> at $dropoff_time.</p>
            <p>Thank you for using KidMate!</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($parent_email, $subject, $body, $html_body);
    }
    
    /**
     * Send payment confirmation email
     */
    public function sendPaymentConfirmation($parent_email, $parent_name, $amount, $payment_id, $journey_date) {
        $subject = "Payment Confirmation - KidMate";
        $body = "Dear $parent_name,\n\nYour payment of \$$amount has been successfully processed.\n\nPayment Details:\n- Payment ID: $payment_id\n- Amount: \$$amount\n- Journey Date: $journey_date\n\nThank you for your payment!\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Payment Confirmation</h2>
            <p>Dear $parent_name,</p>
            <p>Your payment of <strong>\$$amount</strong> has been successfully processed.</p>
            <br>
            <h3>Payment Details:</h3>
            <ul>
                <li><strong>Payment ID:</strong> $payment_id</li>
                <li><strong>Amount:</strong> \$$amount</li>
                <li><strong>Journey Date:</strong> $journey_date</li>
            </ul>
            <p>Thank you for your payment!</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($parent_email, $subject, $body, $html_body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail($user_email, $reset_token) {
        $reset_url = "https://yourdomain.com/reset-password.php?token=" . $reset_token;
        $subject = "Password Reset Request - KidMate";
        $body = "Dear User,\n\nYou have requested to reset your password for your KidMate account.\n\nPlease click the following link to reset your password:\n$reset_url\n\nIf you didn't request this password reset, please ignore this email.\n\nThis link will expire in 1 hour.\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Password Reset Request</h2>
            <p>Dear User,</p>
            <p>You have requested to reset your password for your KidMate account.</p>
            <p>Please click the following link to reset your password:</p>
            <p><a href=\"$reset_url\" style=\"background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;\">Reset Password</a></p>
            <p>If you didn't request this password reset, please ignore this email.</p>
            <p><strong>This link will expire in 1 hour.</strong></p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($user_email, $subject, $body, $html_body);
    }
    
    /**
     * Send attendance notification
     */
    public function sendAttendanceNotification($parent_email, $parent_name, $child_name, $attendance_date, $status) {
        $subject = "Attendance Update: $child_name - $attendance_date";
        $body = "Dear $parent_name,\n\nAttendance update for $child_name on $attendance_date:\nStatus: $status\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>Attendance Update</h2>
            <p>Dear $parent_name,</p>
            <p>Attendance update for <strong>$child_name</strong> on $attendance_date:</p>
            <p><strong>Status:</strong> $status</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($parent_email, $subject, $body, $html_body);
    }
    
    /**
     * Send admin notification for new registrations
     */
    public function sendAdminNotification($admin_email, $user_name, $user_email, $user_phone) {
        $subject = "New User Registration - KidMate";
        $body = "A new user has registered on KidMate.\n\nUser Details:\n- Name: $user_name\n- Email: $user_email\n- Phone: $user_phone\n\nPlease review the registration.";
        
        $html_body = "
        <html>
        <body>
            <h2>New User Registration</h2>
            <p>A new user has registered on KidMate.</p>
            <br>
            <h3>User Details:</h3>
            <ul>
                <li><strong>Name:</strong> $user_name</li>
                <li><strong>Email:</strong> $user_email</li>
                <li><strong>Phone:</strong> $user_phone</li>
            </ul>
            <p>Please review the registration.</p>
        </body>
        </html>";
        
        return $this->sendEmail($admin_email, $subject, $body, $html_body);
    }
    
    /**
     * Send journey status notification for all status changes
     */
    public function sendJourneyStatusNotification($parent_email, $parent_name, $child_name, $pickup_person_name, $status, $timestamp, $additional_info = null) {
        $status_messages = [
            'scheduled' => [
                'subject' => "Journey Scheduled: $child_name",
                'title' => "Journey Scheduled",
                'message' => "Your child $child_name's pickup journey has been scheduled.",
                'details' => "Pickup Person: $pickup_person_name"
            ],
            'pending' => [
                'subject' => "Journey Started: $child_name",
                'title' => "Journey Started",
                'message' => "Your child $child_name's pickup journey has been initiated.",
                'details' => "Pickup Person: $pickup_person_name - Journey is ready to begin."
            ],
            'departed' => [
                'subject' => "Pickup Person Departed: $child_name",
                'title' => "Pickup Person Departed",
                'message' => "Your pickup person $pickup_person_name has departed to pick up $child_name.",
                'details' => "The pickup person is on their way to the pickup location."
            ],
            'picked' => [
                'subject' => "Child Picked Up: $child_name",
                'title' => "Child Picked Up",
                'message' => "Your child $child_name has been picked up by $pickup_person_name.",
                'details' => "Your child is now in transit to the destination."
            ],
            'arrived' => [
                'subject' => "Arrived at Destination: $child_name",
                'title' => "Arrived at Destination",
                'message' => "Your child $child_name has arrived at the destination with $pickup_person_name.",
                'details' => "The journey is almost complete."
            ],
            'completed' => [
                'subject' => "Journey Completed: $child_name",
                'title' => "Journey Completed",
                'message' => "Your child $child_name's journey has been completed successfully.",
                'details' => "Thank you for using KidMate!"
            ],
            'pickup_started' => [
                'subject' => "Pickup Started: $child_name",
                'title' => "Pickup Started",
                'message' => "Your child $child_name is being picked up by $pickup_person_name.",
                'details' => "You can track the journey in real-time through the KidMate app."
            ],
            'in_transit' => [
                'subject' => "In Transit: $child_name",
                'title' => "Journey In Progress",
                'message' => "Your child $child_name is currently in transit with $pickup_person_name.",
                'details' => "The journey is progressing smoothly."
            ],
            'cancelled' => [
                'subject' => "Journey Cancelled: $child_name",
                'title' => "Journey Cancelled",
                'message' => "Your child $child_name's journey has been cancelled.",
                'details' => $additional_info ?: "Please contact support if you have any questions."
            ],
            'delayed' => [
                'subject' => "Journey Delayed: $child_name",
                'title' => "Journey Delayed",
                'message' => "Your child $child_name's journey has been delayed.",
                'details' => $additional_info ?: "We apologize for the inconvenience. We'll keep you updated."
            ]
        ];
        
        $status_info = $status_messages[$status] ?? [
            'subject' => "Journey Update: $child_name",
            'title' => "Journey Update",
            'message' => "Your child $child_name's journey status has been updated to: $status",
            'details' => "Please check the app for more details."
        ];
        
        $body = "Dear $parent_name,\n\n{$status_info['message']}\n\n{$status_info['details']}\n\nTime: $timestamp\n\nBest regards,\nThe KidMate Team";
        
        $html_body = "
        <html>
        <body>
            <h2>{$status_info['title']}</h2>
            <p>Dear $parent_name,</p>
            <p>{$status_info['message']}</p>
            <p>{$status_info['details']}</p>
            <p><strong>Time:</strong> $timestamp</p>
            <br>
            <p>Best regards,<br>The KidMate Team</p>
        </body>
        </html>";
        
        return $this->sendEmail($parent_email, $status_info['subject'], $body, $html_body);
    }
}
?> 