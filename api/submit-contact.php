<?php
require_once __DIR__ . '/../admin/includes/email_config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Alle Pflichtfelder müssen ausgefüllt werden.']);
    exit;
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige E-Mail-Adresse.']);
    exit;
}

// Sanitize input
$name = htmlspecialchars(trim($data['name']), ENT_QUOTES, 'UTF-8');
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($data['phone']) ? htmlspecialchars(trim($data['phone']), ENT_QUOTES, 'UTF-8') : '';
$service = isset($data['service']) ? htmlspecialchars(trim($data['service']), ENT_QUOTES, 'UTF-8') : '';
$message = htmlspecialchars(trim($data['message']), ENT_QUOTES, 'UTF-8');

// Email configuration
$to = 'anduena@ab-bau-fliesen.de';
$subject = 'Neue Kontaktanfrage von ' . $name;

// Build email body
$emailBody = "Neue Kontaktanfrage von der Website\n\n";
$emailBody .= "Name: " . $name . "\n";
$emailBody .= "E-Mail: " . $email . "\n";
if (!empty($phone)) {
    $emailBody .= "Telefon: " . $phone . "\n";
}
if (!empty($service)) {
    $emailBody .= "Gewünschte Leistung: " . $service . "\n";
}
$emailBody .= "\nNachricht:\n" . $message . "\n";
$emailBody .= "\n---\n";
$emailBody .= "Diese E-Mail wurde automatisch von der Website ab-bau-fliesen.de gesendet.\n";
$emailBody .= "Antworten Sie direkt an: " . $email . "\n";

// Send email using SMTP
$mailSent = sendEmail(CONTACT_EMAIL, $subject, $emailBody, $email, $name);

if ($mailSent) {
    // Also send a copy to the sender (optional)
    $confirmationSubject = 'Ihre Anfrage bei AB Bau Fliesen';
    $confirmationBody = "Hallo " . $name . ",\n\n";
    $confirmationBody .= "vielen Dank für Ihre Anfrage. Wir haben Ihre Nachricht erhalten und werden uns in Kürze bei Ihnen melden.\n\n";
    $confirmationBody .= "Ihre Nachricht:\n" . $message . "\n\n";
    $confirmationBody .= "Mit freundlichen Grüßen\n";
    $confirmationBody .= "AB Bau Fliesen\n";
    $confirmationBody .= "Talstraße 3d, 85238 Petershausen\n";
    $confirmationBody .= "Tel: 08137 9957477\n";
    $confirmationBody .= "E-Mail: anduena@ab-bau-fliesen.de\n";
    
    // Send confirmation email to sender
    sendEmail($email, $confirmationSubject, $confirmationBody, CONTACT_EMAIL, 'AB Bau Fliesen');
    
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank! Ihre Nachricht wurde erfolgreich gesendet. Wir werden uns bald bei Ihnen melden.'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Es ist ein Fehler beim Senden der E-Mail aufgetreten. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt unter anduena@ab-bau-fliesen.de'
    ]);
}
?>

