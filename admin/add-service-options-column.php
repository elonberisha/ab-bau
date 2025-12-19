<?php
/**
 * Script to add service_options column to contact_section table
 * This column will store the service dropdown options as JSON
 */

require_once 'includes/db_connect.php';

try {
    // Check if column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM contact_section LIKE 'service_options'");
    $exists = $stmt->fetch();
    
    if ($exists) {
        echo "Column 'service_options' already exists in contact_section table.\n";
    } else {
        // Add the column
        $pdo->exec("ALTER TABLE contact_section ADD COLUMN service_options TEXT DEFAULT NULL AFTER form_button");
        
        // Set default options if table is empty or column is NULL
        $defaultOptions = json_encode([
            ['value' => 'keramik', 'label' => 'Keramik'],
            ['value' => 'marmor', 'label' => 'Marmor'],
            ['value' => 'granit', 'label' => 'Granit'],
            ['value' => 'beratung', 'label' => 'Beratung']
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        
        $pdo->exec("UPDATE contact_section SET service_options = " . $pdo->quote($defaultOptions) . " WHERE service_options IS NULL");
        
        echo "Column 'service_options' added successfully to contact_section table.\n";
        echo "Default options have been set.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
?>

