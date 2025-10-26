<?php
require_once 'config/config.php';

echo "<h2>Setting up Cab Routes Pricing System...</h2>";

try {
    // Create cab_routes table
    echo "<p>Creating cab_routes table...</p>";
    $db->query("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ cab_routes table created</p>";
    
    // Create cab_route_pricing table
    echo "<p>Creating cab_route_pricing table...</p>";
    $db->query("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "<p style='color: green;'>✅ cab_route_pricing table created</p>";
    
    // Insert sample routes
    echo "<p>Inserting sample routes...</p>";
    $db->query("
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
            estimated_duration = VALUES(estimated_duration)
    ");
    echo "<p style='color: green;'>✅ Routes inserted</p>";
    
    // Get cab type IDs
    $sedan = $db->fetch("SELECT id FROM cab_types WHERE name = 'sedan'");
    $xuv = $db->fetch("SELECT id FROM cab_types WHERE name = 'xuv_tavera'");
    $innova = $db->fetch("SELECT id FROM cab_types WHERE name = 'innova'");
    
    if (!$sedan || !$xuv || !$innova) {
        throw new Exception("Cab types not found! Please run install_cab_features.php first.");
    }
    
    // Insert pricing for all routes
    echo "<p>Inserting route pricing...</p>";
    $routes = $db->fetchAll("SELECT id, from_location, to_location FROM cab_routes");
    
    foreach ($routes as $route) {
        // Determine pricing based on route
        $pricing = [];
        
        // Chandigarh - Shimla / Shimla - Chandigarh
        if (($route['from_location'] == 'Chandigarh' && $route['to_location'] == 'Shimla') ||
            ($route['from_location'] == 'Shimla' && $route['to_location'] == 'Chandigarh')) {
            $pricing = [
                ['cab_id' => $sedan['id'], 'price' => 2500, 'round' => 4500],
                ['cab_id' => $xuv['id'], 'price' => 3500, 'round' => 6500],
                ['cab_id' => $innova['id'], 'price' => 4500, 'round' => 8500]
            ];
        }
        // Chandigarh - Manali / Manali - Chandigarh  
        elseif (($route['from_location'] == 'Chandigarh' && $route['to_location'] == 'Manali') ||
                ($route['from_location'] == 'Manali' && $route['to_location'] == 'Chandigarh')) {
            $pricing = [
                ['cab_id' => $sedan['id'], 'price' => 5500, 'round' => 10500],
                ['cab_id' => $xuv['id'], 'price' => 7500, 'round' => 14500],
                ['cab_id' => $innova['id'], 'price' => 9500, 'round' => 18500]
            ];
        }
        // Chandigarh - Dharamshala / Dharamshala - Chandigarh
        elseif (($route['from_location'] == 'Chandigarh' && $route['to_location'] == 'Dharamshala') ||
                ($route['from_location'] == 'Dharamshala' && $route['to_location'] == 'Chandigarh')) {
            $pricing = [
                ['cab_id' => $sedan['id'], 'price' => 4500, 'round' => 8500],
                ['cab_id' => $xuv['id'], 'price' => 6000, 'round' => 11500],
                ['cab_id' => $innova['id'], 'price' => 7500, 'round' => 14500]
            ];
        }
        // Shimla - Manali / Manali - Shimla
        elseif (($route['from_location'] == 'Shimla' && $route['to_location'] == 'Manali') ||
                ($route['from_location'] == 'Manali' && $route['to_location'] == 'Shimla')) {
            $pricing = [
                ['cab_id' => $sedan['id'], 'price' => 5000, 'round' => 9500],
                ['cab_id' => $xuv['id'], 'price' => 6500, 'round' => 12500],
                ['cab_id' => $innova['id'], 'price' => 8000, 'round' => 15500]
            ];
        }
        // Chandigarh - Kasauli / Kasauli - Chandigarh
        elseif (($route['from_location'] == 'Chandigarh' && $route['to_location'] == 'Kasauli') ||
                ($route['from_location'] == 'Kasauli' && $route['to_location'] == 'Chandigarh')) {
            $pricing = [
                ['cab_id' => $sedan['id'], 'price' => 1500, 'round' => 2800],
                ['cab_id' => $xuv['id'], 'price' => 2000, 'round' => 3800],
                ['cab_id' => $innova['id'], 'price' => 2500, 'round' => 4500]
            ];
        }
        
        // Insert pricing for each cab type
        foreach ($pricing as $p) {
            try {
                $db->query("
                    INSERT INTO cab_route_pricing (route_id, cab_type_id, price, one_way_price, round_trip_price, status)
                    VALUES (?, ?, ?, ?, ?, 'active')
                    ON DUPLICATE KEY UPDATE 
                        price = VALUES(price),
                        one_way_price = VALUES(one_way_price),
                        round_trip_price = VALUES(round_trip_price)
                ", [$route['id'], $p['cab_id'], $p['price'], $p['price'], $p['round']]);
            } catch (Exception $e) {
                // Ignore duplicate entries
            }
        }
    }
    echo "<p style='color: green;'>✅ Pricing inserted</p>";
    
    
    // Get statistics
    $total_routes = $db->fetch("SELECT COUNT(*) as count FROM cab_routes")['count'];
    $total_pricing = $db->fetch("SELECT COUNT(*) as count FROM cab_route_pricing")['count'];
    
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; border-left: 5px solid #28a745;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Setup Complete!</h3>";
    echo "<p><strong>Routes Created:</strong> {$total_routes}</p>";
    echo "<p><strong>Pricing Entries:</strong> {$total_pricing}</p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>Sample Routes:</h3>";
    
    $routes = $db->fetchAll("SELECT * FROM cab_routes ORDER BY display_order LIMIT 10");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #667eea; color: white;'>";
    echo "<th>Route</th><th>From</th><th>To</th><th>Distance</th><th>Duration</th><th>Status</th></tr>";
    
    foreach ($routes as $route) {
        echo "<tr>";
        echo "<td><strong>{$route['route_name']}</strong></td>";
        echo "<td>{$route['from_location']}</td>";
        echo "<td>{$route['to_location']}</td>";
        echo "<td>{$route['distance_km']} km</td>";
        echo "<td>{$route['estimated_duration']}</td>";
        echo "<td style='color: " . ($route['status'] == 'active' ? 'green' : 'red') . "'>{$route['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Next Steps:</h3>";
    echo "<ul>";
    echo "<li><a href='" . BASE_URL . "admin/cab-routes.php' target='_blank'>→ Manage Cab Routes in Admin Panel</a></li>";
    echo "<li><a href='" . BASE_URL . "admin/cab_pricing.php' target='_blank'>→ View Existing Cab Pricing</a></li>";
    echo "<li><a href='" . BASE_URL . "' target='_blank'>→ View Homepage</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
