<?php
require_once 'config/config.php';

echo "<h2>Checking Destinations in Database...</h2>";

try {
    // Get all destinations
    $all_destinations = $db->fetchAll("SELECT id, name, slug, country, city, popular, status FROM destinations ORDER BY created_at DESC");
    
    if (empty($all_destinations)) {
        echo "<p style='color: red;'>❌ No destinations found in database!</p>";
        echo "<p>You need to add destinations first through Admin Panel > Destinations</p>";
    } else {
        echo "<h3>Found " . count($all_destinations) . " destinations:</h3>";
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #667eea; color: white;'>";
        echo "<th>ID</th><th>Name</th><th>Slug</th><th>Country</th><th>City</th><th>Popular</th><th>Status</th><th>Action</th>";
        echo "</tr>";
        
        foreach ($all_destinations as $dest) {
            $popularText = $dest['popular'] ? '✅ Yes' : '❌ No';
            $statusColor = $dest['status'] == 'active' ? 'green' : 'red';
            
            echo "<tr>";
            echo "<td>{$dest['id']}</td>";
            echo "<td><strong>{$dest['name']}</strong></td>";
            echo "<td>{$dest['slug']}</td>";
            echo "<td>{$dest['country']}</td>";
            echo "<td>{$dest['city']}</td>";
            echo "<td>{$popularText}</td>";
            echo "<td style='color: {$statusColor}; font-weight: bold;'>{$dest['status']}</td>";
            
            // Add quick action buttons
            if (!$dest['popular'] || $dest['status'] != 'active') {
                echo "<td><a href='?make_popular={$dest['id']}' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px;'>Make Popular & Active</a></td>";
            } else {
                echo "<td style='color: green;'>✅ Ready for Homepage</td>";
            }
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Count popular and active
        $popular_active = $db->fetch("SELECT COUNT(*) as count FROM destinations WHERE popular = 1 AND status = 'active'");
        echo "<br><h3>Summary:</h3>";
        echo "<p><strong>{$popular_active['count']}</strong> destinations will show on homepage (popular = 1 AND status = active)</p>";
        
        if ($popular_active['count'] == 0) {
            echo "<p style='color: orange;'>⚠️ <strong>No destinations will show on homepage!</strong> Click 'Make Popular & Active' buttons above to fix this.</p>";
        }
    }
    
    // Handle quick action
    if (isset($_GET['make_popular'])) {
        $dest_id = intval($_GET['make_popular']);
        $db->query("UPDATE destinations SET popular = 1, status = 'active' WHERE id = ?", [$dest_id]);
        echo "<script>window.location.href = 'check_destinations.php';</script>";
    }
    
    // Add a button to make all active and popular
    if (!empty($all_destinations)) {
        echo "<hr>";
        echo "<h3>Quick Actions:</h3>";
        echo "<form method='POST'>";
        echo "<button type='submit' name='make_all_popular' style='background: #667eea; color: white; padding: 15px 30px; border: none; border-radius: 10px; font-size: 16px; font-weight: bold; cursor: pointer;'>✨ Make ALL Destinations Popular & Active</button>";
        echo "</form>";
        
        if (isset($_POST['make_all_popular'])) {
            $db->query("UPDATE destinations SET popular = 1, status = 'active'");
            echo "<p style='color: green;'>✅ All destinations are now popular and active!</p>";
            echo "<script>setTimeout(function(){ window.location.href = 'check_destinations.php'; }, 1500);</script>";
        }
    }
    
    echo "<hr>";
    echo "<p><a href='" . BASE_URL . "' target='_blank'>→ View Homepage</a> | <a href='" . BASE_URL . "admin/destinations.php' target='_blank'>→ Manage Destinations in Admin</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
