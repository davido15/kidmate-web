-- Test data for pickup arrival system with provided coordinates
-- Coordinates: 5.60951, -0.250754 (Ghana)

-- Update existing journey with the provided coordinates
UPDATE pickup_journey 
SET dropoff_latitude = 5.60951, 
    dropoff_longitude = -0.250754,
    dropoff_location = 'Test Location - Ghana Coordinates'
WHERE pickup_id = 'JOURNEY001';

-- Create a new test journey with the provided coordinates
INSERT INTO pickup_journey (pickup_id, parent_id, child_id, pickup_person_id, status, timestamp, dropoff_location, dropoff_latitude, dropoff_longitude) VALUES
('TEST_GHANA_001', 'test@email.com', 1, 'test-uuid-001', 'picked', NOW(), 'Ghana Test Location', 5.60951, -0.250754);

-- Insert pickup person for testing
INSERT INTO pickup_persons (name, image, pickup_id, kid_id, uuid) VALUES
('Test Pickup Person Ghana', 'uploads/pickup/test-person.jpg', 'TEST_GHANA_001', 1, 'test-uuid-001')
ON DUPLICATE KEY UPDATE name = 'Test Pickup Person Ghana';

-- Insert OTP for the test journey
INSERT INTO otp_codes (pickup_id, otp_code, email, is_used, expires_at, created_at) VALUES
('TEST_GHANA_001', '123456', 'test@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW())
ON DUPLICATE KEY UPDATE otp_code = '123456';

-- Test URLs:
-- pickup_arrival.php?pickup_id=TEST_GHANA_001
-- verify.php?pickup_id=TEST_GHANA_001 