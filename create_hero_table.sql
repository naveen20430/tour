-- Create hero_images table for dynamic hero content
CREATE TABLE IF NOT EXISTS hero_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) DEFAULT NULL,
    subtitle VARCHAR(500) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_active TINYINT(1) DEFAULT 0,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default hero image
INSERT INTO hero_images (title, subtitle, description, image_path, is_active, sort_order) 
VALUES (
    'Welcome to Adventure Tours',
    'Discover Amazing Destinations',
    'Experience the world like never before with our carefully curated travel packages. From exotic destinations to cultural experiences, we make your travel dreams come true.',
    'assets/images/hero/default-hero.jpg',
    1,
    1
);
