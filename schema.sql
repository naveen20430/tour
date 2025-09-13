CREATE DATABASE IF NOT EXISTS tour_booking;
USE tour_booking;

CREATE TABLE tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(50),
    title VARCHAR(100),
    description TEXT,
    inclusions TEXT,
    pricing TEXT,
    vehicle_options TEXT
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_id INT,
    name VARCHAR(100),
    email VARCHAR(100),
    date_of_travel DATE,
    no_of_pax INT,
    contact VARCHAR(20),
    id_proof VARCHAR(100),
    address TEXT,
    pricing VARCHAR(50),
    vehicle VARCHAR(50),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id)
);

CREATE TABLE addons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2)
);

CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admin_users (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample data for tours
INSERT INTO tours (city, title, description, inclusions, pricing, vehicle_options) VALUES
('Shimla', 'Shimla Hill Station Tour', 'Experience the beauty of Shimla, the Queen of Hills with scenic views and colonial architecture.', 'Accommodation, Breakfast, Sightseeing, Guide', '5000,6000,7000', 'Sedan, SUV, Tempo Traveller'),
('Manali', 'Manali Adventure Tour', 'Thrilling adventure in Manali with river rafting, trekking, and snow activities.', 'Hotel, Meals, Adventure Activities, Transport', '7000,8000,9000', 'SUV, Bus, Jeep'),
('Dharamsala', 'Dharamsala Spiritual Tour', 'Peaceful retreat in Dharamsala, home to Dalai Lama and Tibetan culture.', 'Stay, Meals, Monastery Visits, Meditation', '4500,5500,6500', 'Sedan, SUV'),
('Kasol', 'Kasol Backpacking Tour', 'Chill backpacking experience in Kasol with cafes, paragliding, and nature.', 'Hostel, Meals, Activities, Local Transport', '4000,5000,6000', 'SUV, Shared Jeep');

-- Sample data for addons
INSERT INTO addons (name, description, price) VALUES
('Paragliding', 'Thrilling paragliding experience over the hills.', 1500.00),
('River Rafting', 'Adventurous river rafting in Manali.', 2000.00),
('Trekking', 'Guided trekking to nearby peaks.', 1000.00),
('Photography Tour', 'Professional photography session.', 800.00);