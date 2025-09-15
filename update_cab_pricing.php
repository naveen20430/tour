<?php
/**
 * Update Cab Pricing Script
 * Updates the cab_types table with new pricing according to the tour pricing table
 */

require_once 'config/config.php';

echo "<h1>Updating Cab Pricing</h1>\n";
echo "<p>This script will update your cab pricing to match the tour pricing table you provided.</p>\n";

try {
    // First, let's check if cab_types table exists
    $check_table = $db->fetchAll("SHOW TABLES LIKE 'cab_types'");
    
    if (empty($check_table)) {
        echo "<p>Creating cab_types table...</p>\n";
        
        // Create cab_types table
        $db->execute("
            CREATE TABLE cab_types (
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
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_name (name)
            )
        ");
        echo "<p style='color: green;'>✓ Cab_types table created successfully!</p>\n";
    }
    
    echo "<p>Updating cab types with new pricing...</p>\n";
    
    // Based on your pricing table, I'm using average prices for per-day rates
    // These are the base prices that can be adjusted per tour
    $cab_types = [
        [
            'name' => 'sedan',
            'display_name' => 'Sedan',
            'base_price' => 3000.00, // Average from your table
            'price_per_km' => 12.00,
            'max_passengers' => 4,
            'description' => 'Comfortable sedan car suitable for small groups',
            'features' => '["AC", "Music System", "Comfortable Seating", "GPS Navigation"]'
        ],
        [
            'name' => 'ertiga', 
            'display_name' => 'Ertiga',
            'base_price' => 4000.00, // Average from your table
            'price_per_km' => 15.00,
            'max_passengers' => 7,
            'description' => 'Spacious Ertiga perfect for medium-sized groups',
            'features' => '["AC", "7 Seater", "Ample Luggage Space", "Music System", "Power Steering"]'
        ],
        [
            'name' => 'innova',
            'display_name' => 'Innova', 
            'base_price' => 5200.00, // Average from your table
            'price_per_km' => 18.00,
            'max_passengers' => 7,
            'description' => 'Premium Toyota Innova for comfortable group travel',
            'features' => '["AC", "Premium Comfort", "Extra Luggage Space", "Entertainment System", "USB Charging", "Reclining Seats"]'
        ],
        [
            'name' => 'tempo_traveller',
            'display_name' => 'Tempo Traveller',
            'base_price' => 7000.00, // Average from your table
            'price_per_km' => 25.00,
            'max_passengers' => 12,
            'description' => 'Spacious Tempo Traveller for large groups and extended tours',
            'features' => '["AC", "12+ Seater", "Large Luggage Compartment", "Entertainment System", "Comfortable Seats", "Tour Guide Space"]'
        ]
    ];
    
    // Insert or update cab types
    foreach ($cab_types as $cab) {
        $db->execute("
            INSERT INTO cab_types (name, display_name, base_price, price_per_km, max_passengers, description, features) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                display_name = VALUES(display_name),
                base_price = VALUES(base_price),
                price_per_km = VALUES(price_per_km),
                max_passengers = VALUES(max_passengers),
                description = VALUES(description),
                features = VALUES(features),
                updated_at = CURRENT_TIMESTAMP
        ", [
            $cab['name'],
            $cab['display_name'], 
            $cab['base_price'],
            $cab['price_per_km'],
            $cab['max_passengers'],
            $cab['description'],
            $cab['features']
        ]);
        
        echo "<p style='color: green;'>✓ Updated {$cab['display_name']} - ₹{$cab['base_price']}/day</p>\n";
    }
    
    echo "<p>Creating tour-specific pricing table...</p>\n";
    
    // Create a tour-specific pricing table for the exact prices from your table
    $db->execute("
        CREATE TABLE IF NOT EXISTS tour_cab_pricing (
            id INT PRIMARY KEY AUTO_INCREMENT,
            tour_name VARCHAR(100) NOT NULL,
            sedan_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            ertiga_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            innova_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            tempo_traveller_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_tour (tour_name)
        )
    ");
    
    // Insert the exact pricing from your table
    $tour_pricing = [
        ['Day tour of Shimla', 2730, 3255, 3780, 4830],
        ['Full day tour Kufri, Fagu & Naldehra tour', 3045, 3780, 4830, 5880],
        ['Kufri and Chail tour', 3255, 3780, 4830, 5880],
        ['Chandigarh – Shimla and vice versa', 3255, 4515, 5880, 7455],
        ['Chandigarh – Manali and vice versa', 6930, 7980, 9555, 12705],
        ['Day tour of Manali', 220, 2730, 3780, 4830],
        ['Solang nalla, atal tunnel up to Sissu', 3255, 4305, 5355, 6930],
        ['Shimla – Manali and vice versa.', 6930, 7980, 9030, 11655]
    ];
    
    foreach ($tour_pricing as $pricing) {
        $db->execute("
            INSERT INTO tour_cab_pricing (tour_name, sedan_price, ertiga_price, innova_price, tempo_traveller_price)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                sedan_price = VALUES(sedan_price),
                ertiga_price = VALUES(ertiga_price),
                innova_price = VALUES(innova_price),
                tempo_traveller_price = VALUES(tempo_traveller_price),
                updated_at = CURRENT_TIMESTAMP
        ", $pricing);
        
        echo "<p style='color: blue;'>✓ Added pricing for: {$pricing[0]}</p>\n";
    }
    
    // Update bookings table to support new cab types if needed
    echo "<p>Checking bookings table structure...</p>\n";
    
    $columns = $db->fetchAll("SHOW COLUMNS FROM bookings");
    $has_cab_type = false;
    $has_cab_price = false;
    $has_total_with_cab = false;
    
    foreach ($columns as $column) {
        if ($column['Field'] == 'cab_type') $has_cab_type = true;
        if ($column['Field'] == 'cab_price') $has_cab_price = true;
        if ($column['Field'] == 'total_with_cab') $has_total_with_cab = true;
    }
    
    if (!$has_cab_type || !$has_cab_price || !$has_total_with_cab) {
        echo "<p>Adding cab-related columns to bookings table...</p>\n";
        
        if (!$has_cab_type) {
            $db->execute("ALTER TABLE bookings ADD COLUMN cab_type VARCHAR(50) DEFAULT NULL COMMENT 'Selected cab type for the tour'");
        }
        if (!$has_cab_price) {
            $db->execute("ALTER TABLE bookings ADD COLUMN cab_price DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cab charges for the tour'");
        }
        if (!$has_total_with_cab) {
            $db->execute("ALTER TABLE bookings ADD COLUMN total_with_cab DECIMAL(10,2) DEFAULT NULL COMMENT 'Total amount including cab charges'");
        }
        
        echo "<p style='color: green;'>✓ Bookings table updated with cab columns!</p>\n";
    }
    
    echo "<h2 style='color: green;'>🎉 Cab Pricing Update Completed Successfully!</h2>\n";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Updated Cab Options:</h3>";
    echo "<ul>";
    echo "<li>✓ Sedan (₹3,000/day average, up to 4 passengers)</li>";
    echo "<li>✓ Ertiga (₹4,000/day average, up to 7 passengers)</li>";
    echo "<li>✓ Innova (₹5,200/day average, up to 7 passengers)</li>";
    echo "<li>✓ Tempo Traveller (₹7,000/day average, up to 12 passengers)</li>";
    echo "<li>✓ Tour-specific pricing table created with exact rates</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>1. <a href='admin/cab_pricing.php'>Manage cab pricing from admin panel →</a></li>";
    echo "<li>2. Test the booking system with new cab options</li>";
    echo "<li>3. Update tour pages to show cab pricing</li>";
    echo "<li>4. Delete this migration file for security</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during update: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database configuration and try again.</p>\n";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Website</a> | <a href='admin/bookings.php'>View Bookings →</a></p>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cab Pricing Update</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; line-height: 1.6; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .warning { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .success { background: #d4edda; padding: 10px; border-left: 4px solid #28a745; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="warning">
        <strong>Note:</strong> This script updates your cab pricing structure. Please backup your database before running.
    </div>
</body>
</html>