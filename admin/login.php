<?php
require_once 'functions.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Determine View State
$view = 'login'; // Default

if (isset($_SESSION['2fa_pending'])) {
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
        unset($_SESSION['2fa_pending']);
        unset($_SESSION['2fa_code']);
        unset($_SESSION['2fa_expiry']);
        unset($_SESSION['temp_user']); // Clear temp user
        unset($_SESSION['reset_step']);
        unset($_SESSION['reset_otp']);
        unset($_SESSION['reset_expiry']);
        unset($_SESSION['reset_user']); // Clear reset user
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
            
            $userEmail = $user['email'];
            
            // Send Email
            $subject = "AB Bau Admin - Kodi i Verifikimit";
            $msg = "Përshëndetje " . htmlspecialchars($user['username']) . ",\n\nKodi juaj për hyrje në panelin e administrimit është:\n\n" . $otp . "\n\nKy kod skadon në 5 minuta.\n\nNëse nuk keni tentuar të hyni ju, ju lutemi ndryshoni fjalëkalimin menjëherë.";
            $headers = "From: no-reply@ab-bau.de";
            
            @mail($userEmail, $subject, $msg, $headers);
            // DEBUG: Write to file
            file_put_contents(__DIR__ . '/last_otp.txt', "User: $username | Code: $otp");
            
            $view = '2fa';
            $message = 'Kodi i verifikimit u dërgua në ' . htmlspecialchars($userEmail);
        } else {
            $error = 'Username ose fjalëkalimi i gabuar!';
        }
    } 
    
    // Login Step 2: OTP Verification
    elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $userOtp = trim($_POST['otp'] ?? '');
        
        if (isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expiry'])) {
            if (time() > $_SESSION['2fa_expiry']) {
                $error = 'Kodi ka skaduar! Ju lutemi provoni përsëri.';
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                unset($_SESSION['temp_user']);
                $view = 'login';
            } elseif ($userOtp == $_SESSION['2fa_code']) {
                // Success - Log in the user
        $_SESSION['admin_logged_in'] = true;
                $_SESSION['current_user'] = $_SESSION['temp_user']; // Set current user
                
                unset($_SESSION['2fa_pending']);
                unset($_SESSION['2fa_code']);
                unset($_SESSION['2fa_expiry']);
                unset($_SESSION['temp_user']);
                
        header('Location: dashboard.php');
        exit;
    } else {
                $error = 'Kodi i gabuar!';
                $view = '2fa';
            }
        } else {
            $view = 'login';
            $error = 'Seanca ka skaduar.';
        }
    }

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
            
            $subject = "AB Bau Admin - Reset Password";
            $msg = "Përshëndetje " . htmlspecialchars($user['username']) . ",\n\nKodi juaj për ndryshimin e fjalëkalimit është:\n\n" . $otp . "\n\nKy kod skadon në 5 minuta.";
            $headers = "From: no-reply@ab-bau.de";
            
            @mail($user['email'], $subject, $msg, $headers);
            file_put_contents(__DIR__ . '/last_otp.txt', "Reset User: {$user['username']} | Code: $otp");
            
            $view = 'forgot_verify';
            $message = 'Kodi u dërgua në emailin tuaj.';
        } else {
            $error = 'Email nuk u gjet në sistem.';
            $view = 'forgot_email';
        }
    }

    // Verify Reset Code
    elseif (isset($_POST['action']) && $_POST['action'] === 'verify_reset') {
        $otp = trim($_POST['otp'] ?? '');
        if (isset($_SESSION['reset_otp']) && $otp == $_SESSION['reset_otp']) {
            if (time() > $_SESSION['reset_expiry']) {
                $error = 'Kodi ka skaduar.';
                $view = 'forgot_email';
                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_user']);
            } else {
                $_SESSION['reset_step'] = 'new_pass';
                $view = 'forgot_new_pass';
            }
        } else {
            $error = 'Kodi i gabuar.';
            $view = 'forgot_verify';
        }
    }

    // Save New Password
    elseif (isset($_POST['action']) && $_POST['action'] === 'save_pass') {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';
        
        if (empty($pass) || strlen($pass) < 6) {
            $error = 'Fjalëkalimi duhet të ketë të paktën 6 karaktere.';
            $view = 'forgot_new_pass';
        } elseif ($pass !== $confirm) {
            $error = 'Fjalëkalimet nuk përputhen.';
            $view = 'forgot_new_pass';
        } else {
            if (isset($_SESSION['reset_user'])) {
                $usernameToReset = $_SESSION['reset_user']['username'];
                updateUserPassword($usernameToReset, $pass);
                
                unset($_SESSION['reset_step']);
                unset($_SESSION['reset_otp']);
                unset($_SESSION['reset_expiry']);
                unset($_SESSION['reset_user']);
                
                $view = 'login';
                $message = 'Fjalëkalimi u ndryshua me sukses. Ju lutemi hyni.';
            } else {
                $error = 'Gabim i panjohur. Provoni përsëri.';
                $view = 'login';
            }
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
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            <p class="text-gray-500 mt-2 text-sm font-medium">Paneli i Administrimit</p>
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
                            <label for="username" class="block text-sm font-bold text-gray-700 mb-2">Përdoruesi</label>
                            <input type="text" id="username" name="username" required autofocus
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="Shkruani emrin e përdoruesit">
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Fjalëkalimi</label>
                            <input type="password" id="password" name="password" required
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="Shkruani fjalëkalimin tuaj">
                            <div class="mt-2 text-right">
                                <a href="?action=forgot" class="text-sm text-primary hover:text-primary-dark font-medium transition-colors">Harruat fjalëkalimin?</a>
                            </div>
            </div>
            
            <button type="submit"
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Vazhdo <i class="fas fa-arrow-right ml-2 mt-1"></i>
                        </button>
                    </form>

                <!-- VIEW: 2FA (Step 2) -->
                <?php elseif ($view === '2fa'): ?>
                    <div class="text-center mb-6">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shield-alt text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Verifikimi me 2 Hapa</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Kodi u dërgua tek <span class="font-medium text-gray-900"><?php 
                                $tempEmail = $_SESSION['temp_user']['email'] ?? 'email...';
                                echo substr($tempEmail, 0, 3) . '***' . substr($tempEmail, strpos($tempEmail, '@')); 
                            ?></span>
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
                            Verifiko <i class="fas fa-check-circle ml-2 mt-1"></i>
                        </button>
                    </form>
                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="cancel">
                        <button class="w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Kthehu
                        </button>
                    </form>

                <!-- VIEW: FORGOT PASSWORD - EMAIL -->
                <?php elseif ($view === 'forgot_email'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Rivendos Fjalëkalimin</h3>
                        <p class="text-sm text-gray-500 mt-1">Shkruani emailin tuaj të regjistruar</p>
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
                            Dërgo Kodin <i class="fas fa-paper-plane ml-2 mt-1"></i>
                        </button>
                    </form>
                    <div class="mt-4 text-center">
                        <a href="login.php" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Kthehu tek Login
                        </a>
                    </div>

                <!-- VIEW: FORGOT PASSWORD - VERIFY -->
                <?php elseif ($view === 'forgot_verify'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Verifiko Kodin</h3>
                        <p class="text-sm text-gray-500 mt-1">Kodi i dërguar tek <span class="font-medium"><?php echo htmlspecialchars($_SESSION['reset_user']['email'] ?? ''); ?></span></p>
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
                            Verifiko <i class="fas fa-check-circle ml-2 mt-1"></i>
                        </button>
                    </form>
                    <form method="POST" action="" class="mt-4">
                        <input type="hidden" name="action" value="cancel">
                        <button class="w-full text-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                            <i class="fas fa-times mr-1"></i> Anulo
            </button>
        </form>
        
                <!-- VIEW: FORGOT PASSWORD - NEW PASSWORD -->
                <?php elseif ($view === 'forgot_new_pass'): ?>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Fjalëkalimi i Ri</h3>
                        <p class="text-sm text-gray-500 mt-1">Për userin: <span class="font-medium"><?php echo htmlspecialchars($_SESSION['reset_user']['username'] ?? ''); ?></span></p>
                    </div>
                    <form method="POST" action="" class="space-y-6">
                        <input type="hidden" name="action" value="save_pass">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Fjalëkalimi i ri</label>
                            <input type="password" name="password" required autofocus
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="******">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmo fjalëkalimin</label>
                            <input type="password" name="confirm" required
                                class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all duration-200"
                                placeholder="******">
                        </div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-gradient-to-r from-primary to-primary-dark hover:from-primary-dark hover:to-primary focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transform hover:-translate-y-0.5 transition-all duration-200">
                            Ruaj Fjalëkalimin <i class="fas fa-save ml-2 mt-1"></i>
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