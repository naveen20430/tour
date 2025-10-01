<?php
/**
 * Database Update Script for Tour Slider
 * This script adds the necessary columns for tour slider functionality
 */

require_once 'config/config.php';

echo "<h2>🔧 Tour Slider Database Update</h2>";
echo "<div style='font-family: Arial, sans-serif; max-width: 800px; margin: 20px;'>";

$errors = [];
$success = [];

try {
    // Check if database connection works
    $db->fetch("SELECT 1");
    echo "<p>✅ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

// 1. Add in_slider column to tours table
echo "<h3>Adding 'in_slider' column...</h3>";
try {
    $db->execute("ALTER TABLE tours ADD COLUMN in_slider TINYINT(1) DEFAULT 0");
    $success[] = "✅ Added 'in_slider' column to tours table";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        $success[] = "ℹ️ 'in_slider' column already exists";
    } else {
        $errors[] = "❌ Failed to add 'in_slider' column: " . $e->getMessage();
    }
}

// 2. Add slider_order column to tours table
echo "<h3>Adding 'slider_order' column...</h3>";
try {
    $db->execute("ALTER TABLE tours ADD COLUMN slider_order INT DEFAULT 0");
    $success[] = "✅ Added 'slider_order' column to tours table";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        $success[] = "ℹ️ 'slider_order' column already exists";
    } else {
        $errors[] = "❌ Failed to add 'slider_order' column: " . $e->getMessage();
    }
}

// 3. Add default slider settings
echo "<h3>Adding default slider settings...</h3>";
$default_settings = [
    'slider_autoplay' => '1',
    'slider_autoplay_speed' => '6000',
    'slider_animation_speed' => '1000',
    'slider_show_arrows' => '1',
    'slider_show_dots' => '1',
    'slider_pause_on_hover' => '1',
    'slider_slides_count' => '5'
];

foreach ($default_settings as $key => $value) {
    try {
        // Check if setting already exists
        $existing = $db->fetch("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
        if (!$existing) {
            $db->execute("INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES (?, ?, 'text')", [$key, $value]);
            $success[] = "✅ Added setting: $key = $value";
        } else {
            $success[] = "ℹ️ Setting '$key' already exists";
        }
    } catch (Exception $e) {
        $errors[] = "❌ Failed to add setting '$key': " . $e->getMessage();
    }
}

// 4. Set some tours as featured for the slider (if they exist)
echo "<h3>Setting up default slider tours...</h3>";
try {
    $tours = $db->fetchAll("SELECT id, title FROM tours WHERE status = 'active' LIMIT 3");
    if (!empty($tours)) {
        foreach ($tours as $index => $tour) {
            $order = $index + 1;
            $db->execute("UPDATE tours SET in_slider = 1, slider_order = ? WHERE id = ?", [$order, $tour['id']]);
            $success[] = "✅ Added '{$tour['title']}' to slider (order: $order)";
        }
    } else {
        $success[] = "ℹ️ No tours found to add to slider";
    }
} catch (Exception $e) {
    $errors[] = "❌ Failed to set default slider tours: " . $e->getMessage();
}

// 5. Create index for better performance
echo "<h3>Creating database indexes...</h3>";
try {
    $db->execute("CREATE INDEX idx_tours_slider ON tours(in_slider, slider_order)");
    $success[] = "✅ Created index for slider performance";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
        $success[] = "ℹ️ Slider index already exists";
    } else {
        $errors[] = "❌ Failed to create index: " . $e->getMessage();
    }
}

// 6. Test the slider functionality
echo "<h3>Testing slider functionality...</h3>";
try {
    $sliderTours = $db->fetchAll("
        SELECT t.*, d.name as destination_name, d.country,
               COALESCE(t.in_slider, 0) as in_slider,
               COALESCE(t.slider_order, 0) as slider_order
        FROM tours t 
        LEFT JOIN destinations d ON t.destination_id = d.id 
        WHERE t.status = 'active'
        ORDER BY t.slider_order ASC, t.featured DESC, t.popular DESC, t.created_at DESC
        LIMIT 3
    ");
    
    $success[] = "✅ Slider query test successful - found " . count($sliderTours) . " tours";
    
    if (!empty($sliderTours)) {
        foreach ($sliderTours as $tour) {
            $inSlider = $tour['in_slider'] ? 'Yes' : 'No';
            $success[] = "   📍 {$tour['title']} - In Slider: $inSlider, Order: {$tour['slider_order']}";
        }
    }
} catch (Exception $e) {
    $errors[] = "❌ Slider query test failed: " . $e->getMessage();
}

// Display results
echo "<hr><h3>📊 Update Summary</h3>";

if (!empty($success)) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4 style='color: #155724; margin-top: 0;'>✅ Successful Operations:</h4>";
    foreach ($success as $msg) {
        echo "<p style='margin: 5px 0; color: #155724;'>$msg</p>";
    }
    echo "</div>";
}

if (!empty($errors)) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h4 style='color: #721c24; margin-top: 0;'>❌ Errors:</h4>";
    foreach ($errors as $msg) {
        echo "<p style='margin: 5px 0; color: #721c24;'>$msg</p>";
    }
    echo "</div>";
}

// Final status
if (empty($errors)) {
    echo "<div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #0c5460; margin-top: 0;'>🎉 Database Update Complete!</h3>";
    echo "<p style='color: #0c5460;'>Your tour slider database has been successfully updated. You can now:</p>";
    echo "<ul style='color: #0c5460;'>";
    echo "<li>✅ Access the <a href='admin/tour-slider.php'>Tour Slider Admin Panel</a></li>";
    echo "<li>✅ Configure slider settings</li>";
    echo "<li>✅ Select and order tours for the slider</li>";
    echo "<li>✅ View the <a href='index.php' target='_blank'>Homepage Slider</a></li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24; margin-top: 0;'>⚠️ Update Completed with Issues</h3>";
    echo "<p style='color: #721c24;'>Some operations failed. Please check the errors above and contact support if needed.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔗 Quick Links</h3>";
echo "<div style='margin: 15px 0;'>";
echo "<a href='admin/tour-slider.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🎛️ Tour Slider Admin</a>";
echo "<a href='index.php' target='_blank' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>🏠 View Homepage</a>";
echo "<a href='admin/index.php' style='background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📊 Admin Dashboard</a>";
echo "</div>";

echo "</div>";

// Log the update
try {
    error_log("Tour Slider Database Update completed at " . date('Y-m-d H:i:s'));
} catch (Exception $e) {
    // Ignore logging errors
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 20px;
    background: #f8f9fa;
}

a {
    display: inline-block;
    margin: 5px;
    transition: all 0.3s ease;
}

a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

h2 {
    color: #333;
    text-align: center;
    margin-bottom: 30px;
}

h3 {
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 5px;
}
</style>