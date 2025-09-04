<?php
// Test real SMTP email delivery
echo "<h2>Testing Real SMTP Email Delivery</h2>";

// SMTP Configuration
$smtp_host = 'smtp.hostinger.com';
$smtp_port = 587;
$smtp_username = 'schoolapp@outrankconsult.com';
$smtp_password = 'Gq]PxrqB#sC2';
$smtp_encryption = 'tls';

// Email details
$to = 'daviddors12@gmail.com';
$subject = 'KidMate SMTP Test - ' . date('Y-m-d H:i:s');
$message = 'This is a test email sent via Hostinger SMTP to verify email delivery is working.';

// Create proper headers for SMTP
$headers = array();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-type: text/html; charset=UTF-8";
$headers[] = "From: KidMate <schoolapp@outrankconsult.com>";
$headers[] = "Reply-To: schoolapp@outrankconsult.com";
$headers[] = "X-Mailer: PHP/" . phpversion();

echo "<p><strong>Testing SMTP configuration:</strong></p>";
echo "<ul>";
echo "<li>Host: $smtp_host</li>";
echo "<li>Port: $smtp_port</li>";
echo "<li>Username: $smtp_username</li>";
echo "<li>Encryption: $smtp_encryption</li>";
echo "<li>To: $to</li>";
echo "<li>Subject: $subject</li>";
echo "</ul>";

// Try using SwiftMailer approach or cURL
function sendSMTPEmail($host, $port, $username, $password, $to, $subject, $message) {
    // Create a socket connection to SMTP server
    $socket = fsockopen($host, $port, $errno, $errstr, 30);
    
    if (!$socket) {
        return "Connection failed: $errstr ($errno)";
    }
    
    // Read server response
    $response = fgets($socket, 1024);
    echo "<p>Server greeting: " . htmlspecialchars($response) . "</p>";
    
    // Send EHLO command
    fputs($socket, "EHLO localhost\r\n");
    $response = fgets($socket, 1024);
    echo "<p>EHLO response: " . htmlspecialchars($response) . "</p>";
    
    // Start TLS
    fputs($socket, "STARTTLS\r\n");
    $response = fgets($socket, 1024);
    echo "<p>STARTTLS response: " . htmlspecialchars($response) . "</p>";
    
    fclose($socket);
    return "SMTP connection test completed";
}

// Test SMTP connection
echo "<h3>SMTP Connection Test:</h3>";
$smtp_test = sendSMTPEmail($smtp_host, $smtp_port, $smtp_username, $smtp_password, $to, $subject, $message);
echo "<p>$smtp_test</p>";

// Try using mail() with proper configuration
echo "<h3>PHP mail() Test:</h3>";
$mail_result = mail($to, $subject, $message, implode("\r\n", $headers));

if ($mail_result) {
    echo "<p style='color: green;'>✅ mail() function returned success</p>";
    echo "<p>However, this doesn't guarantee delivery. Check your email.</p>";
} else {
    echo "<p style='color: red;'>❌ mail() function failed</p>";
}

// Check if we can use cURL to send via external service
echo "<h3>Alternative: cURL Email Test</h3>";
if (function_exists('curl_init')) {
    echo "<p>✅ cURL is available - we can use external email services</p>";
    
    // Simple email via a webhook or API service
    $curl_data = array(
        'to' => $to,
        'subject' => $subject,
        'message' => $message,
        'from' => 'schoolapp@outrankconsult.com'
    );
    
    echo "<p>cURL data prepared: " . json_encode($curl_data) . "</p>";
} else {
    echo "<p style='color: red;'>❌ cURL is not available</p>";
}

echo "<hr>";
echo "<p><strong>Recommendation:</strong> Use PHPMailer library for reliable SMTP email delivery.</p>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?> 