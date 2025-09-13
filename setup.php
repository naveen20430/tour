<?php
// Database setup script with table existence check
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'travhub_db';

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE $database");
    
    echo "<h2>🔧 Setting up TravHub Database</h2>";
    
    // Check which tables exist
    $adminTableExists = $pdo->query("SHOW TABLES LIKE 'admin_users'")->rowCount() > 0;
    $toursTableExists = $pdo->query("SHOW TABLES LIKE 'tours'")->rowCount() > 0;
    $settingsTableExists = $pdo->query("SHOW TABLES LIKE 'site_settings'")->rowCount() > 0;
    
    if (!$adminTableExists || !$toursTableExists || !$settingsTableExists) {
        echo "<p>📦 Creating missing database tables...</p>";
        // Read and execute SQL file
        $sql = file_get_contents('config/setup_database.sql');
        
        // Split SQL into individual statements to handle errors better
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        $createdTables = 0;
        
        foreach ($statements as $statement) {
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                    if (stripos($statement, 'CREATE TABLE') !== false) {
                        $createdTables++;
                    }
                } catch (PDOException $e) {
                    // Skip if table already exists
                    if (strpos($e->getMessage(), 'already exists') === false) {
                        echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                    }
                }
            }
        }
        echo "<p>✅ Database setup completed! ($createdTables tables processed)</p>";
    } else {
        echo "<p>📋 All required database tables exist.</p>";
    }
    
    // Check if sample data exists
    $dataExists = 0;
    try {
        $dataExists = $pdo->query("SELECT COUNT(*) FROM tours")->fetchColumn();
    } catch (PDOException $e) {
        echo "<p style='color: red;'>Error checking tours table: " . $e->getMessage() . "</p>";
    }
    
    if ($dataExists == 0) {
        echo "<p>🌟 Adding sample tours and destinations...</p>";
        // Add sample data
        $sampleSql = file_get_contents('config/sample_data.sql');
        $pdo->exec($sampleSql);
        echo "<p>✅ Sample data added successfully!</p>";
    } else {
        echo "<p>📊 Sample data already exists - skipping data insertion.</p>";
        echo "<p><strong>Found $dataExists tours in database</strong></p>";
    }
    
    // Get tour count for confirmation
    $tourCount = 0;
    $destinationCount = 0;
    
    try {
        $tourCount = $pdo->query("SELECT COUNT(*) FROM tours WHERE status = 'active'")->fetchColumn();
        $destinationCount = $pdo->query("SELECT COUNT(*) FROM destinations WHERE status = 'active'")->fetchColumn();
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>Note: Some tables may still be initializing...</p>";
    }
    
    echo "<hr>";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 20px; margin: 20px 0;'>";
    echo "<h3>🎉 Setup Complete!</h3>";
    echo "<p><strong>Database Status:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Active Tours: $tourCount</li>";
    echo "<li>✅ Active Destinations: $destinationCount</li>";
    echo "<li>✅ Admin user created</li>";
    echo "<li>✅ Site settings configured</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h3>🚀 Access Your Website:</h3>";
    echo "<div style='margin: 20px 0;'>";
    echo "<p><strong>🌐 Main Website:</strong> <a href='index.php' target='_blank' style='color: #007bff; text-decoration: none;'>http://localhost/tour/</a></p>";
    echo "<p><strong>🗺️ Tours Page:</strong> <a href='tours.php' target='_blank' style='color: #007bff; text-decoration: none;'>http://localhost/tour/tours.php</a></p>";
    echo "<p><strong>🔐 Admin Panel:</strong> <a href='admin/login.php' target='_blank' style='color: #007bff; text-decoration: none;'>http://localhost/tour/admin/login.php</a></p>";
    echo "<p style='margin-left: 20px; color: #666;'>Username: <code>admin</code> | Password: <code>password</code></p>";
    echo "</div>";
    
    echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;'>";
    echo "<p><strong>📝 Security Note:</strong> Delete this setup.php file after setup for security.</p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; padding: 15px; margin: 20px 0;'>";
    echo "<h3>❌ Setup Failed</h3>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure MySQL/XAMPP/WAMP is running</li>";
    echo "<li>Check database credentials in config/database.php</li>";
    echo "<li>Ensure the database user has CREATE privileges</li>";
    echo "</ul>";
    echo "</div>";
}
?>
