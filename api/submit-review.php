<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../admin/includes/db_connect.php';

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$name = trim($_POST['name'] ?? '');
$message = trim($_POST['message'] ?? '');
$rating = (int)($_POST['rating'] ?? 5);

// Validation
if (empty($name) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle Pflichtfelder aus.']);
    exit;
}

if ($rating < 1 || $rating > 5) {
    $rating = 5;
}

try {
    // Insert into database with status 'pending'
    $stmt = $pdo->prepare("INSERT INTO reviews (name, message, rating, status, date) VALUES (:name, :message, :rating, 'pending', NOW())");
    $result = $stmt->execute([
        'name' => htmlspecialchars($name),
        'message' => htmlspecialchars($message),
        'rating' => $rating
    ]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>