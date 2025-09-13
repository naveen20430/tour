<?php
/**
 * Installation Script for Cab Booking Features
 * Run this script once to add cab booking functionality to your existing tour system
 */

require_once 'config/config.php';

echo "<h1>Installing Cab Booking Features</h1>\n";

try {
    echo "<p>Step 1: Adding cab-related columns to bookings table...</p>\n";
    
    // Check if columns already exist
    $check_columns = $db->fetchAll("SHOW COLUMNS FROM bookings LIKE 'cab_%'");
    
    if (empty($check_columns)) {
        // Add new columns to bookings table
        $db->execute("
            ALTER TABLE bookings 
            ADD COLUMN cab_type ENUM('sedan', 'xuv_tavera', 'innova') DEFAULT NULL COMMENT 'Selected cab type for the tour',
            ADD COLUMN cab_price DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Cab charges for the tour',
            ADD COLUMN total_with_cab DECIMAL(10,2) DEFAULT NULL COMMENT 'Total amount including cab charges'
        ");
        echo "<p style='color: green;'>✓ Cab columns added to bookings table successfully!</p>\n";
    } else {
        echo "<p style='color: orange;'>⚠ Cab columns already exist in bookings table.</p>\n";
    }
    
    echo "<p>Step 2: Creating cab_types table...</p>\n";
    
    // Create cab_types table
    $db->execute("
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
        )
    ");
    echo "<p style='color: green;'>✓ Cab_types table created successfully!</p>\n";
    
    echo "<p>Step 3: Inserting default cab types...</p>\n";
    
    // Insert default cab types
    $db->execute("
        INSERT INTO cab_types (name, display_name, base_price, price_per_km, max_passengers, description, features) VALUES
        ('sedan', 'Sedan', 2500.00, 12.00, 4, 'Comfortable sedan car suitable for small groups', '[\"AC\", \"Music System\", \"Comfortable Seating\"]'),
        ('xuv_tavera', 'Xylo / XUV / TAVERA', 3500.00, 15.00, 7, 'SUV vehicles perfect for medium groups', '[\"AC\", \"Spacious Interior\", \"Luggage Space\", \"Music System\"]'),
        ('innova', 'Innova', 4500.00, 18.00, 7, 'Premium Toyota Innova for comfortable group travel', '[\"AC\", \"Premium Comfort\", \"Extra Luggage Space\", \"Entertainment System\", \"USB Charging\"]')
        ON DUPLICATE KEY UPDATE
            display_name = VALUES(display_name),
            base_price = VALUES(base_price),
            price_per_km = VALUES(price_per_km),
            max_passengers = VALUES(max_passengers),
            description = VALUES(description),
            features = VALUES(features)
    ");
    echo "<p style='color: green;'>✓ Default cab types inserted successfully!</p>\n";
    
    echo "<p>Step 4: Updating existing bookings...</p>\n";
    
    // Update existing bookings to have default cab values
    $updated = $db->execute("
        UPDATE bookings SET 
            cab_price = 0.00,
            total_with_cab = total_amount 
        WHERE (cab_price IS NULL OR total_with_cab IS NULL) AND total_amount IS NOT NULL
    ");
    
    echo "<p style='color: green;'>✓ Existing bookings updated with default cab values!</p>\n";
    
    echo "<h2 style='color: green;'>🎉 Installation Completed Successfully!</h2>\n";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3>Cab Booking Features Added:</h3>";
    echo "<ul>";
    echo "<li>✓ Sedan cabs (₹2,500/day, up to 4 passengers)</li>";
    echo "<li>✓ Xylo/XUV/TAVERA cabs (₹3,500/day, up to 7 passengers)</li>";
    echo "<li>✓ Innova cabs (₹4,500/day, up to 7 passengers)</li>";
    echo "<li>✓ Automatic pricing calculation</li>";
    echo "<li>✓ Passenger capacity validation</li>";
    echo "<li>✓ Admin booking management with cab details</li>";
    echo "<li>✓ CSV export with cab information</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li>1. Test the booking system by making a test booking</li>";
    echo "<li>2. Access admin panel at <a href='admin/bookings.php'>/admin/bookings.php</a> to manage bookings</li>";
    echo "<li>3. Customize cab pricing in the cab_types table if needed</li>";
    echo "<li>4. Delete this installation file for security: <code>install_cab_features.php</code></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error during installation: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database configuration and try again.</p>\n";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Website</a> | <a href='admin/bookings.php'>View Admin Bookings →</a></p>";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cab Features Installation</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        p { line-height: 1.6; }
        ul { line-height: 1.8; }
        .warning { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="warning">
        <strong>Important:</strong> This is a one-time installation script. Please delete this file after successful installation for security purposes.
    </div>
</body>
</html>
