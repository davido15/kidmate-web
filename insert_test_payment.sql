-- Insert test payment record
INSERT INTO payments (
    payment_id, 
    parent_id, 
    child_id, 
    amount, 
    currency, 
    status, 
    payment_method, 
    description, 
    journey_date, 
    created_at
) VALUES (
    'PAY_1755022000_1234',  -- Unique payment ID
    '1',                    -- Parent ID (replace with actual parent ID)
    '1',                    -- Child/Student ID (replace with actual student ID)
    500.00,                 -- Amount
    'GHS',                  -- Currency (Ghana Cedi)
    'pending',              -- Status (pending, paid, failed)
    'online',               -- Payment method
    'School Fees - First Term 2024',  -- Payment description
    '2024-02-15',           -- Due date
    NOW()                   -- Created timestamp
);

-- Insert another test payment with different details
INSERT INTO payments (
    payment_id, 
    parent_id, 
    child_id, 
    amount, 
    currency, 
    status, 
    payment_method, 
    description, 
    journey_date, 
    created_at
) VALUES (
    'PAY_1755022100_5678',  -- Unique payment ID
    '2',                    -- Parent ID (replace with actual parent ID)
    '2',                    -- Child/Student ID (replace with actual student ID)
    250.00,                 -- Amount
    'GHS',                  -- Currency
    'pending',              -- Status
    'mobile_money',         -- Payment method
    'Books and Stationery Payment',  -- Payment description
    '2024-02-20',           -- Due date
    NOW()                   -- Created timestamp
);

-- Insert a third test payment
INSERT INTO payments (
    payment_id, 
    parent_id, 
    child_id, 
    amount, 
    currency, 
    status, 
    payment_method, 
    description, 
    journey_date, 
    created_at
) VALUES (
    'PAY_1755022200_9012',  -- Unique payment ID
    '3',                    -- Parent ID (replace with actual parent ID)
    '3',                    -- Child/Student ID (replace with actual student ID)
    150.00,                 -- Amount
    'GHS',                  -- Currency
    'pending',              -- Status
    'bank_transfer',        -- Payment method
    'Uniform Payment',      -- Payment description
    '2024-02-25',           -- Due date
    NOW()                   -- Created timestamp
); 