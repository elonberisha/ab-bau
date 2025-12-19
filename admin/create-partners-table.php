<?php
/**
 * Script to create the partners table
 * Run this file once to create the partners table in the database
 */

require_once __DIR__ . '/includes/db_connect.php';

$sql = "CREATE TABLE IF NOT EXISTS `partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `active` (`active`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $pdo->exec($sql);
    echo "<h2>✓ Tabelle 'partners' wurde erfolgreich erstellt!</h2>";
    echo "<p>Die Tabelle ist jetzt bereit für die Verwendung.</p>";
    echo "<p><a href='partners.php'>Zur Partners-Verwaltung gehen</a></p>";
} catch(PDOException $e) {
    echo "<h2>✗ Fehler beim Erstellen der Tabelle:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

