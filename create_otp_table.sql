-- Create OTP table for journey verification
CREATE TABLE `otp_codes` (
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

-- Add index for better performance
CREATE INDEX idx_otp_lookup ON otp_codes(pickup_id, otp_code, is_used, expires_at); 