<?php
include 'db.php';
include 'email_service.php';

echo "<h2>Testing OTP with SMTP Email Service</h2>";

// Test pickup ID
$pickup_id = 'test-journey-1755790143';

// Simple query to get journey data
$query = "SELECT pj.*, k.name as child_name, u.name as parent_name 
          FROM pickup_journey pj 
          LEFT JOIN kids k ON pj.child_id = k.id 
          LEFT JOIN users u ON pj.parent_id = u.id 
          WHERE pj.pickup_id = '$pickup_id'";

$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $journey_data = mysqli_fetch_assoc($result);
    echo "<p>✅ Journey found: " . $journey_data['child_name'] . "</p>";
    
    // Generate OTP
    $otp = sprintf("%06d", rand(0, 999999));
    $admin_email = 'daviddors12@gmail.com';
    
    // Store OTP in database
    $otp_query = "INSERT INTO otp_codes (pickup_id, otp_code, email, expires_at) VALUES ('$pickup_id', '$otp', '$admin_email', '" . date('Y-m-d H:i:s', strtotime('+10 minutes')) . "')";
    
    if (mysqli_query($conn, $otp_query)) {
        echo "<p>✅ OTP stored in database: $otp</p>";
        
        // Send email using EmailService
        $emailService = new EmailService();
        
        // Use the public sendWelcomeEmail method to test email sending
        $email_result = $emailService->sendWelcomeEmail($admin_email, 'Admin');
        
        if ($email_result) {
            echo "<p style='color: green; font-weight: bold;'>✅ Email sent successfully via SMTP!</p>";
            echo "<p>Check your email at: <strong>$admin_email</strong></p>";
            echo "<p>OTP Code: <strong style='font-size: 20px; color: red;'>$otp</strong></p>";
            echo "<p><em>Note: This test sent a welcome email. The OTP code above is stored in the database.</em></p>";
        } else {
            echo "<p style='color: red;'>❌ Email failed to send via SMTP.</p>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ Failed to store OTP in database: " . mysqli_error($conn) . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Journey not found.</p>";
    echo "<p>Query: $query</p>";
    echo "<p>Error: " . mysqli_error($conn) . "</p>";
}
?> 