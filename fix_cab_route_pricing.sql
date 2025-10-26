-- Add max_persons column to cab_route_pricing table
ALTER TABLE `cab_route_pricing` 
ADD COLUMN `max_persons` int(11) NOT NULL DEFAULT 4 AFTER `cab_type_id`;

-- Update max_persons based on cab_type_id
UPDATE cab_route_pricing crp
INNER JOIN cab_types ct ON crp.cab_type_id = ct.id
SET crp.max_persons = ct.max_passengers;
