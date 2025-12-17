<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $messageType = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}

$editingUserId = null;
$editingUser = null;

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // --- CREATE USER ---
        if ($_POST['action'] === 'create') {
            $username = sanitize($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $email = sanitize($_POST['email'] ?? '');
            $role = sanitize($_POST['role'] ?? 'Administrator');
            
            $result = createUser($username, $password, $email, $role);
            
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $result['error'];
                $_SESSION['message_type'] = 'error';
            }
        }
        // --- UPDATE USER ---
        elseif ($_POST['action'] === 'update') {
            $userId = (int)($_POST['id'] ?? 0);
            $username = sanitize($_POST['username'] ?? '');
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = sanitize($_POST['role'] ?? 'Administrator');
            
            // Only update password if provided
            $result = updateUser($userId, $username, $email, !empty($password) ? $password : null, $role);
            
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $result['error'];
                $_SESSION['message_type'] = 'error';
            }
        }
        // --- DELETE USER ---
        elseif ($_POST['action'] === 'delete') {
            $userId = (int)($_POST['id'] ?? 0);
            
            $result = deleteUser($userId);
            
            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = $result['error'];
                $_SESSION['message_type'] = 'error';
            }
        }
        // --- EDIT USER (Load for editing) ---
        elseif ($_POST['action'] === 'edit') {
            $editingUserId = (int)($_POST['id'] ?? 0);
            $userToEdit = getUserById($editingUserId);
            
            // Check if user is protected
            if ($userToEdit && isset($userToEdit['email']) && $userToEdit['email'] === 'elonberisha1999@gmail.com') {
                $_SESSION['message'] = 'Dieser Benutzer kann nicht bearbeitet werden.';
                $_SESSION['message_type'] = 'error';
                header("Location: users.php");
                exit;
            }
            
            header("Location: users.php?edit=" . $editingUserId);
            exit;
        }
        
        header("Location: users.php");
        exit;
    }
}

// Check if editing from URL
if (isset($_GET['edit'])) {
    $editingUserId = (int)$_GET['edit'];
    $editingUser = getUserById($editingUserId);
}

// Fetch all users
$users = getAllUsers();

$pageTitle = 'Benutzer verwalten';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - AB Bau Admin</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Layout Wrapper -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar (Fixed Width) -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content (Flexible) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.html" target="_blank" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:border-primary/30">
                        <i class="fas fa-external-link-alt mr-2 text-xs"></i> Website Live
                    </a>
                </div>
            </header>
            
            <!-- Main Content Area (Scrollable) -->
            <main class="flex-1 overflow-y-auto p-6">
                
                <!-- Message Alert -->
                <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-lg shadow-md animate-fade-in <?php echo $messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
                    <div class="flex items-center">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-2"></i>
                        <span><?php echo htmlspecialchars($message); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Create/Edit User Form -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div class="bg-indigo-50/50 p-4 border-b border-indigo-100">
                        <h2 class="text-lg font-bold text-indigo-900 flex items-center">
                            <i class="fas fa-user-plus mr-2"></i>
                            <?php echo $editingUser ? 'Benutzer bearbeiten' : 'Neuen Benutzer erstellen'; ?>
                        </h2>
                    </div>
                    
                    <form method="POST" class="p-6">
                        <input type="hidden" name="action" value="<?php echo $editingUser ? 'update' : 'create'; ?>">
                        <?php if ($editingUser): ?>
                            <input type="hidden" name="id" value="<?php echo $editingUser['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Benutzername</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($editingUser['username'] ?? ''); ?>" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">E-Mail</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($editingUser['email'] ?? ''); ?>" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">
                                    Passwort
                                    <?php if ($editingUser): ?>
                                        <span class="text-gray-400 font-normal">(leer lassen, um nicht zu ändern)</span>
                                    <?php endif; ?>
                                </label>
                                <input type="password" name="password" <?php echo $editingUser ? '' : 'required'; ?>
                                       placeholder="<?php echo $editingUser ? 'Nur ausfüllen, wenn geändert werden soll' : 'Mindestens 6 Zeichen'; ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Rolle</label>
                                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="Administrator" <?php echo ($editingUser['role'] ?? 'Administrator') === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                                    <option value="Editor" <?php echo ($editingUser['role'] ?? '') === 'Editor' ? 'selected' : ''; ?>>Editor</option>
                                    <option value="Viewer" <?php echo ($editingUser['role'] ?? '') === 'Viewer' ? 'selected' : ''; ?>>Viewer</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end gap-3">
                            <?php if ($editingUser): ?>
                                <a href="users.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-6 rounded-lg shadow-md transform hover:-translate-y-0.5 transition-all">
                                    <i class="fas fa-times mr-2"></i> Abbrechen
                                </a>
                            <?php endif; ?>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transform hover:-translate-y-0.5 transition-all">
                                <i class="fas fa-save mr-2"></i> <?php echo $editingUser ? 'Änderungen speichern' : 'Benutzer erstellen'; ?>
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Users List -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-indigo-50/50 p-4 border-b border-indigo-100">
                        <h2 class="text-lg font-bold text-indigo-900 flex items-center">
                            <i class="fas fa-users mr-2"></i> Alle Benutzer
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-12 text-gray-500">
                                <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                <p class="text-lg font-medium">Keine Benutzer gefunden</p>
                                <p class="text-sm mt-2">Erstellen Sie den ersten Benutzer mit dem Formular oben.</p>
                            </div>
                        <?php else: ?>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Benutzername</th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">E-Mail</th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Rolle</th>
                                            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Erstellt am</th>
                                            <th class="text-right py-3 px-4 text-xs font-semibold text-gray-500 uppercase">Aktionen</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                            <?php 
                                            $isProtected = isset($user['email']) && $user['email'] === 'elonberisha1999@gmail.com';
                                            $isCurrentUser = isLoggedIn() && $_SESSION['user_id'] == $user['id'];
                                            ?>
                                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                                <td class="py-3 px-4 text-sm text-gray-600"><?php echo $user['id']; ?></td>
                                                <td class="py-3 px-4 text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($user['username']); ?>
                                                    <?php if ($isProtected): ?>
                                                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded">Geschützt</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                                                <td class="py-3 px-4 text-sm text-gray-600">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        <?php echo htmlspecialchars($user['role'] ?? 'Administrator'); ?>
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-sm text-gray-500">
                                                    <?php 
                                                    if (isset($user['created_at'])) {
                                                        $date = new DateTime($user['created_at']);
                                                        echo $date->format('d.m.Y H:i');
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="py-3 px-4 text-right">
                                                    <div class="flex items-center justify-end gap-2">
                                                        <?php if (!$isProtected): ?>
                                                            <form method="POST" class="inline">
                                                                <input type="hidden" name="action" value="edit">
                                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="text-indigo-600 hover:text-indigo-800 transition-colors" title="Bearbeiten">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                            </form>
                                                        <?php else: ?>
                                                            <span class="text-gray-400 cursor-not-allowed" title="Geschützter Benutzer - kann nicht bearbeitet werden">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!$isProtected && !$isCurrentUser): ?>
                                                            <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?');">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Löschen">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        <?php elseif ($isCurrentUser): ?>
                                                            <span class="text-gray-400 cursor-not-allowed" title="Sie können sich nicht selbst löschen">
                                                                <i class="fas fa-ban"></i>
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="text-gray-400 cursor-not-allowed" title="Geschützter Benutzer - kann nicht gelöscht werden">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
            </main>
        </div>
    </div>
    
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.w-64').classList.toggle('-translate-x-full');
        });
    </script>
</body>
</html>

