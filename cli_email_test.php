<?php
// Command line email test
echo "=== KidMate Email Test ===\n";

// Test email details
$to = 'daviddors12@gmail.com';
$subject = 'KidMate CLI Test - ' . date('Y-m-d H:i:s');
$message = 'This is a test email from KidMate PHP CLI application.';
$headers = 'From: KidMate <noreply@kidmate.com>' . "\r\n";

echo "Testing email to: $to\n";
echo "Subject: $subject\n";

// Try to send email
$result = mail($to, $subject, $message, $headers);

echo "\n--- Result ---\n";
if ($result) {
    echo "✅ Email sent successfully!\n";
    echo "Please check your email at: $to\n";
    echo "Also check your spam folder if you don't see it in your inbox.\n";
} else {
    echo "❌ Email failed to send!\n";
    echo "Possible issues:\n";
    echo "- PHP mail() function not configured\n";
    echo "- No email server running (SMTP/sendmail)\n";
    echo "- Firewall blocking outgoing emails\n";
    echo "- Email server configuration issue\n";
}

// Show PHP configuration
echo "\n--- PHP Configuration ---\n";
echo "PHP Version: " . phpversion() . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "SMTP Port: " . ini_get('smtp_port') . "\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "mail.add_x_header: " . ini_get('mail.add_x_header') . "\n";

// Test database connection
echo "\n--- Database Test ---\n";
if (file_exists('db.php')) {
    include 'db.php';
    if (isset($conn) && $conn) {
        echo "✅ Database connection working\n";
        
        // Test OTP table
        $test_query = "SELECT COUNT(*) as count FROM otp_codes LIMIT 1";
        $result = mysqli_query($conn, $test_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "✅ OTP table exists with " . $row['count'] . " records\n";
        } else {
            echo "❌ OTP table query failed: " . mysqli_error($conn) . "\n";
        }
    } else {
        echo "❌ Database connection failed\n";
    }
} else {
    echo "❌ db.php file not found\n";
}

echo "\n--- Test completed at: " . date('Y-m-d H:i:s') . " ---\n";
?> 