<?php
// Start session immediately
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';
require_once 'includes/email_config.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Determine View State
$view = 'login'; // Default

// Debug: Check session state
// error_log('Session 2FA Pending: ' . (isset($_SESSION['2fa_pending']) ? 'Yes' : 'No'));

if (isset($_SESSION['2fa_pending']) && $_SESSION['2fa_pending'] === true) {
    $view = '2fa';
} elseif (isset($_GET['action']) && $_GET['action'] === 'forgot') {
    $view = 'forgot_email';
} elseif (isset($_SESSION['reset_step'])) {
    if ($_SESSION['reset_step'] === 'verify') $view = 'forgot_verify';
    if ($_SESSION['reset_step'] === 'new_pass') $view = 'forgot_new_pass';
}

$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- LOGIN ACTIONS ---
    
    // Cancel/Reset Action
    if (isset($_POST['action']) && $_POST['action'] === 'cancel') {
        session_unset(); // Clear all session data
        session_destroy(); // Destroy session
        session_start(); // Start fresh
        header('Location: login.php');
        exit;
    } 
    
    // Login Step 1: Password Verification
    elseif (isset($_POST['step']) && $_POST['step'] == 1) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        $user = verifyUserCredentials($username, $password);
        
        if ($user) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            
            $_SESSION['2fa_code'] = $otp;
            $_SESSION['2fa_expiry'] = time() + 300; // 5 minutes validity
            $_SESSION['2fa_pending'] = true;
            $_SESSION['temp_user'] = $user; // Store user temporarily
            
            // Send 2FA code to admin email (not user's email for security)
            $adminEmail = 'elonberisha1999@gmail.com';
            $userEmail = $user['email'];
            
            // Send Email to admin using SMTP
            $subject = "AB Bau Admin - Verifizierungscode für " . htmlspecialchars($user['username']);
            $msg = "Hallo,\n\nEin Login-Versuch wurde für den Benutzer '" . htmlspecialchars($user['username']) . "' (Email: " . htmlspecialchars($userEmail) . ") registriert.\n\n";
            $msg .= "Der Verifizierungscode für den Zugang zum Admin-Panel ist:\n\n" . $otp . "\n\n";
            $msg .= "Dieser Code läuft in 5 Minuten ab.\n\n";
            $msg .= "Wenn Sie sich nicht angemeldet haben, ändern Sie bitte sofort Ihr Passwort.";
            
            // Try sending email to admin using SMTP
            sendEmail($adminEmail, $subject, $msg);
            
            // Always write to file for backup/local testing
            file_put_contents(__DIR__ . '/2fa.txt', "========================================\nAB Bau Admin - Verifizierungscode 2FA\n========================================\n\nBenutzer: " . htmlspecialchars($username) . "\nCode: " . $otp . "\nDatum/Uhrzeit: " . date('d.m.Y H:i:s') . "\nGültig bis: " . date('d.m.Y H:i:s', time() + 300) . "\n\n========================================\nDieser Code läuft in 5 Minuten ab!\n========================================\n");
            
            $view = '2fa';
            $message = 'Der Verifizierungscode wurde generiert. Bitte überprüfen Sie die Datei admin/2fa.txt oder Ihre E-Mail (elonberisha1999@gmail.com).';
        } else {
            $error = 'Benutzername oder Passwort falsch!';
        }
    } 
    
    // Login Step 2: OTP Verification
    elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $userOtp = trim($_POST['otp'] ?? '');
        
        // Debugging logs
        // error_log("OTP Submitted: $userOtp");
        // error_log("Session OTP: " . ($_SESSION['2fa_code'] ?? 'Not Set'));
        
        if (isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expiry'])) {
            if (time() > $_SESSION['2fa_expiry']) {
                $error = 'Code ist abgelaufen! Bitte versuchen Sie es erneut.';
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                unset($_SESSION['temp_user']);
                $view = 'login';
            } elseif ($userOtp == $_SESSION['2fa_code']) {
                // Success - Log in the user
                $_SESSION['user_id'] = $_SESSION['temp_user']['id'];
                $_SESSION['username'] = $_SESSION['temp_user']['username'];
                $_SESSION['role'] = $_SESSION['temp_user']['role'];
                $_SESSION['admin_logged_in'] = true; // Legacy support if needed
                
                // Cleanup temp session vars
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                unset($_SESSION['temp_user']);
                
                // Important: Write session data before redirect
                session_write_close();
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Falscher Code!';
                $view = '2fa'; // Keep user on 2FA screen
            }
        } else {
            $view = 'login';
            $error = 'Sitzung ist abgelaufen. Bitte versuchen Sie es erneut.';
        }
    }

    // ... (Forgot password logic remains similar) ...
     // --- FORGOT PASSWORD ACTIONS ---

    // Send Reset Code
    elseif (isset($_POST['action']) && $_POST['action'] === 'send_reset') {
        $email = trim($_POST['email'] ?? '');
        $user = getUserByEmail($email);
        
        if ($user) {
            $otp = rand(100000, 999999);
            $_SESSION['reset_otp'] = $otp;
            $_SESSION['reset_expiry'] = time() + 300;
            $_SESSION['reset_step'] = 'verify';
            $_SESSION['reset_user'] = $user; // Store user to reset
            
            // Send reset code to admin email (not user's email for security)
            $adminEmail = 'elonberisha1999@gmail.com';
            
            $subject = "AB Bau Admin - Passwort zurücksetzen für " . htmlspecialchars($user['username']);
            $msg = "Hallo,\n\nEin Passwort-Reset wurde für den Benutzer '" . htmlspecialchars($user['username']) . "' (Email: " . htmlspecialchars($user['email']) . ") angefordert.\n\n";
            $msg .= "Der Code zum Zurücksetzen des Passworts ist:\n\n" . $otp . "\n\n";
            $msg .= "Dieser Code läuft in 5 Minuten ab.\n\n";
            $msg .= "Wenn Sie diese Anfrage nicht gestellt haben, ignorieren Sie diese E-Mail.";
            
            // Send email using SMTP
            sendEmail($adminEmail, $subject, $msg);
            file_put_contents(__DIR__ . '/2fa.txt', "========================================\nAB Bau Admin - Passwort Reset Code\n========================================\n\nBenutzer: " . htmlspecialchars($user['username']) . "\nEmail: " . htmlspecialchars($user['email']) . "\nCode: " . $otp . "\nDatum/Uhrzeit: " . date('d.m.Y H:i:s') . "\nGültig bis: " . date('d.m.Y H:i:s', time() + 300) . "\n\n========================================\nDieser Code läuft in 5 Minuten ab!\n========================================\n");
            
            $view = 'forgot_verify';
            $message = 'Der Reset-Code wurde generiert. Bitte überprüfen Sie die Datei admin/2fa.txt oder Ihre E-Mail (elonberisha1999@gmail.com).';
        } else {
            $error = 'E-Mail wurde im System nicht gefunden.';
            $view = 'forgot_email';
        }
    }

    // Verify Reset Code
    elseif (isset($_POST['action']) && $_POST['action'] === 'verify_reset') {
        $otp = trim($_POST['otp'] ?? '');
        if (isset($_SESSION['reset_otp']) && $otp == $_SESSION['reset_otp']) {
            if (time() > $_SESSION['reset_expiry']) {
                $error = 'Code ist abgelaufen.';
                $view = 'forgot_email';
                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_user']);
            } else {
                $_SESSION['reset_step'] = 'new_pass';
                $view = 'forgot_new_pass';
            }
        } else {
            $error = 'Falscher Code.';
            $view = 'forgot_verify';
        }
    }

    // Save New Password
    elseif (isset($_POST['action']) && $_POST['action'] === 'save_pass') {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        
        if (empty($pass) || strlen($pass) < 6) {
            $error = 'Das Passwort muss mindestens 6 Zeichen lang sein.';
            $view = 'forgot_new_pass';
        } elseif ($pass !== $confirm) {
            $error = 'Die Passwörter stimmen nicht überein.';
            $view = 'forgot_new_pass';
        } else {
            if (isset($_SESSION['reset_user'])) {
                $userId = $_SESSION['reset_user']['id']; // Use ID for update
                if (updateUserPassword($userId, $pass)) {
                    unset($_SESSION['reset_step']);
                    unset($_SESSION['reset_otp']);
                    unset($_SESSION['reset_expiry']);
                    unset($_SESSION['reset_user']);
                    
                    $view = 'login';
                    $message = 'Passwort wurde erfolgreich geändert. Bitte melden Sie sich an.';
                } else {
                    $error = 'Fehler beim Speichern des Passworts.';
                    $view = 'forgot_new_pass';
                }
            } else {
                $error = 'Unbekannter Fehler. Bitte versuchen Sie es erneut.';
                $view = 'login';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AB Bau</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
            20%, 40%, 60%, 80% { transform: translateX(4px); }
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, hsla(216, 85%, 65%, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(216, 85%, 65%, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, hsla(216, 85%, 65%, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, hsla(216, 85%, 65%, 0.1) 0px, transparent 50%);
            background-attachment: fixed;
        }
        .login-card {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.05), 
                0 2px 4px -1px rgba(0, 0, 0, 0.03),
                0 20px 25px -5px rgba(0, 0, 0, 0.05), 
                0 8px 10px -6px rgba(0, 0, 0, 0.01);
        }
        .otp-input {
            letter-spacing: 0.8em;
            text-align: center;
            font-size: 1.5rem;
            font-family: monospace;
            font-weight: 600;
        }
        .floating-icon {
            animation: float 6s ease-in-out infinite;
        }
        .error-shake {
            animation: shake 0.4s cubic-bezier(.36,.07,.19,.97) both;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo / Brand -->
        <div class="text-center mb-8 relative z-10">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-white shadow-2xl mb-6 floating-icon p-4 ring-1 ring-gray-100">
                <img src="../logo.svg" alt="AB Bau" class="w-full h-full object-contain">
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">AB Bau Admin</h1>
            <p class="text-gray-500 mt-2 text-sm font-medium">Verwaltungs-Panel</p>
        </div>
        
        <!-- Card -->
        <div class="login-card rounded-3xl shadow-2xl overflow-hidden relative">
            <div class="h-1.5 bg-gradient-to-r from-primary to-primary-dark w-full absolute top-0 left-0"></div>

            <div class="p-8 sm:p-10">
                <!-- Notifications -->
                <?php if ($error): ?>
                    <div class="bg-red-50/80 backdrop-blur-sm border border-red-200 text-red-600 p-4 rounded-xl mb-6 flex items-start error-shake shadow-sm">
                        <i class="fas fa-exclamation-circle mt-1 mr-3 text-lg flex-shrink-0"></i>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="bg-blue-50/80 backdrop-blur-sm border border-blue-200 text-blue-600 p-4 rounded-xl mb-6 flex items-start shadow-sm">
                        <i class="fas fa-info-circle mt-1 mr-3 text-lg flex-shrink-0"></i>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>
        
                <!-- VIEW: LOGIN (Step 1) -->
                <?php if ($view === 'login'): ?>
                    <form method="POST" action="" class="space-y-5" autocomplete="off">
                        <input type="hidden" name="step" value="1">
                        
                        <!-- Username Field -->
                        <div>
                            <label for="username" class="block text-sm font-bold text-gray-700 mb-2">Benutzername</label>
                            <input type="text" id="username" name="username" required autofocus
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="Benutzername eingeben">
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Passwort</label>
                            <input type="password" id="password" name="password" required
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="Passwort eingeben">
                            <div class="mt-2 text-right">
                                <a href="?action=forgot" class="text-sm text-primary hover:text-primary-dark font-medium transition-colors">Passwort vergessen?</a>
                            </div>
                        </div>
            
                        <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Weiter <i class="fas fa-arrow-right ml-2 mt-1"></i>
                        </button>
                    </form>

                <!-- VIEW: 2FA (Step 2) -->
                <?php elseif ($view === '2fa'): ?>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Zwei-Faktor-Authentifizierung</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Der Code wurde in die Datei <span class="font-medium text-gray-900">admin/2fa.txt</span> geschrieben.
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Öffnen Sie die Datei <code class="bg-gray-100 px-2 py-1 rounded">admin/2fa.txt</code>, um den Code zu sehen.
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Der Code wurde auch an <span class="font-medium">elonberisha1999@gmail.com</span> gesendet.
                        </p>
                    </div>

                    <form method="POST" action="" class="space-y-6" autocomplete="off">
                        <input type="hidden" name="step" value="2">
                        <div>
                            <input type="text" name="otp" required autofocus maxlength="6" pattern="[0-9]*" inputmode="numeric"
                                class="otp-input block w-full py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="000000">
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Verifizieren <i class="fas fa-check-circle ml-2 mt-1"></i>
                        </button>
                    </form>
                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="cancel">
                        <button class="w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Zurück
                        </button>
                    </form>

                <!-- VIEW: FORGOT PASSWORD - EMAIL -->
                <?php elseif ($view === 'forgot_email'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Passwort zurücksetzen</h3>
                        <p class="text-sm text-gray-500 mt-1">Geben Sie Ihre registrierte E-Mail-Adresse ein</p>
                    </div>
                    <form method="POST" action="" class="space-y-6">
                        <input type="hidden" name="action" value="send_reset">
                        <div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email" required autofocus
                                    class="block w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                    placeholder="email@example.com">
                            </div>
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Code senden <i class="fas fa-paper-plane ml-2 mt-1"></i>
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <a href="login.php" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Zurück zum Login
                        </a>
                    </div>

                <!-- VIEW: FORGOT PASSWORD - VERIFY -->
                <?php elseif ($view === 'forgot_verify'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Code verifizieren</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Der Code wurde in die Datei <span class="font-medium text-gray-900">admin/2fa.txt</span> geschrieben.
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            Öffnen Sie die Datei <code class="bg-gray-100 px-2 py-1 rounded">admin/2fa.txt</code>, um den Code zu sehen.
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            Der Code wurde auch an <span class="font-medium">elonberisha1999@gmail.com</span> gesendet.
                        </p>
                    </div>
                    <form method="POST" action="" class="space-y-6">
                        <input type="hidden" name="action" value="verify_reset">
                        <div>
                            <input type="text" name="otp" required autofocus maxlength="6" pattern="[0-9]*" inputmode="numeric"
                                class="otp-input block w-full py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="000000">
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Verifizieren <i class="fas fa-check-circle ml-2 mt-1"></i>
                        </button>
                    </form>
                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="cancel">
                        <button class="w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-times mr-1"></i> Anulo
                        </button>
                    </button>
                </form>
        
                <!-- VIEW: FORGOT PASSWORD - NEW PASSWORD -->
                <?php elseif ($view === 'forgot_new_pass'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Neues Passwort</h3>
                        <p class="text-sm text-gray-500 mt-1">Für Benutzer: <span class="font-medium"><?php echo htmlspecialchars($_SESSION['reset_user']['username'] ?? ''); ?></span></p>
                    </div>
                    <form method="POST" action="" class="space-y-6">
                        <input type="hidden" name="action" value="save_pass">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Neues Passwort</label>
                            <input type="password" name="password" required autofocus
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="******">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Passwort bestätigen</label>
                            <input type="password" name="confirm" required
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="******">
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Passwort speichern <i class="fas fa-save ml-2 mt-1"></i>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-center space-y-2">
            <p class="text-xs text-gray-400">
                &copy; <?php echo date('Y'); ?> AB Bau Admin Panel. All rights reserved.
            </p>
            <div class="flex items-center justify-center space-x-4 text-xs text-gray-400">
                <span class="flex items-center"><i class="fas fa-lock mr-1.5 text-gray-300"></i> Secure Login</span>
                <span class="flex items-center"><i class="fas fa-shield-check mr-1.5 text-gray-300"></i> 2FA Enabled</span>
            </div>
        </div>
    </div>
</body>
</html>