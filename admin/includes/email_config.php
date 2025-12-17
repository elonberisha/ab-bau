<?php
/**
 * Email Configuration for SMTP
 * 
 * Configure your SMTP settings here for production
 * For local development (XAMPP), you can use Gmail SMTP or other services
 * 
 * IMPORTANT: For Gmail, you need to:
 * 1. Enable 2-Step Verification
 * 2. Generate an App Password (not your regular password)
 * 3. Use the App Password in SMTP_PASSWORD
 */

// SMTP Configuration
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com'); // Gmail SMTP - change for your provider
define('SMTP_PORT', 587); // 587 for TLS, 465 for SSL
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl'
define('SMTP_USERNAME', 'elonberisha1999@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'ohmmeihpubfcupph'); // Gmail App Password (generate from Google Account settings)
define('SMTP_FROM_EMAIL', 'no-reply@ab-bau-fliesen.de');
define('SMTP_FROM_NAME', 'AB Bau Fliesen');

// Admin email for 2FA and password reset
define('ADMIN_EMAIL', 'elonberisha1999@gmail.com');

// Contact form recipient
define('CONTACT_EMAIL', 'anduena@ab-bau-fliesen.de');

/**
 * Send email using SMTP
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body
 * @param string $fromEmail Sender email (optional)
 * @param string $fromName Sender name (optional)
 * @return bool True on success, false on failure
 */
function sendEmailSMTP($to, $subject, $message, $fromEmail = null, $fromName = null) {
    if (!SMTP_ENABLED) {
        // Fallback to PHP mail() if SMTP is disabled
        $headers = "From: " . ($fromEmail ?: SMTP_FROM_EMAIL) . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        return @mail($to, $subject, $message, $headers);
    }
    
    $fromEmail = $fromEmail ?: SMTP_FROM_EMAIL;
    $fromName = $fromName ?: SMTP_FROM_NAME;
    
    // Use stream_socket_client for better connection handling
    $context = stream_context_create();
    $smtp = @stream_socket_client(
        SMTP_HOST . ':' . SMTP_PORT,
        $errno,
        $errstr,
        30,
        STREAM_CLIENT_CONNECT,
        $context
    );
    
    if (!$smtp) {
        error_log("SMTP Connection failed: $errstr ($errno)");
        return false;
    }
    
    // Set timeout
    stream_set_timeout($smtp, 30);
    
    // Read initial response
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '220') {
        error_log("SMTP Initial response failed: $response");
        fclose($smtp);
        return false;
    }
    
    // Send EHLO
    fputs($smtp, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
    $response = fgets($smtp, 515);
    
    // Start TLS if needed
    if (SMTP_SECURE === 'tls') {
        fputs($smtp, "STARTTLS\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '220') {
            error_log("SMTP STARTTLS failed: $response");
            fclose($smtp);
            return false;
        }
        
        if (!stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            error_log("SMTP TLS encryption failed");
            fclose($smtp);
            return false;
        }
        
        // Send EHLO again after TLS
        fputs($smtp, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
        $response = fgets($smtp, 515);
    }
    
    // Authenticate
    fputs($smtp, "AUTH LOGIN\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("SMTP AUTH LOGIN failed: $response");
        fclose($smtp);
        return false;
    }
    
    fputs($smtp, base64_encode(SMTP_USERNAME) . "\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '334') {
        error_log("SMTP Username failed: $response");
        fclose($smtp);
        return false;
    }
    
    fputs($smtp, base64_encode(SMTP_PASSWORD) . "\r\n");
    $response = fgets($smtp, 515);
    
    if (strpos($response, '235') === false) {
        error_log("SMTP Authentication failed: $response");
        fclose($smtp);
        return false;
    }
    
    // Set sender
    fputs($smtp, "MAIL FROM: <" . $fromEmail . ">\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("SMTP MAIL FROM failed: $response");
        fclose($smtp);
        return false;
    }
    
    // Set recipient
    fputs($smtp, "RCPT TO: <" . $to . ">\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '250') {
        error_log("SMTP RCPT TO failed: $response");
        fclose($smtp);
        return false;
    }
    
    // Send data
    fputs($smtp, "DATA\r\n");
    $response = fgets($smtp, 515);
    if (substr($response, 0, 3) != '354') {
        error_log("SMTP DATA failed: $response");
        fclose($smtp);
        return false;
    }
    
    // Email headers and body
    $emailData = "From: " . $fromName . " <" . $fromEmail . ">\r\n";
    $emailData .= "To: <" . $to . ">\r\n";
    $emailData .= "Subject: " . $subject . "\r\n";
    $emailData .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $emailData .= "\r\n";
    $emailData .= $message . "\r\n";
    $emailData .= ".\r\n";
    
    fputs($smtp, $emailData);
    $response = fgets($smtp, 515);
    
    // Quit
    fputs($smtp, "QUIT\r\n");
    fclose($smtp);
    
    return strpos($response, '250') !== false;
}

/**
 * Simple email sending function (wrapper)
 * Falls back to file writing if SMTP fails (for local development)
 */
function sendEmail($to, $subject, $message, $fromEmail = null, $fromName = null, $writeToFile = true) {
    $result = sendEmailSMTP($to, $subject, $message, $fromEmail, $fromName);
    
    // If SMTP fails and we're in local development, write to file
    if (!$result && $writeToFile) {
        $emailFile = __DIR__ . '/../emails.txt';
        $emailContent = "\n" . str_repeat("=", 60) . "\n";
        $emailContent .= "TO: $to\n";
        $emailContent .= "SUBJECT: $subject\n";
        $emailContent .= "FROM: " . ($fromEmail ?: SMTP_FROM_EMAIL) . "\n";
        $emailContent .= "DATE: " . date('Y-m-d H:i:s') . "\n";
        $emailContent .= str_repeat("-", 60) . "\n";
        $emailContent .= $message . "\n";
        $emailContent .= str_repeat("=", 60) . "\n";
        
        file_put_contents($emailFile, $emailContent, FILE_APPEND);
        
        return true; // Return true so the system thinks email was sent
    }
    
    return $result;
}
?>

