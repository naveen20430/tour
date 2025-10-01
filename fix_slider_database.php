<?php
/**
 * Command Line Database Fix for Tour Slider
 * Run this from command line to fix the database columns issue
 */

require_once 'config/config.php';

echo "🔧 Fixing Tour Slider Database...\n\n";

$success = 0;
$errors = 0;

// 1. Add in_slider column
echo "Adding 'in_slider' column to tours table...\n";
try {
    $db->execute("ALTER TABLE tours ADD COLUMN in_slider TINYINT(1) DEFAULT 0");
    echo "✅ Added in_slider column\n";
    $success++;
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️  in_slider column already exists\n";
        $success++;
    } else {
        echo "❌ Failed to add in_slider column: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// 2. Add slider_order column
echo "\nAdding 'slider_order' column to tours table...\n";
try {
    $db->execute("ALTER TABLE tours ADD COLUMN slider_order INT DEFAULT 0");
    echo "✅ Added slider_order column\n";
    $success++;
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️  slider_order column already exists\n";
        $success++;
    } else {
        echo "❌ Failed to add slider_order column: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// 3. Add default settings
echo "\nAdding default slider settings...\n";
$settings = [
    'slider_autoplay' => '1',
    'slider_autoplay_speed' => '6000',
    'slider_animation_speed' => '1000',
    'slider_show_arrows' => '1',
    'slider_show_dots' => '1',
    'slider_pause_on_hover' => '1',
    'slider_slides_count' => '5'
];

foreach ($settings as $key => $value) {
    try {
        $existing = $db->fetch("SELECT id FROM site_settings WHERE setting_key = ?", [$key]);
        if (!$existing) {
            $db->execute("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
            echo "✅ Added setting: $key\n";
            $success++;
        } else {
            echo "ℹ️  Setting $key already exists\n";
        }
    } catch (Exception $e) {
        echo "❌ Failed to add setting $key: " . $e->getMessage() . "\n";
        $errors++;
    }
}

// 4. Set first 3 tours as slider tours
echo "\nSetting up default slider tours...\n";
try {
    $tours = $db->fetchAll("SELECT id, title FROM tours WHERE status = 'active' LIMIT 3");
    if (!empty($tours)) {
        foreach ($tours as $index => $tour) {
            $order = $index + 1;
            $db->execute("UPDATE tours SET in_slider = 1, slider_order = ? WHERE id = ?", [$order, $tour['id']]);
            echo "✅ Added '{$tour['title']}' to slider (order: $order)\n";
            $success++;
        }
    } else {
        echo "ℹ️  No active tours found\n";
    }
} catch (Exception $e) {
    echo "❌ Failed to set default slider tours: " . $e->getMessage() . "\n";
    $errors++;
}

// 5. Test the fix
echo "\nTesting the fix...\n";
try {
    $result = $db->fetchAll("
        SELECT t.id, t.title, 
               COALESCE(t.in_slider, 0) as in_slider,
               COALESCE(t.slider_order, 0) as slider_order
        FROM tours t 
        WHERE t.status = 'active'
        LIMIT 3
    ");
    echo "✅ Query test successful - found " . count($result) . " tours\n";
    foreach ($result as $tour) {
        echo "   📍 {$tour['title']} - In Slider: {$tour['in_slider']}, Order: {$tour['slider_order']}\n";
    }
    $success++;
} catch (Exception $e) {
    echo "❌ Query test failed: " . $e->getMessage() . "\n";
    $errors++;
}

// Summary
echo "\n" . str_repeat("=", 50) . "\n";
if ($errors == 0) {
    echo "🎉 DATABASE FIX COMPLETED SUCCESSFULLY!\n";
    echo "✅ $success operations completed\n";
    echo "❌ $errors errors\n\n";
    echo "You can now access:\n";
    echo "- Tour Slider Admin: admin/tour-slider.php\n";
    echo "- Homepage with Slider: index.php\n";
} else {
    echo "⚠️  DATABASE FIX COMPLETED WITH SOME ISSUES\n";
    echo "✅ $success operations completed\n";
    echo "❌ $errors errors\n";
}
echo str_repeat("=", 50) . "\n";
?>