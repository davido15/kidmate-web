<?php
include 'db.php';

// Clean up expired OTPs (older than 24 hours)
$cleanup_sql = "DELETE FROM otp_codes WHERE expires_at < NOW() - INTERVAL 24 HOUR;";

if ($conn->query($cleanup_sql) === TRUE) {
    $affected_rows = $conn->affected_rows;
    echo "Successfully cleaned up $affected_rows expired OTP codes.<br>";
} else {
    echo "Error cleaning up expired OTPs: " . $conn->error . "<br>";
}

// Also clean up used OTPs older than 7 days
$cleanup_used_sql = "DELETE FROM otp_codes WHERE is_used = 1 AND created_at < NOW() - INTERVAL 7 DAY;";

if ($conn->query($cleanup_used_sql) === TRUE) {
    $affected_rows = $conn->affected_rows;
    echo "Successfully cleaned up $affected_rows used OTP codes older than 7 days.<br>";
} else {
    echo "Error cleaning up used OTPs: " . $conn->error . "<br>";
}

echo "<br><strong>OTP Cleanup Complete!</strong><br>";
echo "Database is now optimized.<br>";

// Show current OTP statistics
$stats_query = "SELECT 
    COUNT(*) as total_otps,
    SUM(CASE WHEN is_used = 0 AND expires_at > NOW() THEN 1 ELSE 0 END) as active_otps,
    SUM(CASE WHEN is_used = 1 THEN 1 ELSE 0 END) as used_otps,
    SUM(CASE WHEN is_used = 0 AND expires_at <= NOW() THEN 1 ELSE 0 END) as expired_otps
FROM otp_codes";

$stats_result = $conn->query($stats_query);
if ($stats_result && $stats_result->num_rows > 0) {
    $stats = $stats_result->fetch_assoc();
    echo "<br><strong>OTP Statistics:</strong><br>";
    echo "Total OTPs: " . $stats['total_otps'] . "<br>";
    echo "Active OTPs: " . $stats['active_otps'] . "<br>";
    echo "Used OTPs: " . $stats['used_otps'] . "<br>";
    echo "Expired OTPs: " . $stats['expired_otps'] . "<br>";
}
?> 