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
// Convert line breaks to <br> for HTML
$messageHtml = nl2br($message);

// Email configuration
$to = 'anduena@ab-bau-fliesen.de';
$subject = '📧 Neue Kontaktanfrage von ' . $name;

// Build HTML email template
$emailBodyHTML = '
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .email-content {
            padding: 30px 20px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #2563eb;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #374151;
            width: 180px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
            flex: 1;
        }
        .message-box {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .message-box h3 {
            margin: 0 0 15px 0;
            color: #1f2937;
            font-size: 18px;
            font-weight: 600;
        }
        .message-content {
            color: #4b5563;
            line-height: 1.8;
            white-space: pre-wrap;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
        .reply-button {
            display: inline-block;
            background-color: #2563eb;
            color: #ffffff !important;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin: 20px 0;
        }
        .badge {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>📧 Neue Kontaktanfrage</h1>
            <p>Von der Website ab-bau-fliesen.de</p>
        </div>
        
        <div class="email-content">
            <div class="info-box">
                <div class="info-row">
                    <div class="info-label">👤 Name:</div>
                    <div class="info-value">' . $name . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">✉️ E-Mail:</div>
                    <div class="info-value"><a href="mailto:' . $email . '" style="color: #2563eb; text-decoration: none;">' . $email . '</a></div>
                </div>' . 
                (!empty($phone) ? '
                <div class="info-row">
                    <div class="info-label">📞 Telefon:</div>
                    <div class="info-value"><a href="tel:' . preg_replace('/[^0-9+]/', '', $phone) . '" style="color: #2563eb; text-decoration: none;">' . $phone . '</a></div>
                </div>' : '') . 
                (!empty($service) ? '
                <div class="info-row">
                    <div class="info-label">🏗️ Gewünschte Leistung:</div>
                    <div class="info-value"><span class="badge">' . $service . '</span></div>
                </div>' : '') . '
            </div>
            
            <div class="message-box">
                <h3>💬 Nachricht:</h3>
                <div class="message-content">' . $messageHtml . '</div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="mailto:' . $email . '?subject=Re: Ihre Anfrage bei AB Bau Fliesen" class="reply-button">📬 Direkt antworten</a>
            </div>
        </div>
        
        <div class="email-footer">
            <p><strong>AB Bau - Bau und Fliesen GmbH</strong></p>
            <p>Talstraße 3d, 85238 Petershausen</p>
            <p>Tel: 08137 9957477 | E-Mail: anduena@ab-bau-fliesen.de</p>
            <p style="margin-top: 15px; color: #9ca3af;">Diese E-Mail wurde automatisch von der Website generiert.</p>
        </div>
    </div>
</body>
</html>';

// Send email using SMTP with HTML
$mailSent = sendEmail(CONTACT_EMAIL, $subject, $emailBodyHTML, $email, $name, false, true);

if ($mailSent) {
    // Also send a confirmation email to the sender
    $confirmationSubject = '✅ Ihre Anfrage bei AB Bau Fliesen';
    $confirmationBodyHTML = '
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
        }
        .email-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-content {
            padding: 30px 20px;
        }
        .success-icon {
            text-align: center;
            font-size: 48px;
            margin: 20px 0;
        }
        .message-box {
            background-color: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>✅ Vielen Dank für Ihre Anfrage!</h1>
        </div>
        
        <div class="email-content">
            <div class="success-icon">✓</div>
            
            <p>Hallo ' . $name . ',</p>
            
            <p>vielen Dank für Ihre Anfrage. Wir haben Ihre Nachricht erhalten und werden uns <strong>in Kürze bei Ihnen melden</strong>.</p>
            
            <div class="message-box">
                <p style="margin: 0; color: #166534;"><strong>Ihre Nachricht:</strong></p>
                <p style="margin: 10px 0 0 0; color: #4b5563;">' . $messageHtml . '</p>
            </div>
            
            <p>Falls Sie dringende Fragen haben, können Sie uns auch direkt kontaktieren:</p>
            
            <p style="margin: 20px 0;">
                <strong>AB Bau - Bau und Fliesen GmbH</strong><br>
                Talstraße 3d, 85238 Petershausen<br>
                Tel: <a href="tel:081379957477" style="color: #2563eb;">08137 9957477</a><br>
                E-Mail: <a href="mailto:anduena@ab-bau-fliesen.de" style="color: #2563eb;">anduena@ab-bau-fliesen.de</a>
            </p>
            
            <p>Mit freundlichen Grüßen,<br>
            <strong>Ihr Team von AB Bau Fliesen</strong></p>
        </div>
        
        <div class="email-footer">
            <p>Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht direkt auf diese E-Mail.</p>
        </div>
    </div>
</body>
</html>';
    
    // Send confirmation email to sender
    sendEmail($email, $confirmationSubject, $confirmationBodyHTML, CONTACT_EMAIL, 'AB Bau Fliesen', false, true);
    
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

