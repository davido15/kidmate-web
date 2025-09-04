-- Insert dummy data for verify page testing

-- Insert dummy users (parents)
INSERT INTO users (name, email, phone, password_hash, role) VALUES
('John Smith', 'john.smith@email.com', '+233201234567', 'dummy_hash', 'Parent'),
('Sarah Johnson', 'sarah.johnson@email.com', '+233202345678', 'dummy_hash', 'Parent'),
('Michael Brown', 'michael.brown@email.com', '+233203456789', 'dummy_hash', 'Parent'),
('Emily Davis', 'emily.davis@email.com', '+233204567890', 'dummy_hash', 'Parent'),
('David Wilson', 'david.wilson@email.com', '+233205678901', 'dummy_hash', 'Parent');

-- Insert dummy kids
INSERT INTO kids (name, image, parent_id) VALUES
('Emma Smith', 'uploads/kids/emma.jpg', 1),
('Lucas Johnson', 'uploads/kids/lucas.jpg', 2),
('Sophia Brown', 'uploads/kids/sophia.jpg', 3),
('Oliver Davis', 'uploads/kids/oliver.jpg', 4),
('Ava Wilson', 'uploads/kids/ava.jpg', 5);

-- Get the actual kid IDs that were just inserted
SET @kid1_id = LAST_INSERT_ID();
SET @kid2_id = @kid1_id + 1;
SET @kid3_id = @kid1_id + 2;
SET @kid4_id = @kid1_id + 3;
SET @kid5_id = @kid1_id + 4;

-- Insert dummy pickup persons for kid_id 5
INSERT INTO pickup_persons (name, image, pickup_id, kid_id, uuid) VALUES
('Grandma Sarah', 'uploads/pickup/grandma-sarah.jpg', 'PICK006', 5, 'uuid-006'),
('Uncle Tom', 'uploads/pickup/uncle-tom.jpg', 'PICK007', 5, 'uuid-007'),
('Aunt Maria', 'uploads/pickup/aunt-maria.jpg', 'PICK008', 5, 'uuid-008'),
('Family Friend John', 'uploads/pickup/friend-john.jpg', 'PICK009', 5, 'uuid-009'),
('Neighbor Lisa', 'uploads/pickup/neighbor-lisa.jpg', 'PICK010', 5, 'uuid-010');

-- Insert dummy pickup journeys
INSERT INTO pickup_journey (pickup_id, parent_id, child_id, pickup_person_id, status, timestamp) VALUES
-- Journey 1: Complete journey
('JOURNEY001', 'john.smith@email.com', @kid1_id, 'uuid-001', 'pending', '2025-01-15 08:00:00'),
('JOURNEY001', 'john.smith@email.com', @kid1_id, 'uuid-001', 'picked', '2025-01-15 08:15:00'),
('JOURNEY001', 'john.smith@email.com', @kid1_id, 'uuid-001', 'departed', '2025-01-15 08:20:00'),
('JOURNEY001', 'john.smith@email.com', @kid1_id, 'uuid-001', 'dropoff', '2025-01-15 08:45:00'),
('JOURNEY001', 'john.smith@email.com', @kid1_id, 'uuid-001', 'completed', '2025-01-15 08:50:00'),

-- Journey 2: In progress journey
('JOURNEY002', 'sarah.johnson@email.com', @kid2_id, 'uuid-002', 'pending', '2025-01-15 09:00:00'),
('JOURNEY002', 'sarah.johnson@email.com', @kid2_id, 'uuid-002', 'picked', '2025-01-15 09:10:00'),

-- Journey 3: Just departed
('JOURNEY003', 'michael.brown@email.com', @kid3_id, 'uuid-003', 'pending', '2025-01-15 10:00:00'),
('JOURNEY003', 'michael.brown@email.com', @kid3_id, 'uuid-003', 'picked', '2025-01-15 10:05:00'),
('JOURNEY003', 'michael.brown@email.com', @kid3_id, 'uuid-003', 'departed', '2025-01-15 10:10:00'),

-- Journey 4: Pending pickup
('JOURNEY004', 'emily.davis@email.com', @kid4_id, 'uuid-004', 'pending', '2025-01-15 11:00:00'),

-- Journey 5: Dropoff in progress
('JOURNEY005', 'david.wilson@email.com', @kid5_id, 'uuid-005', 'pending', '2025-01-15 12:00:00'),
('JOURNEY005', 'david.wilson@email.com', @kid5_id, 'uuid-005', 'picked', '2025-01-15 12:10:00'),
('JOURNEY005', 'david.wilson@email.com', @kid5_id, 'uuid-005', 'departed', '2025-01-15 12:15:00'),
('JOURNEY005', 'david.wilson@email.com', @kid5_id, 'uuid-005', 'dropoff', '2025-01-15 12:30:00');

-- Insert dummy OTP codes (some expired, some active)
INSERT INTO otp_codes (pickup_id, otp_code, email, is_used, expires_at, created_at) VALUES
-- Active OTPs (not expired, not used)
('JOURNEY001', '123456', 'john.smith@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),
('JOURNEY002', '234567', 'sarah.johnson@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),
('JOURNEY003', '345678', 'michael.brown@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),
('JOURNEY004', '456789', 'emily.davis@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),
('JOURNEY005', '567890', 'david.wilson@email.com', 0, DATE_ADD(NOW(), INTERVAL 10 MINUTE), NOW()),

-- Used OTPs
('JOURNEY001', '111111', 'john.smith@email.com', 1, DATE_ADD(NOW(), INTERVAL -5 MINUTE), DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
('JOURNEY002', '222222', 'sarah.johnson@email.com', 1, DATE_ADD(NOW(), INTERVAL -5 MINUTE), DATE_SUB(NOW(), INTERVAL 20 MINUTE)),

-- Expired OTPs
('JOURNEY001', '999999', 'john.smith@email.com', 0, DATE_SUB(NOW(), INTERVAL 5 MINUTE), DATE_SUB(NOW(), INTERVAL 15 MINUTE)),
('JOURNEY003', '888888', 'michael.brown@email.com', 0, DATE_SUB(NOW(), INTERVAL 10 MINUTE), DATE_SUB(NOW(), INTERVAL 20 MINUTE));

-- Test pickup IDs for QR codes:
-- JOURNEY001 - Complete journey (Emma Smith)
-- JOURNEY002 - In progress (Lucas Johnson) 
-- JOURNEY003 - Just departed (Sophia Brown)
-- JOURNEY004 - Pending pickup (Oliver Davis)
-- JOURNEY005 - Dropoff in progress (Ava Wilson) 