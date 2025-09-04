<?php
include 'db.php';

// SQL to create OTP table
$sql = "
CREATE TABLE IF NOT EXISTS `otp_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pickup_id` varchar(36) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pickup_id` (`pickup_id`),
  KEY `idx_otp_code` (`otp_code`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_used` (`is_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";

// Execute the SQL
if ($conn->query($sql) === TRUE) {
    echo "OTP table created successfully!<br>";
} else {
    echo "Error creating OTP table: " . $conn->error . "<br>";
}

// Create additional index for better performance
$index_sql = "CREATE INDEX IF NOT EXISTS idx_otp_lookup ON otp_codes(pickup_id, otp_code, is_used, expires_at);";

if ($conn->query($index_sql) === TRUE) {
    echo "OTP table indexes created successfully!<br>";
} else {
    echo "Error creating indexes: " . $conn->error . "<br>";
}

// Clean up expired OTPs (older than 24 hours)
$cleanup_sql = "DELETE FROM otp_codes WHERE expires_at < NOW() - INTERVAL 24 HOUR;";
if ($conn->query($cleanup_sql) === TRUE) {
    echo "Expired OTPs cleaned up successfully!<br>";
} else {
    echo "Error cleaning up expired OTPs: " . $conn->error . "<br>";
}

echo "<br><strong>OTP Table Setup Complete!</strong><br>";
echo "You can now use the verify page with database-stored OTPs.<br>";
echo "<a href='verify.php'>Go to Verify Page</a>";
?> 