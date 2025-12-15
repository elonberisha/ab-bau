<?php
require_once 'admin/functions.php';

// Generate fresh hash for 'admin123'
$passwordHash = password_hash('admin123', PASSWORD_DEFAULT);

$users = [
    [
        'username' => 'admin',
        'email' => 'elonberisha1999@gmail.com',
        'password_hash' => $passwordHash,
        'role' => 'admin'
    ],
    [
        'username' => 'fation',
        'email' => 'Fation.shala@hotmail.de',
        'password_hash' => $passwordHash,
        'role' => 'admin'
    ]
];

$config = [
    'users' => $users
];

// Write directly to config.json
if (writeJson('config.json', $config)) {
    echo "<h1>Sukses!</h1>";
    echo "<p>Përdoruesit u resetuan.</p>";
    echo "<ul>";
    echo "<li>User: <b>admin</b> / Pass: <b>admin123</b></li>";
    echo "<li>User: <b>fation</b> / Pass: <b>admin123</b></li>";
    echo "</ul>";
    echo "<p>Hash i ri: " . $passwordHash . "</p>";
    echo "<p><a href='admin/login.php'>Shko tek Login</a></p>";
} else {
    echo "<h1>Gabim!</h1>";
    echo "<p>Nuk u arrit të shkruhet skedari config.json. Kontrollo lejet.</p>";
}
?>
