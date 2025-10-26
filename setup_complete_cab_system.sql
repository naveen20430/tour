-- Create cab_bookings table
CREATE TABLE IF NOT EXISTS `cab_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `route_id` int(11) NOT NULL,
  `pricing_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `travel_date` date NOT NULL,
  `trip_type` enum('one_way','round_trip') NOT NULL,
  `num_passengers` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `special_requirements` text,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `payment_status` enum('pending','paid','refunded') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`),
  KEY `route_id` (`route_id`),
  KEY `pricing_id` (`pricing_id`),
  KEY `status` (`status`),
  KEY `travel_date` (`travel_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add max_persons column to cab_route_pricing if not exists
ALTER TABLE `cab_route_pricing` 
ADD COLUMN IF NOT EXISTS `max_persons` int(11) NOT NULL DEFAULT 4 AFTER `cab_type_id`;

-- Update max_persons based on cab_type_id
UPDATE cab_route_pricing crp
INNER JOIN cab_types ct ON crp.cab_type_id = ct.id
SET crp.max_persons = ct.max_passengers
WHERE crp.max_persons = 0 OR crp.max_persons IS NULL;
