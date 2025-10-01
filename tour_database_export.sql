-- ================================================
-- TravHub Tour Booking System - Complete Database Export
-- Generated on: 2025-10-01
-- ================================================

-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS travhub_db;
CREATE DATABASE travhub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travhub_db;

-- ================================================
-- TABLE STRUCTURES
-- ================================================

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

-- Site settings table
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'boolean') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Destinations table (must be created before tours due to foreign key)
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

-- Tours table (extended version combining both schemas)
CREATE TABLE tours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE,
    city VARCHAR(50),
    destination_id INT,
    description TEXT,
    short_description TEXT,
    price DECIMAL(10,2),
    discount_price DECIMAL(10,2) DEFAULT NULL,
    duration_days INT DEFAULT 1,
    duration_nights INT DEFAULT 0,
    max_people INT DEFAULT 10,
    min_people INT DEFAULT 1,
    featured_image VARCHAR(255),
    gallery TEXT,
    inclusions TEXT,
    exclusions TEXT,
    pricing TEXT,
    vehicle_options TEXT,
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

-- Tour category relations
CREATE TABLE tour_category_relations (
    tour_id INT,
    category_id INT,
    PRIMARY KEY (tour_id, category_id),
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES tour_categories(id) ON DELETE CASCADE
);

-- Bookings table (enhanced version)
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    date_of_travel DATE NOT NULL,
    no_of_pax INT NOT NULL,
    contact VARCHAR(20) NOT NULL,
    id_proof VARCHAR(100),
    address TEXT,
    pricing VARCHAR(50),
    vehicle VARCHAR(50),
    total_amount DECIMAL(10,2) DEFAULT 0,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    status VARCHAR(20) DEFAULT 'pending',
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE SET NULL
);

-- Addons table
CREATE TABLE addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booking addons relation
CREATE TABLE booking_addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    addon_id INT,
    quantity INT DEFAULT 1,
    price_per_unit DECIMAL(10,2),
    total_price DECIMAL(10,2),
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(id) ON DELETE CASCADE
);

-- Users table (for frontend user registration)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hero images table
CREATE TABLE hero_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    subtitle VARCHAR(500),
    image_path VARCHAR(255) NOT NULL,
    link_url VARCHAR(255),
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author_id INT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,
    meta_title VARCHAR(200),
    meta_description TEXT,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cab pricing table
CREATE TABLE cab_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_type VARCHAR(100) NOT NULL,
    price_per_km DECIMAL(8,2) NOT NULL,
    base_price DECIMAL(8,2) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ================================================
-- DEFAULT DATA INSERTS
-- ================================================

-- Insert default admin user (password: 'admin123')
INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
('admin', 'admin@travhub.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'super_admin');

-- Insert default site settings
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'TravHub', 'text'),
('site_tagline', 'Adventure & Experience The Travel', 'text'),
('site_email', 'info@travhub.com', 'text'),
('site_phone', '+1 234 567 8900', 'text'),
('site_address', '6391 Elgin St. Celina, Delaware 10299', 'textarea'),
('opening_hours', '9:00am - 10:00pm', 'text'),
('contact_email', 'exam126@gmail.com', 'text');

-- Insert sample destinations
INSERT INTO destinations (name, slug, description, short_description, country, city, popular, status) VALUES
('Shimla Hills', 'shimla-hills', 'Experience the beauty of Shimla, the Queen of Hills with scenic views and colonial architecture.', 'Queen of Hills with colonial charm', 'India', 'Shimla', TRUE, 'active'),
('Manali Mountains', 'manali-mountains', 'Adventure hub in the Himalayas with river rafting, trekking, and snow activities.', 'Adventure hub in Himalayas', 'India', 'Manali', TRUE, 'active'),
('Dharamshala Spiritual', 'dharamshala-spiritual', 'Peaceful retreat in Dharamshala, home to Dalai Lama and Tibetan culture.', 'Spiritual retreat destination', 'India', 'Dharamshala', FALSE, 'active'),
('Kasol Valley', 'kasol-valley', 'Serene valley perfect for backpacking with cafes, nature walks, and paragliding.', 'Backpacker paradise valley', 'India', 'Kasol', TRUE, 'active');

-- Insert sample tour categories
INSERT INTO tour_categories (name, slug, description, status) VALUES
('Adventure Tours', 'adventure-tours', 'Thrilling adventure activities and sports', 'active'),
('Cultural Tours', 'cultural-tours', 'Explore local culture and heritage', 'active'),
('Spiritual Tours', 'spiritual-tours', 'Peaceful spiritual and meditation tours', 'active'),
('Hill Station Tours', 'hill-station-tours', 'Scenic mountain and hill destinations', 'active'),
('Backpacking Tours', 'backpacking-tours', 'Budget-friendly backpacking experiences', 'active');

