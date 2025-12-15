<?php
// Database Configuration
$db_host = 'localhost';
$db_user = 'root';     // Default XAMPP user
$db_pass = '';         // Default XAMPP password (empty)
$db_name = 'ab-bau';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // If connection fails, stop script and show error
    die("Lidhja me databazën dështoi: " . $e->getMessage());
}
?>
