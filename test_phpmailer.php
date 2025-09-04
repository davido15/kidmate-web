<?php
// Include PHPMailer
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

echo "<h2>PHPMailer SMTP Email Test</h2>";

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'schoolapp@outrankconsult.com';
    $mail->Password   = 'Gq]PxrqB#sC2';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Enable verbose debug output
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;

    // Recipients
    $mail->setFrom('schoolapp@outrankconsult.com', 'KidMate');
    $mail->addAddress('daviddors12@gmail.com', 'David');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'KidMate PHPMailer Test - ' . date('Y-m-d H:i:s');
    $mail->Body    = '<h2>KidMate Email Test</h2>
                      <p>Hello David,</p>
                      <p>This is a test email sent using <strong>PHPMailer</strong> via Hostinger SMTP.</p>
                      <p>If you receive this email, it means the email system is working correctly!</p>
                      <ul>
                          <li>SMTP Host: smtp.hostinger.com</li>
                          <li>Port: 587</li>
                          <li>Encryption: STARTTLS</li>
                          <li>From: schoolapp@outrankconsult.com</li>
                      </ul>
                      <p>Best regards,<br>KidMate Team</p>';
    
    $mail->AltBody = 'KidMate Email Test - This is a test email sent using PHPMailer via Hostinger SMTP. If you receive this email, the system is working correctly!';

    // Send the email
    $mail->send();
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Email sent successfully!</h3>";
    echo "<p><strong>To:</strong> daviddors12@gmail.com</p>";
    echo "<p><strong>Subject:</strong> " . $mail->Subject . "</p>";
    echo "<p><strong>Via:</strong> Hostinger SMTP (smtp.hostinger.com)</p>";
    echo "<p style='color: #721c24; background: #f8d7da; padding: 10px; border-radius: 3px;'>";
    echo "<strong>Check your email now!</strong> The email should appear in your Gmail inbox within 1-2 minutes.";
    echo "</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #721c24;'>❌ Email could not be sent</h3>";
    echo "<p><strong>Error:</strong> {$mail->ErrorInfo}</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?> 