-- Insert sample tours data
INSERT INTO tours (title, slug, city, destination_id, description, short_description, price, duration_days, duration_nights, max_people, inclusions, pricing, vehicle_options, tour_type, featured, popular, status) VALUES
('Shimla Hill Station Tour', 'shimla-hill-station-tour', 'Shimla', 1, 'Experience the beauty of Shimla, the Queen of Hills with scenic views and colonial architecture.', 'Queen of Hills experience', 5000.00, 3, 2, 8, 'Accommodation, Breakfast, Sightseeing, Guide', '5000,6000,7000', 'Sedan, SUV, Tempo Traveller', 'mountain', TRUE, TRUE, 'active'),
('Manali Adventure Tour', 'manali-adventure-tour', 'Manali', 2, 'Thrilling adventure in Manali with river rafting, trekking, and snow activities.', 'Thrilling Himalayan adventure', 7000.00, 4, 3, 10, 'Hotel, Meals, Adventure Activities, Transport', '7000,8000,9000', 'SUV, Bus, Jeep', 'adventure', TRUE, TRUE, 'active'),
('Dharamshala Spiritual Tour', 'dharamshala-spiritual-tour', 'Dharamshala', 3, 'Peaceful retreat in Dharamshala, home to Dalai Lama and Tibetan culture.', 'Peaceful spiritual retreat', 4500.00, 3, 2, 6, 'Stay, Meals, Monastery Visits, Meditation', '4500,5500,6500', 'Sedan, SUV', 'cultural', FALSE, TRUE, 'active'),
('Kasol Backpacking Tour', 'kasol-backpacking-tour', 'Kasol', 4, 'Chill backpacking experience in Kasol with cafes, paragliding, and nature walks.', 'Chill backpacking experience', 4000.00, 2, 1, 12, 'Hostel, Meals, Activities, Local Transport', '4000,5000,6000', 'SUV, Shared Jeep', 'adventure', FALSE, TRUE, 'active');

-- Insert sample addons data
INSERT INTO addons (name, description, price, status) VALUES
('Paragliding', 'Thrilling paragliding experience over the hills.', 1500.00, 'active'),
('River Rafting', 'Adventurous river rafting in mountain rivers.', 2000.00, 'active'),
('Trekking Guide', 'Professional trekking guide for mountain trails.', 1000.00, 'active'),
('Photography Session', 'Professional photography session at scenic locations.', 800.00, 'active'),
('Camping Equipment', 'Complete camping gear rental for overnight stays.', 1200.00, 'active');

-- Insert cab pricing data
INSERT INTO cab_pricing (vehicle_type, price_per_km, base_price, status) VALUES
('Sedan', 12.00, 500.00, 'active'),
('SUV', 15.00, 800.00, 'active'),
('Tempo Traveller', 20.00, 1200.00, 'active'),
('Mini Bus', 25.00, 1500.00, 'active'),
('Luxury Car', 30.00, 2000.00, 'active');

-- Insert sample hero images
INSERT INTO hero_images (title, subtitle, image_path, link_url, sort_order, status) VALUES
('Discover Amazing Places', 'Explore the world with our expertly curated tours and adventures', 'assets/images/hero/2025-09-12_1757677123_804adbb8-eea9-411f-9eb1-e3c7852ee9e3.jpg', 'tours.php', 1, 'active'),
('Adventure Awaits', 'Experience thrilling adventures in the most beautiful destinations', 'assets/images/hero/2025-09-12_1757677280_download.png', 'tours.php?type=adventure', 2, 'active');

-- ================================================
-- INDEXES FOR BETTER PERFORMANCE
-- ================================================

-- Tours table indexes
CREATE INDEX idx_tours_destination ON tours(destination_id);
CREATE INDEX idx_tours_status ON tours(status);
CREATE INDEX idx_tours_featured ON tours(featured);
CREATE INDEX idx_tours_popular ON tours(popular);
CREATE INDEX idx_tours_price ON tours(price);
CREATE INDEX idx_tours_type ON tours(tour_type);

-- Bookings table indexes
CREATE INDEX idx_bookings_tour_id ON bookings(tour_id);
CREATE INDEX idx_bookings_email ON bookings(email);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_date ON bookings(date_of_travel);

-- Destinations table indexes
CREATE INDEX idx_destinations_country ON destinations(country);
CREATE INDEX idx_destinations_popular ON destinations(popular);
CREATE INDEX idx_destinations_status ON destinations(status);

-- Users table indexes
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);

-- ================================================
-- END OF DATABASE EXPORT
-- Database: travhub_db
-- Tables: 15
-- Sample Data: Included
-- ================================================