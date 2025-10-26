-- Create cab routes pricing system
USE travhub_db;

-- Create cab_routes table for route management
CREATE TABLE IF NOT EXISTS cab_routes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    route_name VARCHAR(200) NOT NULL,
    from_location VARCHAR(100) NOT NULL,
    to_location VARCHAR(100) NOT NULL,
    distance_km DECIMAL(10,2) DEFAULT 0,
    estimated_duration VARCHAR(50) NULL COMMENT 'e.g., 3-4 hours',
    description TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_route (from_location, to_location)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cab_route_pricing table for cab type pricing per route
CREATE TABLE IF NOT EXISTS cab_route_pricing (
    id INT PRIMARY KEY AUTO_INCREMENT,
    route_id INT NOT NULL,
    cab_type_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    one_way_price DECIMAL(10,2) NULL,
    round_trip_price DECIMAL(10,2) NULL,
    price_notes TEXT NULL COMMENT 'Additional pricing information',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (route_id) REFERENCES cab_routes(id) ON DELETE CASCADE,
    FOREIGN KEY (cab_type_id) REFERENCES cab_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_route_cab (route_id, cab_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample routes
INSERT INTO cab_routes (route_name, from_location, to_location, distance_km, estimated_duration, description, display_order) VALUES
('Chandigarh to Shimla', 'Chandigarh', 'Shimla', 120, '3-4 hours', 'Scenic route through the mountains to the Queen of Hills', 1),
('Shimla to Chandigarh', 'Shimla', 'Chandigarh', 120, '3-4 hours', 'Return journey from Shimla to Chandigarh', 2),
('Chandigarh to Manali', 'Chandigarh', 'Manali', 310, '8-9 hours', 'Long scenic drive to the adventure capital', 3),
('Manali to Chandigarh', 'Manali', 'Chandigarh', 310, '8-9 hours', 'Return journey from Manali to Chandigarh', 4),
('Chandigarh to Dharamshala', 'Chandigarh', 'Dharamshala', 240, '6-7 hours', 'Journey to the home of Dalai Lama', 5),
('Dharamshala to Chandigarh', 'Dharamshala', 'Chandigarh', 240, '6-7 hours', 'Return journey from Dharamshala to Chandigarh', 6),
('Shimla to Manali', 'Shimla', 'Manali', 250, '7-8 hours', 'Mountain route connecting two popular hill stations', 7),
('Manali to Shimla', 'Manali', 'Shimla', 250, '7-8 hours', 'Return journey from Manali to Shimla', 8),
('Chandigarh to Kasauli', 'Chandigarh', 'Kasauli', 65, '1.5-2 hours', 'Short trip to the peaceful hill station', 9),
('Kasauli to Chandigarh', 'Kasauli', 'Chandigarh', 65, '1.5-2 hours', 'Return from Kasauli to Chandigarh', 10)
ON DUPLICATE KEY UPDATE 
    route_name = VALUES(route_name),
    distance_km = VALUES(distance_km),
    estimated_duration = VALUES(estimated_duration);

-- Insert sample pricing for each route and cab type
-- Get route and cab type IDs
SET @chandigarh_shimla = (SELECT id FROM cab_routes WHERE from_location = 'Chandigarh' AND to_location = 'Shimla');
SET @shimla_chandigarh = (SELECT id FROM cab_routes WHERE from_location = 'Shimla' AND to_location = 'Chandigarh');
SET @chandigarh_manali = (SELECT id FROM cab_routes WHERE from_location = 'Chandigarh' AND to_location = 'Manali');
SET @manali_chandigarh = (SELECT id FROM cab_routes WHERE from_location = 'Manali' AND to_location = 'Chandigarh');
SET @chandigarh_dharamshala = (SELECT id FROM cab_routes WHERE from_location = 'Chandigarh' AND to_location = 'Dharamshala');
SET @dharamshala_chandigarh = (SELECT id FROM cab_routes WHERE from_location = 'Dharamshala' AND to_location = 'Chandigarh');

SET @sedan_id = (SELECT id FROM cab_types WHERE name = 'sedan');
SET @xuv_id = (SELECT id FROM cab_types WHERE name = 'xuv_tavera');
SET @innova_id = (SELECT id FROM cab_types WHERE name = 'innova');

-- Note: max_passengers is already in cab_types table (4 for sedan, 7 for xuv/innova)

-- Chandigarh to Shimla pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@chandigarh_shimla, @sedan_id, 2500.00, 2500.00, 4500.00, 'Includes toll charges'),
(@chandigarh_shimla, @xuv_id, 3500.00, 3500.00, 6500.00, 'Includes toll charges and extra luggage space'),
(@chandigarh_shimla, @innova_id, 4500.00, 4500.00, 8500.00, 'Premium comfort with AC and entertainment')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

-- Shimla to Chandigarh pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@shimla_chandigarh, @sedan_id, 2500.00, 2500.00, 4500.00, 'Includes toll charges'),
(@shimla_chandigarh, @xuv_id, 3500.00, 3500.00, 6500.00, 'Includes toll charges and extra luggage space'),
(@shimla_chandigarh, @innova_id, 4500.00, 4500.00, 8500.00, 'Premium comfort with AC and entertainment')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

-- Chandigarh to Manali pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@chandigarh_manali, @sedan_id, 5500.00, 5500.00, 10500.00, 'Long distance, includes toll and parking'),
(@chandigarh_manali, @xuv_id, 7500.00, 7500.00, 14500.00, 'Perfect for group travel with luggage'),
(@chandigarh_manali, @innova_id, 9500.00, 9500.00, 18500.00, 'Most comfortable for long journeys')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

-- Manali to Chandigarh pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@manali_chandigarh, @sedan_id, 5500.00, 5500.00, 10500.00, 'Long distance, includes toll and parking'),
(@manali_chandigarh, @xuv_id, 7500.00, 7500.00, 14500.00, 'Perfect for group travel with luggage'),
(@manali_chandigarh, @innova_id, 9500.00, 9500.00, 18500.00, 'Most comfortable for long journeys')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

-- Chandigarh to Dharamshala pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@chandigarh_dharamshala, @sedan_id, 4500.00, 4500.00, 8500.00, 'Includes highway tolls'),
(@chandigarh_dharamshala, @xuv_id, 6000.00, 6000.00, 11500.00, 'Suitable for families'),
(@chandigarh_dharamshala, @innova_id, 7500.00, 7500.00, 14500.00, 'Premium travel experience')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

-- Dharamshala to Chandigarh pricing
INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, price_notes) VALUES
(@dharamshala_chandigarh, @sedan_id, 4500.00, 4500.00, 8500.00, 'Includes highway tolls'),
(@dharamshala_chandigarh, @xuv_id, 6000.00, 6000.00, 11500.00, 'Suitable for families'),
(@dharamshala_chandigarh, @innova_id, 7500.00, 7500.00, 14500.00, 'Premium travel experience')
ON DUPLICATE KEY UPDATE price = VALUES(price), one_way_price = VALUES(one_way_price), round_trip_price = VALUES(round_trip_price);

SELECT 'Cab routes and pricing created successfully!' as message;
SELECT COUNT(*) as total_routes FROM cab_routes;
SELECT COUNT(*) as total_pricing_entries FROM cab_route_pricing;
