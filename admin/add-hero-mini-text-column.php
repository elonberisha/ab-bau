<?php
/**
 * Script to add mini_text column to hero_section table
 * Run this file once to add the mini_text column
 */

require_once __DIR__ . '/includes/db_connect.php';

try {
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM hero_section LIKE 'mini_text'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        // Add the column
        $sql = "ALTER TABLE `hero_section` ADD COLUMN `mini_text` varchar(255) DEFAULT 'PREMIUM QUALITÄT SEIT 2010' AFTER `subtitle`";
        $pdo->exec($sql);
        echo "<h2>✓ Spalte 'mini_text' wurde erfolgreich hinzugefügt!</h2>";
        echo "<p>Die Spalte ist jetzt bereit für die Verwendung.</p>";
        echo "<p><a href='hero.php'>Zur Hero-Verwaltung gehen</a></p>";
    } else {
        echo "<h2>✓ Spalte 'mini_text' existiert bereits!</h2>";
        echo "<p>Die Spalte ist bereits vorhanden.</p>";
        echo "<p><a href='hero.php'>Zur Hero-Verwaltung gehen</a></p>";
    }
} catch(PDOException $e) {
    echo "<h2>✗ Fehler beim Hinzufügen der Spalte:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

