-- Create database
CREATE DATABASE IF NOT EXISTS travhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travhub_db;

-- Admin users table
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin') DEFAULT 'admin',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: 'admin123')
INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
('admin', 'admin@travhub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'super_admin');

-- Site settings table
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'boolean') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'TravHub', 'text'),
('site_tagline', 'Adventure & Experience The Travel', 'text'),
('site_email', 'info@travhub.com', 'text'),
('site_phone', '+1 234 567 8900', 'text'),
('site_address', '6391 Elgin St. Celina, Delaware 10299', 'textarea'),
('opening_hours', '9:00am - 10:00pm', 'text'),
('contact_email', 'exam126@gmail.com', 'text');

-- Tours table
CREATE TABLE tours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    destination_id INT,
    description TEXT,
    short_description TEXT,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2) DEFAULT NULL,
    duration_days INT NOT NULL,
    duration_nights INT NOT NULL,
    max_people INT NOT NULL,
    min_people INT DEFAULT 1,
    featured_image VARCHAR(255),
    gallery TEXT,
    inclusions TEXT,
    exclusions TEXT,
    itinerary TEXT,
    difficulty_level ENUM('easy', 'moderate', 'difficult') DEFAULT 'moderate',
    tour_type ENUM('adventure', 'cultural', 'wildlife', 'beach', 'mountain', 'city') DEFAULT 'cultural',
    featured BOOLEAN DEFAULT FALSE,
    popular BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    availability_start DATE,
    availability_end DATE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
);

-- Destinations table
CREATE TABLE destinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    short_description TEXT,
    country VARCHAR(50) NOT NULL,
    city VARCHAR(50),
    featured_image VARCHAR(255),
    gallery TEXT,
    popular BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    meta_title VARCHAR(200),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tour categories table
CREATE TABLE tour_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tour category relations
CREATE TABLE tour_category_relations (
    tour_id INT,
    category_id INT,
    PRIMARY KEY (tour_id, category_id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES tour_categories(id) ON DELETE CASCADE
);
