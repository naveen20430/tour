-- Add max_persons field to cab_route_pricing table
USE travhub_db;

-- Add max_persons column if it doesn't exist
ALTER TABLE cab_route_pricing 
ADD COLUMN IF NOT EXISTS max_persons INT DEFAULT 4 COMMENT 'Maximum passengers allowed' AFTER cab_type_id;

-- Update existing records with max_persons from cab_types table
UPDATE cab_route_pricing crp
JOIN cab_types ct ON crp.cab_type_id = ct.id
SET crp.max_persons = ct.max_passengers
WHERE crp.max_persons IS NULL OR crp.max_persons = 0;

-- Show updated structure
SELECT 'max_persons column added successfully!' as message;

-- Show sample data with max_persons
SELECT 
    cr.route_name,
    ct.display_name as cab_type,
    crp.max_persons,
    crp.one_way_price,
    crp.round_trip_price
FROM cab_route_pricing crp
JOIN cab_routes cr ON crp.route_id = cr.id
JOIN cab_types ct ON crp.cab_type_id = ct.id
ORDER BY cr.display_order, ct.base_price
LIMIT 10;
