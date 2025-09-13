<?php
// Direct database installation script
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'travhub_db';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Drop and recreate database
    $pdo->exec("DROP DATABASE IF EXISTS $database");
    $pdo->exec("CREATE DATABASE $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $database");
    
    echo "<h2>Installing TravHub Database...</h2>";
    
    // Create tables
    $pdo->exec("
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
        )
    ");
    
    $pdo->exec("
        CREATE TABLE site_settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            setting_type ENUM('text', 'textarea', 'image', 'boolean') DEFAULT 'text',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
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
        )
    ");
    
    $pdo->exec("
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
        )
    ");
    
    $pdo->exec("
        CREATE TABLE tour_categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            image VARCHAR(255),
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE tour_category_relations (
            tour_id INT,
            category_id INT,
            PRIMARY KEY (tour_id, category_id),
            FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES tour_categories(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE bookings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            booking_number VARCHAR(20) UNIQUE NOT NULL,
            tour_id INT NOT NULL,
            user_id INT NULL,
            guest_name VARCHAR(100) NOT NULL,
            guest_email VARCHAR(100) NOT NULL,
            guest_phone VARCHAR(20) NOT NULL,
            number_of_people INT NOT NULL,
            tour_date DATE NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            paid_amount DECIMAL(10,2) DEFAULT 0,
            payment_status ENUM('pending', 'partial', 'paid', 'refunded') DEFAULT 'pending',
            payment_method VARCHAR(50) NULL,
            payment_details TEXT NULL,
            booking_status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
            special_requirements TEXT NULL,
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            first_name VARCHAR(50) NOT NULL,
            last_name VARCHAR(50) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20) NULL,
            date_of_birth DATE NULL,
            address TEXT NULL,
            city VARCHAR(50) NULL,
            country VARCHAR(50) NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            email_verified BOOLEAN DEFAULT FALSE,
            verification_token VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE blog_categories (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT NULL,
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    $pdo->exec("
        CREATE TABLE blog_posts (
            id INT PRIMARY KEY AUTO_INCREMENT,
            title VARCHAR(200) NOT NULL,
            slug VARCHAR(200) UNIQUE NOT NULL,
            category_id INT NULL,
            author_id INT NOT NULL,
            excerpt TEXT NULL,
            content LONGTEXT NOT NULL,
            featured_image VARCHAR(255) NULL,
            tags TEXT NULL,
            featured BOOLEAN DEFAULT FALSE,
            status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
            published_at TIMESTAMP NULL,
            meta_title VARCHAR(200) NULL,
            meta_description TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
            FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE CASCADE
        )
    ");
    
    $pdo->exec("
        CREATE TABLE contact_inquiries (
            id INT PRIMARY KEY AUTO_INCREMENT,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20) NULL,
            subject VARCHAR(200) NULL,
            message TEXT NOT NULL,
            status ENUM('new', 'read', 'replied') DEFAULT 'new',
            replied_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    echo "<p>✅ Database tables created</p>";
    
    // Insert admin user
    $pdo->exec("
        INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
        ('admin', 'admin@travhub.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'super_admin')
    ");
    
    // Insert site settings
    $pdo->exec("
        INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
        ('site_name', 'TravHub', 'text'),
        ('site_tagline', 'Adventure & Experience The Travel', 'text'),
        ('site_email', 'info@travhub.com', 'text'),
        ('site_phone', '+1 234 567 8900', 'text'),
        ('site_address', '6391 Elgin St. Celina, Delaware 10299', 'textarea'),
        ('opening_hours', '9:00am - 10:00pm', 'text'),
        ('contact_email', 'exam126@gmail.com', 'text')
    ");
    
    // Insert destinations
    $pdo->exec("
        INSERT INTO destinations (name, slug, description, short_description, country, city, popular, status) VALUES
        ('Dubai, Emirates', 'dubai-emirates', 'Experience the luxury and modern marvels of Dubai with its iconic skyline, world-class shopping, and desert adventures. From the towering Burj Khalifa to the pristine beaches, Dubai offers a perfect blend of tradition and innovation.', 'Luxury destination with modern attractions', 'UAE', 'Dubai', TRUE, 'active'),
        ('Paris, France', 'paris-france', 'Discover the city of love with its iconic landmarks, charming cafes, and romantic atmosphere. Walk along the Seine River, visit the Eiffel Tower, and explore world-class museums like the Louvre.', 'The city of love and lights', 'France', 'Paris', TRUE, 'active'),
        ('Bali, Indonesia', 'bali-indonesia', 'Tropical paradise with beautiful beaches, ancient temples, and vibrant culture. Experience traditional Balinese hospitality, stunning rice terraces, and world-class surfing spots.', 'Tropical paradise with rich culture', 'Indonesia', 'Bali', TRUE, 'active'),
        ('Tokyo, Japan', 'tokyo-japan', 'Modern metropolis where ancient traditions meet cutting-edge technology. Experience authentic sushi, visit historic temples, and enjoy the vibrant nightlife of this incredible city.', 'Where tradition meets innovation', 'Japan', 'Tokyo', TRUE, 'active'),
        ('Santorini, Greece', 'santorini-greece', 'Breathtaking Greek island famous for its white-washed buildings, blue-domed churches, and spectacular sunsets. Enjoy wine tasting, volcanic beaches, and Mediterranean cuisine.', 'Iconic Greek island paradise', 'Greece', 'Santorini', TRUE, 'active')
    ");
    
    // Insert tour categories
    $pdo->exec("
        INSERT INTO tour_categories (name, slug, description) VALUES
        ('Adventure Tours', 'adventure-tours', 'Thrilling adventures and outdoor activities'),
        ('Cultural Tours', 'cultural-tours', 'Explore local culture and heritage'),
        ('Beach Tours', 'beach-tours', 'Relaxing beach destinations'),
        ('City Tours', 'city-tours', 'Urban exploration and city sightseeing'),
        ('Romantic Tours', 'romantic-tours', 'Perfect getaways for couples'),
        ('Luxury Tours', 'luxury-tours', 'Premium travel experiences')
    ");
    
    // Insert tours
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Dubai Desert Safari & City Tour', 'dubai-desert-safari-city-tour', 1, 'Experience the best of Dubai with our comprehensive tour package. Start your adventure with a thrilling desert safari including dune bashing, camel riding, and a traditional BBQ dinner under the stars. Explore Dubai''s modern landmarks including Burj Khalifa, Dubai Mall, and the stunning Palm Jumeirah. This tour perfectly combines adventure, culture, and luxury for an unforgettable Dubai experience.', 'Ultimate Dubai adventure with desert safari and city highlights', 74999, 66499, 4, 3, 8, 'assets/images/tours/dubai-tour.jpg', '[\"Hotel pickup and drop-off\", \"Professional tour guide\", \"Desert safari with BBQ dinner\", \"Burj Khalifa tickets\", \"Dubai Mall visit\", \"All transportation\"]', '[\"International flights\", \"Personal expenses\", \"Optional activities\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Dubai Mall\", \"description\": \"Airport pickup, hotel check-in, visit Dubai Mall and Burj Khalifa\"}, {\"day\": 2, \"title\": \"Desert Safari\", \"description\": \"Full day desert safari with dune bashing, camel riding, and BBQ dinner\"}, {\"day\": 3, \"title\": \"City Tour\", \"description\": \"Visit Palm Jumeirah, Dubai Marina, and traditional souks\"}, {\"day\": 4, \"title\": \"Departure\", \"description\": \"Hotel checkout and airport transfer\"}]', 'easy', 'city', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Dubai Desert Safari & City Tour - 4 Days', 'Experience Dubai with desert safari, city tour, and luxury attractions')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Paris Romantic Getaway', 'paris-romantic-getaway', 2, 'Fall in love with the City of Light on this romantic 5-day Paris adventure. Visit iconic landmarks like the Eiffel Tower, Louvre Museum, and Notre-Dame Cathedral. Enjoy Seine River cruises, charming café visits, and strolls through Montmartre. This tour includes skip-the-line tickets to major attractions and a romantic dinner cruise. Perfect for couples seeking an unforgettable Parisian experience.', 'Romantic 5-day Paris tour with iconic landmarks and Seine cruise', 1299.00, 1199.00, 5, 4, 6, 'assets/images/tours/paris-tour.jpg', '[\"4-star hotel accommodation\", \"Daily breakfast\", \"Skip-the-line museum tickets\", \"Seine River dinner cruise\", \"Professional guide\", \"Airport transfers\"]', '[\"International flights\", \"Lunch and dinner (except cruise)\", \"Personal shopping\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Eiffel Tower\", \"description\": \"Airport pickup, hotel check-in, evening Eiffel Tower visit\"}, {\"day\": 2, \"title\": \"Louvre & Seine Cruise\", \"description\": \"Morning at Louvre Museum, afternoon Seine River cruise\"}, {\"day\": 3, \"title\": \"Montmartre & Sacré-Cœur\", \"description\": \"Explore artistic Montmartre district and Sacré-Cœur Basilica\"}, {\"day\": 4, \"title\": \"Versailles Day Trip\", \"description\": \"Full day excursion to Palace of Versailles\"}, {\"day\": 5, \"title\": \"Departure\", \"description\": \"Last-minute shopping and airport transfer\"}]', 'easy', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Paris Romantic Getaway - 5 Days', 'Romantic Paris tour with Eiffel Tower, Louvre, and Seine cruise')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Bali Cultural & Beach Experience', 'bali-cultural-beach-experience', 3, 'Discover the magic of Bali with this perfect blend of cultural immersion and beach relaxation. Visit ancient temples like Tanah Lot and Uluwatu, experience traditional Balinese ceremonies, and explore the stunning Tegallalang Rice Terraces. Relax on pristine beaches in Seminyak and Kuta, enjoy world-class spa treatments, and savor authentic Indonesian cuisine. This tour offers the complete Bali experience.', 'Perfect Bali experience combining culture, temples, and beautiful beaches', 799.00, 699.00, 6, 5, 10, 'assets/images/tours/bali-tour.jpg', '[\"Boutique hotel accommodation\", \"Daily breakfast\", \"Temple entrance fees\", \"Cultural performances\", \"Spa treatment\", \"Airport transfers\", \"Professional guide\"]', '[\"International flights\", \"Lunch and dinner\", \"Personal activities\", \"Travel insurance\", \"Optional excursions\"]', '[{\"day\": 1, \"title\": \"Arrival & Ubud\", \"description\": \"Airport pickup, transfer to Ubud, visit Monkey Forest Sanctuary\"}, {\"day\": 2, \"title\": \"Rice Terraces & Temples\", \"description\": \"Visit Tegallalang Rice Terraces and Tirta Empul Temple\"}, {\"day\": 3, \"title\": \"Tanah Lot Sunset\", \"description\": \"Explore Tanah Lot Temple and enjoy spectacular sunset\"}, {\"day\": 4, \"title\": \"Beach Day Seminyak\", \"description\": \"Transfer to Seminyak, beach relaxation and spa treatment\"}, {\"day\": 5, \"title\": \"Uluwatu & Kecak Dance\", \"description\": \"Visit Uluwatu Temple and watch traditional Kecak dance\"}, {\"day\": 6, \"title\": \"Departure\", \"description\": \"Last-minute shopping and airport transfer\"}]', 'moderate', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Bali Cultural & Beach Experience - 6 Days', 'Bali tour with temples, rice terraces, and beach relaxation')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Tokyo Modern & Traditional', 'tokyo-modern-traditional', 4, 'Immerse yourself in the fascinating contrasts of Tokyo, where ultra-modern skyscrapers stand alongside ancient temples. Experience the bustling energy of Shibuya Crossing, the tranquility of Senso-ji Temple, and the culinary delights of Tsukiji Fish Market. This tour includes visits to traditional districts like Asakusa, modern areas like Harajuku, and day trips to nearby Mt. Fuji and Kamakura.', 'Explore Tokyo blend of modern innovation and ancient traditions', 1599.00, 1399.00, 7, 6, 8, 'assets/images/tours/tokyo-tour.jpg', '[\"4-star hotel accommodation\", \"Daily breakfast\", \"JR Pass for transportation\", \"Mt. Fuji day trip\", \"Traditional tea ceremony\", \"Professional English guide\"]', '[\"International flights\", \"Lunch and dinner\", \"Personal shopping\", \"Travel insurance\", \"Optional activities\"]', '[{\"day\": 1, \"title\": \"Arrival & Asakusa\", \"description\": \"Airport pickup, hotel check-in, explore traditional Asakusa district\"}, {\"day\": 2, \"title\": \"Shibuya & Harajuku\", \"description\": \"Experience modern Tokyo in Shibuya and trendy Harajuku\"}, {\"day\": 3, \"title\": \"Tsukiji & Imperial Palace\", \"description\": \"Early morning fish market visit and Imperial Palace gardens\"}, {\"day\": 4, \"title\": \"Mt. Fuji Day Trip\", \"description\": \"Full day excursion to iconic Mt. Fuji and Lake Kawaguchi\"}, {\"day\": 5, \"title\": \"Kamakura Ancient Capital\", \"description\": \"Visit historic Kamakura with Great Buddha statue\"}, {\"day\": 6, \"title\": \"Traditional Culture\", \"description\": \"Tea ceremony experience and traditional craft workshops\"}, {\"day\": 7, \"title\": \"Departure\", \"description\": \"Last-minute shopping in Ginza and airport transfer\"}]', 'moderate', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Tokyo Modern & Traditional - 7 Days', 'Tokyo tour with modern attractions and traditional culture')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Santorini Sunset Paradise', 'santorini-sunset-paradise', 5, 'Experience the breathtaking beauty of Santorini with this romantic Greek island getaway. Watch world-famous sunsets from Oia, explore the charming villages of Fira and Imerovigli, and relax on unique volcanic beaches. Enjoy wine tasting at local vineyards, take boat trips to volcanic islands, and indulge in delicious Greek cuisine. This tour captures the essence of Greek island paradise.', 'Romantic Santorini tour with stunning sunsets and Greek island charm', 999.00, 899.00, 4, 3, 6, 'assets/images/tours/santorini-tour.jpg', '[\"Boutique hotel with caldera view\", \"Daily breakfast\", \"Wine tasting tour\", \"Volcano boat trip\", \"Sunset dinner in Oia\", \"Airport transfers\"]', '[\"International flights\", \"Lunch and dinner (except included)\", \"Personal expenses\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Fira\", \"description\": \"Airport pickup, hotel check-in, explore Fira town\"}, {\"day\": 2, \"title\": \"Oia Sunset\", \"description\": \"Visit picturesque Oia village for world-famous sunset\"}, {\"day\": 3, \"title\": \"Volcano & Hot Springs\", \"description\": \"Boat trip to volcanic islands and natural hot springs\"}, {\"day\": 4, \"title\": \"Wine Tasting & Departure\", \"description\": \"Morning wine tasting tour and airport transfer\"}]', 'easy', 'beach', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Santorini Sunset Paradise - 4 Days', 'Santorini tour with sunsets, wine tasting, and volcanic islands')
    ");
    
    // Insert blog categories
    $pdo->exec("
        INSERT INTO blog_categories (name, slug, description) VALUES
        ('Travel Tips', 'travel-tips', 'Helpful tips and advice for travelers'),
        ('Destinations', 'destinations', 'Featured destinations and travel guides'),
        ('Adventure', 'adventure', 'Adventure travel stories and guides'),
        ('Culture & Food', 'culture-food', 'Cultural insights and culinary experiences')
    ");
    
    // Insert sample blog posts
    $pdo->exec("
        INSERT INTO blog_posts (title, slug, category_id, author_id, excerpt, content, featured_image, tags, featured, status, published_at) VALUES
        ('10 Essential Travel Tips for First-Time Visitors to Asia', '10-essential-travel-tips-asia', 1, 1, 'Planning your first trip to Asia? Here are the essential tips you need to know for a smooth and memorable journey.', 
         'Asia is a diverse continent with rich cultures, delicious cuisines, and breathtaking landscapes. For first-time visitors, it can be overwhelming to plan the perfect trip. Here are 10 essential tips to help you navigate your Asian adventure:\n\n1. **Research visa requirements early** - Each Asian country has different visa policies\n2. **Pack light and smart** - You will want space for souvenirs\n3. **Learn basic local phrases** - It shows respect and helps with navigation\n4. **Try street food** - Some of the best meals come from local vendors\n5. **Respect local customs** - Dress appropriately and follow cultural norms\n6. **Stay hydrated** - The climate can be quite different from home\n7. **Have backup payment methods** - Not all places accept cards\n8. **Download offline maps** - Internet might not be available everywhere\n9. **Pack a good travel insurance** - Better safe than sorry\n10. **Be open to new experiences** - The unexpected often becomes the highlight', 
         'assets/images/blog/asia-travel-tips.jpg', '[\"travel tips\", \"asia\", \"first time\"]', 1, 'published', NOW()),
        
        ('Hidden Gems of Bali: Beyond the Tourist Trail', 'hidden-gems-bali-beyond-tourist-trail', 2, 1, 'Discover the secret spots in Bali that most tourists never see. From secluded beaches to traditional villages.', 
         'Bali is known for its popular destinations like Kuta Beach and Ubud, but the island has so much more to offer beyond the beaten path. Here are some hidden gems that will give you an authentic Balinese experience:\n\n**Sekumpul Waterfall** - Often called the most beautiful waterfall in Bali, Sekumpul requires a bit of trekking but rewards visitors with stunning cascades surrounded by lush jungle.\n\n**Jatiluwih Rice Terraces** - While Tegallalang gets all the attention, Jatiluwih offers more spectacular and less crowded rice terraces.\n\n**Nusa Penida** - This neighboring island boasts dramatic cliffs, crystal-clear waters, and fewer crowds than mainland Bali.\n\n**Munduk Village** - A peaceful mountain village perfect for those seeking cool weather and coffee plantations.\n\n**Virgin Beach (Pantai Virgin)** - A pristine white sand beach on the east coast with calm waters perfect for swimming.', 
         'assets/images/blog/bali-hidden-gems.jpg', '[\"bali\", \"hidden gems\", \"indonesia\", \"travel\"]', 1, 'published', NOW()),
        
        ('The Ultimate Dubai Food Guide: Where to Eat Like a Local', 'ultimate-dubai-food-guide', 4, 1, 'From street food to fine dining, discover the best places to experience Dubai authentic culinary scene.', 
         'Dubai food scene is a melting pot of flavors from around the world, but to truly experience the city, you need to know where the locals eat. Here your ultimate guide:\n\n**Al Dhiyafah Road** - The heart of Dubai street food scene. Try the famous shawarma at Al Mallah or Lebanese cuisine at Automatic.\n\n**Dubai Marina Walk** - Perfect for waterfront dining with international options.\n\n**Jumeirah Beach Road** - Mix of beachside cafes and upscale restaurants.\n\n**Old Dubai (Deira)** - For the most authentic experience, visit the spice souks and try traditional Emirati dishes.\n\n**Food Trucks at Last Exit** - A unique outdoor food court experience in the desert.\n\n**Must-try dishes:**\n- Machboos (spiced rice with meat)\n- Luqaimat (sweet dumplings)\n- Khanfaroosh (traditional pastry)\n- Fresh dates and Arabic coffee', 
         'assets/images/blog/dubai-food-guide.jpg', '[\"dubai\", \"food\", \"restaurants\", \"local cuisine\"]', 0, 'published', NOW())
    ");
    
    // Link tours to categories
    $pdo->exec("
        INSERT INTO tour_category_relations (tour_id, category_id) VALUES
        (1, 4), (1, 1),
        (2, 5), (2, 3),
        (3, 3), (3, 2),
        (4, 2), (4, 4),
        (5, 5), (5, 3)
    ");
    
    echo "<p>✅ Sample data inserted</p>";
    
    // Get counts
    $tourCount = $pdo->query("SELECT COUNT(*) FROM tours WHERE status = 'active'")->fetchColumn();
    $destinationCount = $pdo->query("SELECT COUNT(*) FROM destinations WHERE status = 'active'")->fetchColumn();
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 20px; margin: 20px 0;'>";
    echo "<h3>🎉 Installation Complete!</h3>";
    echo "<ul>";
    echo "<li>✅ $tourCount Tours created</li>";
    echo "<li>✅ $destinationCount Destinations created</li>";
    echo "<li>✅ Admin user: admin / password</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>🌐 Access Your Site:</h3>";
    echo "<p><strong>Main Website:</strong> <a href='index.php' target='_blank'>http://localhost/tour/</a></p>";
    echo "<p><strong>Tours Page:</strong> <a href='tours.php' target='_blank'>http://localhost/tour/tours.php</a></p>";
    echo "<p><strong>Admin Panel:</strong> <a href='admin/login.php' target='_blank'>http://localhost/tour/admin/login.php</a></p>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;'>";
    echo "<p><strong>📝 Note:</strong> Delete this install.php file for security.</p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 15px;'>";
    echo "<h3>❌ Installation Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
