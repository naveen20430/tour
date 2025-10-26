<?php
/**
 * API Endpoint to save tour search queries
 * Saves user search data with phone number to database
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);

// Set JSON response header
header('Content-Type: application/json');

// Include database configuration
require_once dirname(__DIR__) . '/config/config.php';

// Initialize response
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get form data
    $from = isset($_POST['from']) ? trim($_POST['from']) : null;
    $destination = isset($_POST['destination']) ? trim($_POST['destination']) : null;
    $travel_date = isset($_POST['travel_date']) ? trim($_POST['travel_date']) : null;
    $return_date = isset($_POST['return_date']) ? trim($_POST['return_date']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    
    // Validate phone number
    if (empty($phone)) {
        throw new Exception('Phone number is required');
    }
    
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        throw new Exception('Invalid phone number format');
    }
    
    // Validate dates if provided
    if ($travel_date && !empty($travel_date)) {
        $travel_date_obj = DateTime::createFromFormat('Y-m-d', $travel_date);
        if (!$travel_date_obj) {
            throw new Exception('Invalid travel date format');
        }
    }
    
    if ($return_date && !empty($return_date)) {
        $return_date_obj = DateTime::createFromFormat('Y-m-d', $return_date);
        if (!$return_date_obj) {
            throw new Exception('Invalid return date format');
        }
    }
    
    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    // Get user agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Create search_queries table if it doesn't exist
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS search_queries (
                id INT AUTO_INCREMENT PRIMARY KEY,
                from_location VARCHAR(255) NULL,
                destination_slug VARCHAR(255) NULL,
                travel_date DATE NULL,
                return_date DATE NULL,
                phone VARCHAR(20) NOT NULL,
                ip_address VARCHAR(45) NULL,
                user_agent TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (Exception $tableError) {
        // Table might already exist or creation failed, continue anyway
        error_log('Table creation warning: ' . $tableError->getMessage());
    }
    
    // Insert search query into database
    $db->query("
        INSERT INTO search_queries 
        (from_location, destination_slug, travel_date, return_date, phone, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ", [
        $from,
        $destination,
        $travel_date ?: null,
        $return_date ?: null,
        $phone,
        $ip_address,
        $user_agent
    ]);
    
    $response['success'] = true;
    $response['message'] = 'Search query saved successfully';
    $response['query_id'] = $db->lastInsertId();
    
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    $response['error_details'] = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    
    // Log error
    error_log('Search Query API Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
}

// Output JSON response
echo json_encode($response);
exit;
