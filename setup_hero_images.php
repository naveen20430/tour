<?php
require_once 'config/config.php';

// Check if hero_images table exists
try {
    $result = $db->fetch("SHOW TABLES LIKE 'hero_images'");
    
    if (!$result) {
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
        echo "<div style='padding: 20px; color: green;'>✓ Hero images table created successfully!</div>";
        
        // Insert default hero image data
        $db->execute(
            "INSERT INTO hero_images (title, subtitle, description, image_path, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)",
            [
                'Welcome to Adventure Tours',
                'Discover Amazing Destinations',
                'Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.',
                'assets/images/hero/default-hero.jpg',
                1,
                1
            ]
        );
        echo "<div style='padding: 20px; color: green;'>✓ Default hero image data inserted!</div>";
    } else {
        echo "<div style='padding: 20px; color: blue;'>ℹ Hero images table already exists.</div>";
        
        // Check if there's any active hero image
        $activeHero = $db->fetch("SELECT * FROM hero_images WHERE is_active = 1");
        if (!$activeHero) {
            // Insert default if no active hero exists
            $db->execute(
                "INSERT INTO hero_images (title, subtitle, description, image_path, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)",
                [
                    'Welcome to Adventure Tours',
                    'Discover Amazing Destinations', 
                    'Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.',
                    'assets/images/hero/default-hero.jpg',
                    1,
                    1
                ]
            );
            echo "<div style='padding: 20px; color: green;'>✓ Default active hero image added!</div>";
        }
    }
    
    // Create default hero image if it doesn't exist
    $defaultHeroPath = BASE_PATH . 'assets/images/hero/default-hero.jpg';
    if (!file_exists($defaultHeroPath)) {
        // Create a simple colored placeholder image
        $image = imagecreate(1920, 1080);
        $blue = imagecolorallocate($image, 52, 152, 219);
        $white = imagecolorallocate($image, 255, 255, 255);
        
        // Fill background
        imagefill($image, 0, 0, $blue);
        
        // Add text
        $text = "Default Hero Image";
        imagestring($image, 5, 850, 520, $text, $white);
        
        // Save the image
        if (imagejpeg($image, $defaultHeroPath)) {
            echo "<div style='padding: 20px; color: green;'>✓ Default hero image created at: {$defaultHeroPath}</div>";
        } else {
            echo "<div style='padding: 20px; color: red;'>✗ Failed to create default hero image</div>";
        }
        
        imagedestroy($image);
    } else {
        echo "<div style='padding: 20px; color: blue;'>ℹ Default hero image already exists.</div>";
    }
    
    echo "<div style='padding: 20px; background: #f0f8f0; border: 1px solid #90EE90; border-radius: 5px; margin: 20px;'>";
    echo "<h3 style='color: #006400;'>Setup Complete!</h3>";
    echo "<p>Hero images functionality has been set up successfully. You can now:</p>";
    echo "<ul>";
    echo "<li>Go to <strong>Admin Panel → Hero Images</strong> to manage hero images</li>";
    echo "<li>Upload new hero images with custom titles and descriptions</li>";
    echo "<li>Set which image should be active on the homepage</li>";
    echo "<li>Reorder images by changing sort order</li>";
    echo "</ul>";
    echo "<p><a href='admin/hero-images.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Hero Images Management</a></p>";
    echo "<p><a href='index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;'>View Homepage</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='padding: 20px; color: red;'>Error: " . $e->getMessage() . "</div>";
}
?>
