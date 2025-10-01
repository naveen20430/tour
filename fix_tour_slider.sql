-- ================================================
-- Tour Slider Database Fix
-- Run this SQL script in your database to fix the slider columns issue
-- ================================================

-- Add in_slider column to tours table
ALTER TABLE tours ADD COLUMN in_slider TINYINT(1) DEFAULT 0;

-- Add slider_order column to tours table  
ALTER TABLE tours ADD COLUMN slider_order INT DEFAULT 0;

-- Insert default slider settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('slider_autoplay', '1', 'text'),
('slider_autoplay_speed', '6000', 'text'),
('slider_animation_speed', '1000', 'text'),
('slider_show_arrows', '1', 'text'),
('slider_show_dots', '1', 'text'),
('slider_pause_on_hover', '1', 'text'),
('slider_slides_count', '5', 'text')
ON DUPLICATE KEY UPDATE setting_key=setting_key;

-- Set first 3 active tours as slider tours (if any exist)
UPDATE tours SET in_slider = 1, slider_order = 1 
WHERE status = 'active' 
ORDER BY featured DESC, popular DESC, created_at DESC 
LIMIT 1;

UPDATE tours SET in_slider = 1, slider_order = 2 
WHERE status = 'active' AND in_slider = 0
ORDER BY featured DESC, popular DESC, created_at DESC 
LIMIT 1;

UPDATE tours SET in_slider = 1, slider_order = 3 
WHERE status = 'active' AND in_slider = 0
ORDER BY featured DESC, popular DESC, created_at DESC 
LIMIT 1;

-- Create index for better performance
CREATE INDEX idx_tours_slider ON tours(in_slider, slider_order);

-- ================================================
-- VERIFICATION QUERIES (Optional - for testing)
-- ================================================

-- Check if columns were added successfully
-- DESCRIBE tours;

-- Check slider settings
-- SELECT * FROM site_settings WHERE setting_key LIKE 'slider_%';

-- Check which tours are in slider
-- SELECT id, title, in_slider, slider_order FROM tours WHERE status = 'active' ORDER BY slider_order;

-- ================================================
-- NOTES:
-- ================================================
-- 1. If you get "Duplicate column name" errors, that means the columns already exist - this is OK
-- 2. If you get "Duplicate key name" errors for the index, that means it already exists - this is OK  
-- 3. The ON DUPLICATE KEY UPDATE clause prevents duplicate settings
-- 4. After running this, you should be able to access admin/tour-slider.php without errors
-- ================================================