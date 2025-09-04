<?php
// Test email functionality
$to = 'daviddors12@gmail.com';
$subject = 'KidMate Email Test';
$message = 'This is a test email from KidMate to verify email functionality is working.';
$headers = 'From: KidMate <noreply@kidmate.com>' . "\r\n";

echo "<h2>KidMate Email Test</h2>";
echo "<p>Testing email to: $to</p>";

$result = mail($to, $subject, $message, $headers);

if ($result) {
    echo "<p style='color: green;'>✅ Email sent successfully!</p>";
    echo "<p>Check your email at: $to</p>";
} else {
    echo "<p style='color: red;'>❌ Email failed to send!</p>";
    echo "<p>This could be due to:</p>";
    echo "<ul>";
    echo "<li>PHP mail() function not configured</li>";
    echo "<li>Email server not running</li>";
    echo "<li>Firewall blocking outgoing emails</li>";
    echo "<li>Email going to spam folder</li>";
    echo "</ul>";
}

// Check PHP mail configuration
echo "<h3>PHP Mail Configuration:</h3>";
echo "<p>SMTP: " . ini_get('SMTP') . "</p>";
echo "<p>SMTP Port: " . ini_get('smtp_port') . "</p>";
echo "<p>sendmail_path: " . ini_get('sendmail_path') . "</p>";

// Test database connection
include 'db.php';
if ($conn) {
    echo "<p style='color: green;'>✅ Database connection working</p>";
} else {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
}
?> 