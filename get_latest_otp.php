<?php
include 'db.php';

echo "<h2>Latest OTP for Testing</h2>";

$pickup_id = 'test-journey-1755790143';

// Get the latest OTP for this pickup ID
$query = "SELECT otp_code, email, created_at, expires_at, is_used 
          FROM otp_codes 
          WHERE pickup_id = ? 
          ORDER BY created_at DESC 
          LIMIT 1";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $pickup_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $otp_data = $result->fetch_assoc();
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Latest OTP Found</h3>";
    echo "<p><strong>Pickup ID:</strong> $pickup_id</p>";
    echo "<p><strong>OTP Code:</strong> <span style='font-size: 24px; font-weight: bold; color: #007bff;'>{$otp_data['otp_code']}</span></p>";
    echo "<p><strong>Email:</strong> {$otp_data['email']}</p>";
    echo "<p><strong>Created:</strong> {$otp_data['created_at']}</p>";
    echo "<p><strong>Expires:</strong> {$otp_data['expires_at']}</p>";
    echo "<p><strong>Used:</strong> " . ($otp_data['is_used'] ? 'Yes' : 'No') . "</p>";
    
    if (!$otp_data['is_used'] && strtotime($otp_data['expires_at']) > time()) {
        echo "<p style='color: green; font-weight: bold;'>✅ OTP is valid and can be used for testing</p>";
        echo "<p><strong>Test URL:</strong> <a href='http://localhost:8888/KidMate/verify.php?pickup_id=$pickup_id' target='_blank'>Verify Page</a></p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ OTP is expired or already used</p>";
    }
    echo "</div>";
    
} else {
    echo "<p style='color: red;'>No OTP found for pickup ID: $pickup_id</p>";
    echo "<p>You need to send an OTP first by visiting the verify page.</p>";
}

echo "<hr>";
echo "<p><strong>Test completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
?> 