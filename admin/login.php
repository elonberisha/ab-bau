<?php
require_once 'functions.php';

// Get config for email
$config = readJson('config.json');
$adminEmail = $config['admin_email'] ?? 'office@ab-bau.de';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$step = isset($_SESSION['2fa_pending']) ? 2 : 1;
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if reset action
    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        unset($_SESSION['2fa_pending']);
        unset($_SESSION['2fa_code']);
        unset($_SESSION['2fa_expiry']);
        $step = 1;
    } 
    // Step 1: Password Verification
    elseif (isset($_POST['step']) && $_POST['step'] == 1) {
        $password = $_POST['password'] ?? '';
        
        if (verifyPassword($password)) {
            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            
            // Save to session
            $_SESSION['2fa_code'] = $otp;
            $_SESSION['2fa_expiry'] = time() + 300; // 5 minutes validity
            $_SESSION['2fa_pending'] = true;
            
            // Send Email
            $subject = "AB Bau Admin - Kodi i Verifikimit";
            $msg = "Përshëndetje,\n\nKodi juaj për hyrje në panelin e administrimit është:\n\n" . $otp . "\n\nKy kod skadon në 5 minuta.\n\nNëse nuk keni tentuar të hyni ju, ju lutemi ndryshoni fjalëkalimin menjëherë.";
            $headers = "From: no-reply@ab-bau.de";
            
            // Try to send email
            $mailSent = @mail($adminEmail, $subject, $msg, $headers);
            
            // For development/local testing: Write code to a file if mail fails or just always
            // In production this should be removed or logged securely
            file_put_contents(__DIR__ . '/last_otp.txt', $otp);
            
            $step = 2;
            $message = 'Kodi i verifikimit u dërgua në ' . htmlspecialchars($adminEmail);
        } else {
            $error = 'Fjalëkalimi i gabuar!';
        }
    } 
    // Step 2: OTP Verification
    elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $userOtp = $_POST['otp'] ?? '';
        $userOtp = trim($userOtp);
        
        if (isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expiry'])) {
            if (time() > $_SESSION['2fa_expiry']) {
                $error = 'Kodi ka skaduar! Ju lutemi provoni përsëri.';
                // Reset to step 1
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                $step = 1;
            } elseif ($userOtp == $_SESSION['2fa_code']) {
                // Success
                $_SESSION['admin_logged_in'] = true;
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Kodi i gabuar!';
            }
        } else {
            // Session expired or invalid
            $step = 1;
            $error = 'Seanca ka skaduar, ju lutemi provoni përsëri.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AB Bau</title>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="../dist/css/output.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            background-image: 
                radial-gradient(at 0% 0%, hsla(216, 85%, 65%, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 0%, hsla(216, 85%, 65%, 0.1) 0px, transparent 50%);
        }
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }
        .otp-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.5rem;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <!-- Logo / Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary text-white shadow-xl mb-4 transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-cube text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">AB Bau Admin</h1>
            <p class="text-gray-500 mt-2 text-sm">Paneli i Administrimit</p>
        </div>

        <!-- Card -->
        <div class="login-card rounded-3xl shadow-2xl border border-white/50 overflow-hidden relative">
            <!-- Decorative top line -->
            <div class="h-2 bg-gradient-to-r from-primary to-primary-dark w-full absolute top-0 left-0"></div>

            <div class="p-8 sm:p-10">
                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r-lg mb-6 flex items-start animate-pulse">
                        <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded-r-lg mb-6 flex items-start">
                        <i class="fas fa-info-circle mt-1 mr-3"></i>
                        <span class="text-sm font-medium"><?php echo htmlspecialchars($message); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($step == 1): ?>
                    <!-- Step 1 Form -->
                    <form method="POST" action="" class="space-y-6" autocomplete="off">
                        <input type="hidden" name="step" value="1">
                        
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Fjalëkalimi</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" id="password" name="password" required autofocus
                                    class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                    placeholder="Shkruani fjalëkalimin tuaj">
                            </div>
                        </div>

                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Vazhdo <i class="fas fa-arrow-right ml-2 mt-1"></i>
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Step 2 Form -->
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Verifikimi me 2 Hapa</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Kemi dërguar një kod verifikimi në emailin tuaj<br>
                            <span class="font-medium text-gray-900"><?php echo substr($adminEmail, 0, 3) . '***' . substr($adminEmail, strpos($adminEmail, '@')); ?></span>
                        </p>
                    </div>

                    <form method="POST" action="" class="space-y-6" autocomplete="off">
                        <input type="hidden" name="step" value="2">
                        
                        <div>
                            <label for="otp" class="sr-only">Kodi i Verifikimit</label>
                            <input type="text" id="otp" name="otp" required autofocus maxlength="6" pattern="[0-9]*" inputmode="numeric"
                                class="otp-input block w-full py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="000000">
                        </div>

                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Verifiko & Hyr <i class="fas fa-check-circle ml-2 mt-1"></i>
                        </button>
                    </form>

                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="reset">
                        <button type="submit" class="w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Kthehu tek fjalëkalimi
                        </button>
                    </form>
                    
                    <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                        <p class="text-xs text-gray-400">
                            Nuk e morët kodin? <a href="#" onclick="alert('Për momentin, kodi ruhet edhe në admin/last_otp.txt për testim.'); return false;" class="text-primary hover:underline">Rigjenero kodin</a>
                        </p>
                    </div>
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
