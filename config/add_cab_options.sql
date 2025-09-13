-- Migration script to add cab booking options to the system
-- Run this script to add cab functionality to existing bookings table

USE travhub_db;

-- Add cab-related columns to bookings table if they don't exist
ALTER TABLE bookings 
ADD COLUMN IF NOT EXISTS cab_type ENUM('sedan', 'xuv_tavera', 'innova') DEFAULT NULL COMMENT 'Selected cab type for the tour',
ADD COLUMN IF NOT EXISTS cab_price DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cab charges for the tour',
ADD COLUMN IF NOT EXISTS total_with_cab DECIMAL(10,2) DEFAULT NULL COMMENT 'Total amount including cab charges';

-- Create cab_types table for managing cab options and pricing
CREATE TABLE IF NOT EXISTS cab_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    base_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    price_per_km DECIMAL(10,2) DEFAULT 0.00,
    max_passengers INT NOT NULL DEFAULT 4,
    description TEXT,
    features TEXT COMMENT 'JSON array of features',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default cab types with pricing
INSERT INTO cab_types (name, display_name, base_price, price_per_km, max_passengers, description, features) VALUES
('sedan', 'Sedan', 2500.00, 12.00, 4, 'Comfortable sedan car suitable for small groups', '["AC", "Music System", "Comfortable Seating"]'),
('xuv_tavera', 'Xylo / XUV / TAVERA', 3500.00, 15.00, 7, 'SUV vehicles perfect for medium groups', '["AC", "Spacious Interior", "Luggage Space", "Music System"]'),
('innova', 'Innova', 4500.00, 18.00, 7, 'Premium Toyota Innova for comfortable group travel', '["AC", "Premium Comfort", "Extra Luggage Space", "Entertainment System", "USB Charging"]')
ON DUPLICATE KEY UPDATE
    display_name = VALUES(display_name),
    base_price = VALUES(base_price),
    price_per_km = VALUES(price_per_km),
    max_passengers = VALUES(max_passengers),
    description = VALUES(description),
    features = VALUES(features);

-- Update existing bookings to have default cab values if needed
UPDATE bookings SET 
    cab_price = 0.00,
    total_with_cab = total_amount 
WHERE cab_price IS NULL OR total_with_cab IS NULL;
