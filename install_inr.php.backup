<?php
// Direct database installation script with INR prices
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
    
    echo "<h2>Installing TravHub Database with INR Currency...</h2>";
    
    // Create tables (same as before)
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
    
    echo "<p>✅ Database tables created</p>";
    
    // Insert admin user
    $pdo->exec("
        INSERT INTO admin_users (username, email, password, full_name, role) VALUES 
        ('admin', 'admin@travhub.com', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'super_admin')
    ");
    
    // Insert site settings with Indian contact details
    $pdo->exec("
        INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
        ('site_name', 'TravHub India', 'text'),
        ('site_tagline', 'Adventure & Experience The Travel', 'text'),
        ('site_email', 'info@travhubindia.com', 'text'),
        ('site_phone', '+91 98765 43210', 'text'),
        ('site_address', 'Travel Hub Building, Connaught Place, New Delhi, India', 'textarea'),
        ('opening_hours', '9:00am - 10:00pm IST', 'text'),
        ('contact_email', 'support@travhubindia.com', 'text')
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
    
    // Insert tours with INR prices
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Dubai Desert Safari & City Tour', 'dubai-desert-safari-city-tour', 1, 'Experience the best of Dubai with our comprehensive tour package. Start your adventure with a thrilling desert safari including dune bashing, camel riding, and a traditional BBQ dinner under the stars. Explore Dubai''s modern landmarks including Burj Khalifa, Dubai Mall, and the stunning Palm Jumeirah. This tour perfectly combines adventure, culture, and luxury for an unforgettable Dubai experience.', 'Ultimate Dubai adventure with desert safari and city highlights', 74999.00, 66499.00, 4, 3, 8, 'assets/images/tours/tours-1-1.jpg', '[\"Hotel pickup and drop-off\", \"Professional tour guide\", \"Desert safari with BBQ dinner\", \"Burj Khalifa tickets\", \"Dubai Mall visit\", \"All transportation\"]', '[\"International flights\", \"Personal expenses\", \"Optional activities\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Dubai Mall\", \"description\": \"Airport pickup, hotel check-in, visit Dubai Mall and Burj Khalifa\"}, {\"day\": 2, \"title\": \"Desert Safari\", \"description\": \"Full day desert safari with dune bashing, camel riding, and BBQ dinner\"}, {\"day\": 3, \"title\": \"City Tour\", \"description\": \"Visit Palm Jumeirah, Dubai Marina, and traditional souks\"}, {\"day\": 4, \"title\": \"Departure\", \"description\": \"Hotel checkout and airport transfer\"}]', 'easy', 'city', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Dubai Desert Safari & City Tour - 4 Days', 'Experience Dubai with desert safari, city tour, and luxury attractions')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Paris Romantic Getaway', 'paris-romantic-getaway', 2, 'Fall in love with the City of Light on this romantic 5-day Paris adventure. Visit iconic landmarks like the Eiffel Tower, Louvre Museum, and Notre-Dame Cathedral. Enjoy Seine River cruises, charming café visits, and strolls through Montmartre. This tour includes skip-the-line tickets to major attractions and a romantic dinner cruise. Perfect for couples seeking an unforgettable Parisian experience.', 'Romantic 5-day Paris tour with iconic landmarks and Seine cruise', 107999.00, 99999.00, 5, 4, 6, 'assets/images/tours/tours-1-2.jpg', '[\"4-star hotel accommodation\", \"Daily breakfast\", \"Skip-the-line museum tickets\", \"Seine River dinner cruise\", \"Professional guide\", \"Airport transfers\"]', '[\"International flights\", \"Lunch and dinner (except cruise)\", \"Personal shopping\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Eiffel Tower\", \"description\": \"Airport pickup, hotel check-in, evening Eiffel Tower visit\"}, {\"day\": 2, \"title\": \"Louvre & Seine Cruise\", \"description\": \"Morning at Louvre Museum, afternoon Seine River cruise\"}, {\"day\": 3, \"title\": \"Montmartre & Sacré-Cœur\", \"description\": \"Explore artistic Montmartre district and Sacré-Cœur Basilica\"}, {\"day\": 4, \"title\": \"Versailles Day Trip\", \"description\": \"Full day excursion to Palace of Versailles\"}, {\"day\": 5, \"title\": \"Departure\", \"description\": \"Last-minute shopping and airport transfer\"}]', 'easy', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Paris Romantic Getaway - 5 Days', 'Romantic Paris tour with Eiffel Tower, Louvre, and Seine cruise')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Bali Cultural & Beach Experience', 'bali-cultural-beach-experience', 3, 'Discover the magic of Bali with this perfect blend of cultural immersion and beach relaxation. Visit ancient temples like Tanah Lot and Uluwatu, experience traditional Balinese ceremonies, and explore the stunning Tegallalang Rice Terraces. Relax on pristine beaches in Seminyak and Kuta, enjoy world-class spa treatments, and savor authentic Indonesian cuisine. This tour offers the complete Bali experience.', 'Perfect Bali experience combining culture, temples, and beautiful beaches', 66499.00, 58199.00, 6, 5, 10, 'assets/images/tours/tours-1-3.jpg', '[\"Boutique hotel accommodation\", \"Daily breakfast\", \"Temple entrance fees\", \"Cultural performances\", \"Spa treatment\", \"Airport transfers\", \"Professional guide\"]', '[\"International flights\", \"Lunch and dinner\", \"Personal activities\", \"Travel insurance\", \"Optional excursions\"]', '[{\"day\": 1, \"title\": \"Arrival & Ubud\", \"description\": \"Airport pickup, transfer to Ubud, visit Monkey Forest Sanctuary\"}, {\"day\": 2, \"title\": \"Rice Terraces & Temples\", \"description\": \"Visit Tegallalang Rice Terraces and Tirta Empul Temple\"}, {\"day\": 3, \"title\": \"Tanah Lot Sunset\", \"description\": \"Explore Tanah Lot Temple and enjoy spectacular sunset\"}, {\"day\": 4, \"title\": \"Beach Day Seminyak\", \"description\": \"Transfer to Seminyak, beach relaxation and spa treatment\"}, {\"day\": 5, \"title\": \"Uluwatu & Kecak Dance\", \"description\": \"Visit Uluwatu Temple and watch traditional Kecak dance\"}, {\"day\": 6, \"title\": \"Departure\", \"description\": \"Last-minute shopping and airport transfer\"}]', 'moderate', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Bali Cultural & Beach Experience - 6 Days', 'Bali tour with temples, rice terraces, and beach relaxation')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Tokyo Modern & Traditional', 'tokyo-modern-traditional', 4, 'Immerse yourself in the fascinating contrasts of Tokyo, where ultra-modern skyscrapers stand alongside ancient temples. Experience the bustling energy of Shibuya Crossing, the tranquility of Senso-ji Temple, and the culinary delights of Tsukiji Fish Market. This tour includes visits to traditional districts like Asakusa, modern areas like Harajuku, and day trips to nearby Mt. Fuji and Kamakura.', 'Explore Tokyo blend of modern innovation and ancient traditions', 132999.00, 116499.00, 7, 6, 8, 'assets/images/tours/tours-1-4.jpg', '[\"4-star hotel accommodation\", \"Daily breakfast\", \"JR Pass for transportation\", \"Mt. Fuji day trip\", \"Traditional tea ceremony\", \"Professional English guide\"]', '[\"International flights\", \"Lunch and dinner\", \"Personal shopping\", \"Travel insurance\", \"Optional activities\"]', '[{\"day\": 1, \"title\": \"Arrival & Asakusa\", \"description\": \"Airport pickup, hotel check-in, explore traditional Asakusa district\"}, {\"day\": 2, \"title\": \"Shibuya & Harajuku\", \"description\": \"Experience modern Tokyo in Shibuya and trendy Harajuku\"}, {\"day\": 3, \"title\": \"Tsukiji & Imperial Palace\", \"description\": \"Early morning fish market visit and Imperial Palace gardens\"}, {\"day\": 4, \"title\": \"Mt. Fuji Day Trip\", \"description\": \"Full day excursion to iconic Mt. Fuji and Lake Kawaguchi\"}, {\"day\": 5, \"title\": \"Kamakura Ancient Capital\", \"description\": \"Visit historic Kamakura with Great Buddha statue\"}, {\"day\": 6, \"title\": \"Traditional Culture\", \"description\": \"Tea ceremony experience and traditional craft workshops\"}, {\"day\": 7, \"title\": \"Departure\", \"description\": \"Last-minute shopping in Ginza and airport transfer\"}]', 'moderate', 'cultural', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Tokyo Modern & Traditional - 7 Days', 'Tokyo tour with modern attractions and traditional culture')
    ");
    
    $pdo->exec("
        INSERT INTO tours (title, slug, destination_id, description, short_description, price, discount_price, duration_days, duration_nights, max_people, featured_image, inclusions, exclusions, itinerary, difficulty_level, tour_type, featured, popular, status, availability_start, availability_end, meta_title, meta_description) VALUES
        ('Santorini Sunset Paradise', 'santorini-sunset-paradise', 5, 'Experience the breathtaking beauty of Santorini with this romantic Greek island getaway. Watch world-famous sunsets from Oia, explore the charming villages of Fira and Imerovigli, and relax on unique volcanic beaches. Enjoy wine tasting at local vineyards, take boat trips to volcanic islands, and indulge in delicious Greek cuisine. This tour captures the essence of Greek island paradise.', 'Romantic Santorini tour with stunning sunsets and Greek island charm', 83199.00, 74999.00, 4, 3, 6, 'assets/images/tours/tours-1-5.jpg', '[\"Boutique hotel with caldera view\", \"Daily breakfast\", \"Wine tasting tour\", \"Volcano boat trip\", \"Sunset dinner in Oia\", \"Airport transfers\"]', '[\"International flights\", \"Lunch and dinner (except included)\", \"Personal expenses\", \"Travel insurance\"]', '[{\"day\": 1, \"title\": \"Arrival & Fira\", \"description\": \"Airport pickup, hotel check-in, explore Fira town\"}, {\"day\": 2, \"title\": \"Oia Sunset\", \"description\": \"Visit picturesque Oia village for world-famous sunset\"}, {\"day\": 3, \"title\": \"Volcano & Hot Springs\", \"description\": \"Boat trip to volcanic islands and natural hot springs\"}, {\"day\": 4, \"title\": \"Wine Tasting & Departure\", \"description\": \"Morning wine tasting tour and airport transfer\"}]', 'easy', 'beach', 1, 1, 'active', '2025-01-01', '2025-12-31', 'Santorini Sunset Paradise - 4 Days', 'Santorini tour with sunsets, wine tasting, and volcanic islands')
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
        ('10 Essential Travel Tips for First-Time International Travelers from India', '10-essential-travel-tips-india', 1, 1, 'Planning your first international trip from India? Here are the essential tips you need to know for a smooth and memorable journey.', 
         'Traveling internationally from India requires some preparation, but with the right tips, your journey can be smooth and enjoyable. Here are 10 essential tips for Indian travelers:\\n\\n1. **Check visa requirements early** - Most countries require Indians to have visas\\n2. **Get travel insurance** - Essential for international travel\\n3. **Notify your bank** - Inform them about international travel\\n4. **Pack smart** - Keep essentials in carry-on\\n5. **Learn basic local phrases** - Shows respect and helps navigation\\n6. **Download offline maps** - Internet might be expensive abroad\\n7. **Carry required documents** - Passport, visa, return tickets\\n8. **Budget for currency exchange** - Plan for local currency needs\\n9. **Research local customs** - Respect cultural differences\\n10. **Stay connected** - International roaming or local SIM options', 
         'assets/images/blog/blog-1-1.jpg', '[\"travel tips\", \"india\", \"international travel\"]', 1, 'published', NOW()),
        
        ('Budget Travel Guide: Amazing Destinations Under ₹50,000', 'budget-travel-destinations-50000', 2, 1, 'Discover incredible international destinations that you can visit from India without breaking the bank.', 
         'Traveling internationally doesn''t have to be expensive. Here are amazing destinations you can visit for under ₹50,000:\\n\\n**Thailand** - Beautiful beaches, temples, and street food. Budget: ₹35,000-45,000\\n\\n**Nepal** - Himalayan views and cultural richness. Budget: ₹25,000-35,000\\n\\n**Sri Lanka** - Tropical paradise close to home. Budget: ₹30,000-40,000\\n\\n**Dubai** - Luxury experiences at budget prices. Budget: ₹40,000-50,000\\n\\n**Singapore** - Modern city with great food. Budget: ₹45,000-50,000\\n\\nTips for budget travel:\\n- Travel during off-season\\n- Book flights in advance\\n- Stay in hostels or budget hotels\\n- Use public transportation\\n- Eat local street food', 
         'assets/images/blog/blog-1-2.jpg', '[\"budget travel\", \"destinations\", \"india travel\"]', 1, 'published', NOW()),
        
        ('Indian Food Guide for International Travelers', 'indian-food-guide-international', 4, 1, 'What to expect and how to find authentic Indian food while traveling abroad.', 
         'Missing Indian food while traveling? Here''s your guide to finding authentic Indian cuisine abroad:\\n\\n**Major cities with great Indian food:**\\n- London: Brick Lane for authentic curry\\n- New York: Jackson Heights for regional varieties\\n- Singapore: Little India district\\n- Dubai: Karama area\\n\\n**What to look for:**\\n- Restaurants run by Indian families\\n- Places popular with local Indian communities\\n- Authentic spice levels and ingredients\\n\\n**Tips:**\\n- Carry some Indian spices and instant foods\\n- Download apps like Zomato for Indian restaurants\\n- Join local Indian community groups on social media\\n- Try Indian grocery stores in major cities', 
         'assets/images/blog/blog-1-3.jpg', '[\"indian food\", \"travel\", \"cuisine\"]', 0, 'published', NOW())
    ");
    
    // Link tours to categories
    $pdo->exec("
        INSERT INTO tour_category_relations (tour_id, category_id) VALUES
        (1, 4), (1, 1),
        (2, 5), (2, 2),
        (3, 2), (3, 3),
        (4, 2), (4, 4),
        (5, 5), (5, 3)
    ");
    
    echo "<p>✅ Sample data inserted with INR prices</p>";
    
    // Get counts
    $tourCount = $pdo->query("SELECT COUNT(*) FROM tours WHERE status = 'active'")->fetchColumn();
    $destinationCount = $pdo->query("SELECT COUNT(*) FROM destinations WHERE status = 'active'")->fetchColumn();
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 20px; margin: 20px 0;'>";
    echo "<h3>🎉 Installation Complete with INR Currency!</h3>";
    echo "<ul>";
    echo "<li>✅ $tourCount Tours created with INR pricing</li>";
    echo "<li>✅ $destinationCount Destinations created</li>";
    echo "<li>✅ Admin user: admin / password</li>";
    echo "<li>✅ Currency: Indian Rupees (₹)</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>💰 Sample INR Tour Prices:</h3>";
    echo "<ul>";
    echo "<li>Dubai Tour: ₹74,999 (was ₹66,499 with discount)</li>";
    echo "<li>Paris Tour: ₹1,07,999 (was ₹99,999 with discount)</li>";
    echo "<li>Bali Tour: ₹66,499 (was ₹58,199 with discount)</li>";
    echo "<li>Tokyo Tour: ₹1,32,999 (was ₹1,16,499 with discount)</li>";
    echo "<li>Santorini Tour: ₹83,199 (was ₹74,999 with discount)</li>";
    echo "</ul>";
    
    echo "<h3>🌐 Access Your Site:</h3>";
    echo "<p><strong>Main Website:</strong> <a href='index.php' target='_blank'>http://localhost/tour/</a></p>";
    echo "<p><strong>Tours Page:</strong> <a href='tours.php' target='_blank'>http://localhost/tour/tours.php</a></p>";
    echo "<p><strong>Admin Panel:</strong> <a href='admin/login.php' target='_blank'>http://localhost/tour/admin/login.php</a></p>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;'>";
    echo "<p><strong>📝 Note:</strong> Delete this install_inr.php file for security after installation.</p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 15px;'>";
    echo "<h3>❌ Installation Failed</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
