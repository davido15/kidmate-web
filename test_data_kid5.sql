-- Test data for kid_id 5

-- 1. Insert pickup person for kid_id 5
INSERT INTO pickup_persons (name, image, pickup_id, kid_id, uuid) VALUES
('Test Pickup Person', 'uploads/pickup/test-person.jpg', 'TEST001', 5, 'test-uuid-001');

-- 2. Insert pickup journey for kid_id 5
INSERT INTO pickup_journey (pickup_id, parent_id, child_id, pickup_person_id, status, timestamp) VALUES
('TESTJOURNEY001', 'test@email.com', 5, 'test-uuid-001', 'pending', NOW()),
('TESTJOURNEY001', 'test@email.com', 5, 'test-uuid-001', 'picked', DATE_ADD(NOW(), INTERVAL 5 MINUTE)),
('TESTJOURNEY001', 'test@email.com', 5, 'test-uuid-001', 'departed', DATE_ADD(NOW(), INTERVAL 10 MINUTE));

-- 3. Insert OTP for the journey
INSERT INTO otp_codes (pickup_id, otp_code, email, is_used, expires_at, created_at) VALUES
('TESTJOURNEY001', '123456', 'test@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW());

-- Test URL: verify.php?pickup_id=TESTJOURNEY001
-- Test OTP: 123456 