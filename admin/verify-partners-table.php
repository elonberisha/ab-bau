<?php
require_once __DIR__ . '/includes/db_connect.php';

try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'partners'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        $stmt = $pdo->query("DESCRIBE partners");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h2>✓ Tabelle 'partners' existiert!</h2>";
        echo "<h3>Spalten:</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Feld</th><th>Typ</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><a href='partners.php'>Zur Partners-Verwaltung gehen</a></p>";
    } else {
        echo "<h2>✗ Tabelle 'partners' existiert nicht!</h2>";
        echo "<p><a href='create-partners-table.php'>Tabelle erstellen</a></p>";
    }
} catch(PDOException $e) {
    echo "<h2>✗ Fehler:</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

