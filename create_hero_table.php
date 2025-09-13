<?php
require_once 'config/config.php';

echo "<h2>Hero Images Table Setup</h2>";

try {
    // Check if table exists
    $result = $db->fetch("SHOW TABLES LIKE 'hero_images'");
    
    if (!$result) {
        echo "<p style='color: orange;'>Creating hero_images table...</p>";
        
        // Create the hero_images table
        $sql = "
        CREATE TABLE hero_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) DEFAULT NULL,
            subtitle VARCHAR(500) DEFAULT NULL,
            description TEXT DEFAULT NULL,
            image_path VARCHAR(500) NOT NULL,
            is_active TINYINT(1) DEFAULT 0,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->execute($sql);
        echo "<p style='color: green;'>✓ Hero images table created successfully!</p>";
        
        // Check if hero directory exists
        $heroDir = BASE_PATH . 'assets/images/hero/';
        if (!is_dir($heroDir)) {
            mkdir($heroDir, 0755, true);
            echo "<p style='color: green;'>✓ Hero images directory created: $heroDir</p>";
        }
        
        // Set directory permissions
        chmod($heroDir, 0755);
        echo "<p style='color: green;'>✓ Directory permissions set to 755</p>";
        
        // Insert default hero image data (optional)
        $defaultImagePath = 'assets/images/hero/default-hero.jpg';
        $db->execute(
            "INSERT INTO hero_images (title, subtitle, description, image_path, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)",
            [
                'Welcome to Adventure Tours',
                'Discover Amazing Destinations',
                'Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.',
                $defaultImagePath,
                1,
                1
            ]
        );
        echo "<p style='color: green;'>✓ Default hero image data inserted!</p>";
        
    } else {
        echo "<p style='color: blue;'>ℹ Hero images table already exists.</p>";
        
        // Check directory
        $heroDir = BASE_PATH . 'assets/images/hero/';
        echo "<p><strong>Directory Check:</strong></p>";
        echo "<p>Path: $heroDir</p>";
        echo "<p>Exists: " . (is_dir($heroDir) ? '✅ YES' : '❌ NO') . "</p>";
        echo "<p>Writable: " . (is_writable($heroDir) ? '✅ YES' : '❌ NO') . "</p>";
        
        if (!is_dir($heroDir)) {
            mkdir($heroDir, 0755, true);
            echo "<p style='color: green;'>✓ Created missing directory</p>";
        }
        
        // Fix permissions if needed
        if (!is_writable($heroDir)) {
            chmod($heroDir, 0755);
            echo "<p style='color: green;'>✓ Fixed directory permissions</p>";
        }
    }
    
    // Show current hero images
    $heroes = $db->fetchAll("SELECT * FROM hero_images");
    echo "<h3>Current Hero Images (" . count($heroes) . "):</h3>";
    
    if (empty($heroes)) {
        echo "<p>No hero images found.</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Subtitle</th><th>Image Path</th><th>Active</th><th>File Exists</th></tr>";
        foreach ($heroes as $hero) {
            $filePath = BASE_PATH . $hero['image_path'];
            echo "<tr>";
            echo "<td>" . $hero['id'] . "</td>";
            echo "<td>" . htmlspecialchars($hero['title']) . "</td>";
            echo "<td>" . htmlspecialchars($hero['subtitle']) . "</td>";
            echo "<td>" . htmlspecialchars($hero['image_path']) . "</td>";
            echo "<td>" . ($hero['is_active'] ? '✅ YES' : '❌ NO') . "</td>";
            echo "<td>" . (file_exists($filePath) ? '✅ YES' : '❌ NO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<p style='margin-top: 20px;'><a href='admin/hero-images-debug.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Debug Upload Page</a></p>";
    echo "<p><a href='admin/hero-images.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Normal Hero Management</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<p style='color: red;'>This might be a database connection issue.</p>";
}
?>
