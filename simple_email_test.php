<?php
// Simple email test - can be run directly
echo "<h2>KidMate Simple Email Test</h2>";

// Test email details
$to = 'daviddors12@gmail.com';
$subject = 'KidMate Test Email - ' . date('Y-m-d H:i:s');
$message = 'This is a test email from KidMate PHP application.';
$headers = 'From: KidMate <noreply@kidmate.com>' . "\r\n";

echo "<p><strong>Testing email to:</strong> $to</p>";
echo "<p><strong>Subject:</strong> $subject</p>";

// Try to send email
$result = mail($to, $subject, $message, $headers);

echo "<hr>";
if ($result) {
    echo "<p style='color: green; font-weight: bold;'>✅ Email sent successfully!</p>";
    echo "<p>Please check your email at: <strong>$to</strong></p>";
    echo "<p>Also check your spam folder if you don't see it in your inbox.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Email failed to send!</p>";
    echo "<p>Possible issues:</p>";
    echo "<ul>";
    echo "<li>PHP mail() function not configured</li>";
    echo "<li>No email server running (SMTP/sendmail)</li>";
    echo "<li>Firewall blocking outgoing emails</li>";
    echo "<li>Email server configuration issue</li>";
    echo "</ul>";
}

// Show PHP configuration
echo "<hr>";
echo "<h3>PHP Configuration:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>SMTP:</strong> " . ini_get('SMTP') . "</p>";
echo "<p><strong>SMTP Port:</strong> " . ini_get('smtp_port') . "</p>";
echo "<p><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</p>";
echo "<p><strong>mail.add_x_header:</strong> " . ini_get('mail.add_x_header') . "</p>";

// Test database connection
echo "<hr>";
echo "<h3>Database Test:</h3>";
if (file_exists('db.php')) {
    include 'db.php';
    if (isset($conn) && $conn) {
        echo "<p style='color: green;'>✅ Database connection working</p>";
        
        // Test OTP table
        $test_query = "SELECT COUNT(*) as count FROM otp_codes LIMIT 1";
        $result = mysqli_query($conn, $test_query);
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p style='color: green;'>✅ OTP table exists with " . $row['count'] . " records</p>";
        } else {
            echo "<p style='color: red;'>❌ OTP table query failed: " . mysqli_error($conn) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
    }
} else {
    echo "<p style='color: red;'>❌ db.php file not found</p>";
}

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?> 