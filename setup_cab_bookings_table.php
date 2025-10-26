<?php
require_once 'config/config.php';

$sql = "
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
";

try {
    $db->execute($sql);
    echo "✓ Successfully created cab_bookings table\n";
} catch (Exception $e) {
    echo "✗ Error creating cab_bookings table: " . $e->getMessage() . "\n";
}

echo "\nDone! You can now delete this file if everything works correctly.\n";
?>
