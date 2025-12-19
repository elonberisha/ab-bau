<?php
/**
 * Script to create the portfolio_section table
 * Run this file once to create the portfolio_section table in the database
 */

require_once __DIR__ . '/includes/db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS `portfolio_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hero_image` varchar(500) DEFAULT NULL,
  `show_in_index` tinyint(1) DEFAULT 1,
  `max_items_index` int(11) DEFAULT 6,
  `index_title` varchar(255) DEFAULT NULL,
  `index_description` text DEFAULT NULL,
  `full_title` varchar(255) DEFAULT NULL,
  `full_description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $pdo->exec($sql);
    echo "<h2>✓ Tabelle 'portfolio_section' wurde erfolgreich erstellt!</h2>";
    echo "<p>Die Tabelle ist jetzt bereit für die Verwendung.</p>";
    echo "<p><a href='projekte.php'>Zur Projekte-Verwaltung gehen</a></p>";
} catch(PDOException $e) {
    echo "<h2>✗ Fehler beim Erstellen der Tabelle:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

