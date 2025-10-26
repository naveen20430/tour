<?php
require_once 'config/config.php';

$route_id = $_GET['route_id'] ?? 8;

echo "<h1>Debug Route ID: {$route_id}</h1>";

// Check if route exists
$route = $db->fetch("SELECT * FROM cab_routes WHERE id = ?", [$route_id]);
echo "<h2>Route Data:</h2>";
echo "<pre>";
print_r($route);
echo "</pre>";

// Check pricing options
$pricing = $db->fetchAll("
    SELECT crp.*, ct.display_name, ct.description as cab_description, ct.features
    FROM cab_route_pricing crp
    INNER JOIN cab_types ct ON crp.cab_type_id = ct.id
    WHERE crp.route_id = ?
", [$route_id]);

echo "<h2>Pricing Options:</h2>";
echo "<pre>";
print_r($pricing);
echo "</pre>";

// Check if cab_bookings table exists
try {
    $table_check = $db->fetch("SHOW TABLES LIKE 'cab_bookings'");
    echo "<h2>cab_bookings table exists:</h2>";
    echo "<pre>";
    print_r($table_check);
    echo "</pre>";
} catch (Exception $e) {
    echo "<h2>Error checking cab_bookings table:</h2>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?>
