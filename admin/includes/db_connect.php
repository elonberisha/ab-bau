<?php
// Database Configuration
// For production, update these values or use environment variables
// You can also create a separate config file for production

// Check if environment variables are set (for production)
// If not set, use local development defaults (XAMPP)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'u626536477_ab_bau';
$db_pass = getenv('DB_PASS') ?: 'Ab_bau.123';
$db_name = getenv('DB_NAME') ?: 'u626536477_ab_bau';

// For production, set environment variables or uncomment and update these:
// $db_host = 'your_production_host';
// $db_user = 'your_production_user';
// $db_pass = 'your_production_password';
// $db_name = 'your_production_database';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If connection fails, log the error
    error_log("Database connection failed: " . $e->getMessage());
    
    // Check if we're in an API context (JSON response expected)
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
        // For API calls, return JSON error instead of die()
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Database connection failed. Please contact the administrator.']);
        exit;
    }
    
    // For regular pages, show error
    die("Database connection failed. Please contact the administrator.");
}
?>
