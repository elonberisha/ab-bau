<?php
require_once 'functions.php';
requireLogin();

$currentUser = getCurrentUser();
$username = $currentUser['username'] ?? 'admin'; // Fallback if session issue

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Verify current password first
    if (!verifyUserCredentials($username, $current_password)) {
        $error = 'Fjalëkalimi aktual është i pasaktë!';
    } elseif (strlen($new_password) < 6) {
        $error = 'Fjalëkalimi i ri duhet të ketë të paktën 6 karaktere!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Fjalëkalimet nuk përputhen!';
    } else {
        if (updateUserPassword($username, $new_password)) {
            $message = 'Fjalëkalimi u ndryshua me sukses!';
        } else {
            $error = 'Ndodhi një gabim gjatë ruajtjes. Provoni përsëri.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="sq">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ndrysho Fjalëkalimin - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:pl-64 transition-all duration-300">
            <!-- Header -->
            <header class="bg-white shadow-sm z-20 h-16 flex items-center justify-between px-6">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">Cilësimet e Llogarisë</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Përshëndetje, <b><?php echo htmlspecialchars(ucfirst($username)); ?></b></span>
                </div>
            </header>
            
            <!-- Main -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                <div class="max-w-2xl mx-auto">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">Ndrysho Fjalëkalimin</h2>
                                <p class="text-sm text-gray-500 mt-1">Përditësoni fjalëkalimin për llogarinë tuaj.</p>
                            </div>
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-primary">
                                <i class="fas fa-lock"></i>
                            </div>
                        </div>
                        
                        <div class="p-6 md:p-8">
                            <?php if ($message): ?>
                                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r mb-6 flex items-start">
                                    <i class="fas fa-check-circle mt-1 mr-3"></i>
                                    <span><?php echo $message; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($error): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r mb-6 flex items-start">
                                    <i class="fas fa-exclamation-circle mt-1 mr-3"></i>
                                    <span><?php echo $error; ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <form method="POST" action="" class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fjalëkalimi Aktual</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-key text-gray-400"></i>
                                        </div>
                                        <input type="password" name="current_password" required
                                            class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                            placeholder="Shkruani fjalëkalimin aktual">
                                    </div>
                                </div>
                                
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fjalëkalimi i Ri</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-lock text-gray-400"></i>
                                            </div>
                                            <input type="password" name="new_password" required minlength="6"
                                                class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                                placeholder="Min. 6 karaktere">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmo Fjalëkalimin</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <i class="fas fa-check-double text-gray-400"></i>
                                            </div>
                                            <input type="password" name="confirm_password" required minlength="6"
                                                class="block w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                                placeholder="Përsërit fjalëkalimin">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pt-4 flex items-center justify-end space-x-4">
                                    <button type="reset" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium transition-colors">
                                        Pastro
                                    </button>
                                    <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary-dark text-white font-medium shadow-lg shadow-primary/30 transition-all transform hover:-translate-y-0.5">
                                        <i class="fas fa-save mr-2"></i> Ruaj Ndryshimet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script>
        // Simple sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.bg-gray-900.fixed'); // Select sidebar
            if (sidebar) {
                sidebar.classList.toggle('-translate-x-full');
            }
        });
    </script>
</body>
</html>
