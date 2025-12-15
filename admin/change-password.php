<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'Ndrysho Fjalëkalimin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = 'Të gjitha fushat duhen plotësuar!';
        $messageType = 'error';
    } elseif (!verifyPassword($currentPassword)) {
        $message = 'Fjalëkalimi aktual është i gabuar!';
        $messageType = 'error';
    } elseif ($newPassword !== $confirmPassword) {
        $message = 'Fjalëkalimet e reja nuk përputhen!';
        $messageType = 'error';
    } elseif (strlen($newPassword) < 6) {
        $message = 'Fjalëkalimi duhet të jetë së paku 6 karaktere!';
        $messageType = 'error';
    } else {
        if (updatePassword($newPassword)) {
            $message = 'Passwort wurde erfolgreich geändert!';
            $messageType = 'success';
        } else {
            $message = 'Fehler beim Ändern des Passworts!';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="ml-64 pt-16 p-6 max-w-2xl">
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-key text-primary mr-2"></i>
                Ndrysho Fjalëkalimin
            </h2>
            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2"></i>Fjalëkalimi Aktual
                    </label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Shkruani fjalëkalimin aktual">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>Fjalëkalimi i Ri
                    </label>
                    <input type="password" name="new_password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Shkruani fjalëkalimin e ri (min. 6 karaktere)">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2"></i>Konfirmo Fjalëkalimin e Ri
                    </label>
                    <input type="password" name="confirm_password" required minlength="6"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                           placeholder="Konfirmoni fjalëkalimin e ri">
                </div>
                
                <button type="submit" class="w-full bg-gray-800 text-white px-4 py-3 rounded-lg hover:bg-gray-900 font-semibold">
                    <i class="fas fa-save mr-2"></i>Änderungen speichern
                </button>
            </form>
        </div>
    </div>
</body>
</html>
