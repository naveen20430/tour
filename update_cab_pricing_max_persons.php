<?php
require_once 'config/config.php';

echo "<h2>Adding Maximum Persons Field to Cab Route Pricing...</h2>";

try {
    // Add max_persons column
    echo "<p>Adding max_persons column...</p>";
    $db->query("
        ALTER TABLE cab_route_pricing 
        ADD COLUMN IF NOT EXISTS max_persons INT DEFAULT 4 COMMENT 'Maximum passengers allowed' AFTER cab_type_id
    ");
    echo "<p style='color: green;'>✅ Column added successfully!</p>";
    
    // Update existing records with max_persons from cab_types
    echo "<p>Updating existing records with passenger capacity...</p>";
    $db->query("
        UPDATE cab_route_pricing crp
        JOIN cab_types ct ON crp.cab_type_id = ct.id
        SET crp.max_persons = ct.max_passengers
        WHERE crp.max_persons IS NULL OR crp.max_persons = 0 OR crp.max_persons = 4
    ");
    echo "<p style='color: green;'>✅ Records updated successfully!</p>";
    
    echo "<hr>";
    echo "<h3>✅ Update Complete!</h3>";
    echo "<p>The <code>max_persons</code> field has been added to the cab_route_pricing table.</p>";
    
    // Show sample data
    echo "<h3>Sample Data with Max Persons:</h3>";
    $sample = $db->fetchAll("
        SELECT 
            cr.route_name,
            cr.from_location,
            cr.to_location,
            ct.display_name as cab_type,
            crp.max_persons,
            crp.one_way_price,
            crp.round_trip_price
        FROM cab_route_pricing crp
        JOIN cab_routes cr ON crp.route_id = cr.id
        JOIN cab_types ct ON crp.cab_type_id = ct.id
        WHERE crp.status = 'active'
        ORDER BY cr.display_order, ct.base_price
        LIMIT 15
    ");
    
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #667eea; color: white;'>";
    echo "<th>Route</th><th>Cab Type</th><th>Max Persons</th><th>One Way</th><th>Round Trip</th></tr>";
    
    foreach ($sample as $row) {
        echo "<tr>";
        echo "<td><strong>{$row['route_name']}</strong><br><small>{$row['from_location']} → {$row['to_location']}</small></td>";
        echo "<td>{$row['cab_type']}</td>";
        echo "<td style='text-align: center; font-weight: bold; color: #667eea;'><i class='fas fa-users'></i> {$row['max_persons']}</td>";
        echo "<td>" . formatPriceINR($row['one_way_price']) . "</td>";
        echo "<td>" . formatPriceINR($row['round_trip_price']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Cab Types and Their Capacities:</h3>";
    $cab_types = $db->fetchAll("SELECT display_name, max_passengers FROM cab_types WHERE status = 'active'");
    echo "<ul>";
    foreach ($cab_types as $cab) {
        echo "<li><strong>{$cab['display_name']}</strong>: {$cab['max_passengers']} passengers</li>";
    }
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><a href='" . BASE_URL . "admin/cab-routes.php'>→ Manage Cab Routes</a> | <a href='" . BASE_URL . "'>→ Homepage</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
table { margin: 20px 0; }
table th { padding: 12px; }
table td { padding: 10px; }
table tr:hover { background: #f8f9fa; }
</style>
