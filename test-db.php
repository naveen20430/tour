<?php
// Simple database test to check connection and tables
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'config/config.php';
    
    echo "<h1>Database Test</h1>";
    
    // Test 1: Check connection
    echo "<h2>1. Database Connection</h2>";
    if (isset($db) && $db instanceof Database) {
        echo "✅ Database object created successfully<br>";
        
        // Test basic query
        try {
            $result = $db->fetch("SELECT 1 as test");
            if ($result && $result['test'] == 1) {
                echo "✅ Database connection working<br>";
            } else {
                echo "❌ Database connection test failed<br>";
            }
        } catch (Exception $e) {
            echo "❌ Database query error: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ Database object not created<br>";
    }
    
    // Test 2: Check tables
    echo "<h2>2. Table Check</h2>";
    
    $tables = ['tours', 'destinations', 'blog_posts'];
    foreach ($tables as $table) {
        try {
            $result = $db->fetch("SELECT COUNT(*) as count FROM $table LIMIT 1");
            echo "✅ Table '$table' exists with " . $result['count'] . " records<br>";
        } catch (Exception $e) {
            echo "❌ Table '$table' error: " . $e->getMessage() . "<br>";
        }
    }
    
    // Test 3: Sample search query
    echo "<h2>3. Sample Tour Search</h2>";
    try {
        $tours = $db->fetchAll("
            SELECT id, title, short_description, price 
            FROM tours 
            WHERE status = 'active' 
            AND title LIKE '%tour%' 
            LIMIT 5
        ");
        
        if (count($tours) > 0) {
            echo "✅ Found " . count($tours) . " tours:<br>";
            foreach ($tours as $tour) {
                echo "- " . htmlspecialchars($tour['title']) . " (₹" . $tour['price'] . ")<br>";
            }
        } else {
            echo "⚠️ No tours found (this is okay if you don't have sample data)<br>";
        }
    } catch (Exception $e) {
        echo "❌ Tour search error: " . $e->getMessage() . "<br>";
    }
    
    // Test 4: Check AJAX search functions
    echo "<h2>4. AJAX Search Functions Test</h2>";
    
    // Include the search functions
    $searchFile = 'ajax/search.php';
    if (file_exists($searchFile)) {
        echo "✅ AJAX search file exists<br>";
        
        // Test API call directly
        echo "<h3>Direct API Test:</h3>";
        $testUrl = "ajax/search.php?q=tour&type=all";
        echo "Test URL: <a href='$testUrl' target='_blank'>$testUrl</a><br>";
        
    } else {
        echo "❌ AJAX search file not found<br>";
    }
    
} catch (Exception $e) {
    echo "<h2>Fatal Error:</h2>";
    echo "❌ " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>