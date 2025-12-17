<?php
// Database Configuration
// For production, update these values or use environment variables
// You can also create a separate config file for production

// Check if environment variables are set (for production)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'u626536477_ab_bau';
$db_pass = getenv('DB_PASS') ?: 'Ab_bau.123';
$db_name = getenv('DB_NAME') ?: 'u626536477_ab_bau';

// For local development (XAMPP), use these defaults:
// $db_host = 'localhost';
// $db_user = 'root';
// $db_pass = '';
// $db_name = 'ab-bau';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If connection fails, stop script and show error
    // In production, you might want to log this instead of showing the error
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection failed. Please contact the administrator.");
}
?>
