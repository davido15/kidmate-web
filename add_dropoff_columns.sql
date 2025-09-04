-- Add new columns to pickup_persons table for drop-off location functionality
ALTER TABLE pickup_persons 
ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER kid_id,
ADD COLUMN dropoff_location VARCHAR(255) DEFAULT NULL AFTER phone,
ADD COLUMN dropoff_latitude DECIMAL(10, 8) DEFAULT NULL AFTER dropoff_location,
ADD COLUMN dropoff_longitude DECIMAL(11, 8) DEFAULT NULL AFTER dropoff_latitude;

-- Add index for better performance on location searches
CREATE INDEX idx_pickup_dropoff_location ON pickup_persons(dropoff_location);
CREATE INDEX idx_pickup_coordinates ON pickup_persons(dropoff_latitude, dropoff_longitude); 