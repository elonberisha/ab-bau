<?php
// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Connection
require_once __DIR__ . '/includes/db_connect.php';

// --- AUTHENTICATION FUNCTIONS ---

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

function verifyUserCredentials($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user) {
        $hash = isset($user['password_hash']) ? $user['password_hash'] : ($user['password'] ?? null);
        if ($hash && password_verify($password, $hash)) {
            return $user;
        }
    }
    return false;
}

function getUserByEmail($email) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    return $stmt->fetch();
}

function updateUserPassword($userId, $newPassword) {
    global $pdo;
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $result = $stmt->execute(['hash' => $hash, 'id' => $userId]);
    } catch (PDOException $e) {
        $stmt = $pdo->prepare("UPDATE users SET password = :hash WHERE id = :id");
        $result = $stmt->execute(['hash' => $hash, 'id' => $userId]);
    }
    return $result;
}

// --- USER MANAGEMENT FUNCTIONS ---

function getAllUsers() {
    global $pdo;
    try {
        // Try to get all columns, but handle missing columns gracefully
        $stmt = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // If error (e.g., missing columns), try with minimal columns
        try {
            $stmt = $pdo->query("SELECT id, username, email FROM users ORDER BY id DESC");
            $users = $stmt->fetchAll();
            // Add default values for missing columns
            foreach ($users as &$user) {
                if (!isset($user['role'])) $user['role'] = 'Administrator';
                if (!isset($user['created_at'])) $user['created_at'] = null;
            }
            return $users;
        } catch (PDOException $e2) {
            return [];
        }
    }
}

function getUserById($userId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return false;
    }
}

function createUser($username, $password, $email, $role = 'Administrator') {
    global $pdo;
    
    // Validate inputs
    if (empty($username) || empty($password) || empty($email)) {
        return ['success' => false, 'error' => 'Alle Felder sind erforderlich.'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Das Passwort muss mindestens 6 Zeichen lang sein.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Ungültige E-Mail-Adresse.'];
    }
    
    // Validate and sanitize role
    $allowedRoles = ['Administrator', 'Editor', 'Viewer'];
    if (!in_array($role, $allowedRoles)) {
        $role = 'Administrator'; // Default to Administrator if invalid role
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Benutzername bereits vergeben.'];
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'E-Mail-Adresse bereits vergeben.'];
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    try {
        // Try with all columns first
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role, created_at) VALUES (:username, :password_hash, :email, :role, NOW())");
        $result = $stmt->execute([
            'username' => $username,
            'password_hash' => $hash,
            'email' => $email,
            'role' => $role
        ]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Benutzer erfolgreich erstellt.'];
        }
    } catch (PDOException $e) {
        // Try with 'password' column if 'password_hash' doesn't exist
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role, created_at) VALUES (:username, :password, :email, :role, NOW())");
            $result = $stmt->execute([
                'username' => $username,
                'password' => $hash,
                'email' => $email,
                'role' => $role
            ]);
            
            if ($result) {
                return ['success' => true, 'message' => 'Benutzer erfolgreich erstellt.'];
            }
        } catch (PDOException $e2) {
            // Try with role but without created_at column
            try {
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (:username, :password, :email, :role)");
                $result = $stmt->execute([
                    'username' => $username,
                    'password' => $hash,
                    'email' => $email,
                    'role' => $role
                ]);
                
                if ($result) {
                    return ['success' => true, 'message' => 'Benutzer erfolgreich erstellt.'];
                }
            } catch (PDOException $e3) {
                // Try without role and created_at columns (last resort)
                try {
                    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (:username, :password, :email)");
                    $result = $stmt->execute([
                        'username' => $username,
                        'password' => $hash,
                        'email' => $email
                    ]);
                    
                    if ($result) {
                        // If role column doesn't exist, we can't save it, but user is created
                        return ['success' => true, 'message' => 'Benutzer erfolgreich erstellt. (Hinweis: Rolle konnte nicht gespeichert werden, da die Spalte fehlt.)'];
                    }
                } catch (PDOException $e4) {
                    return ['success' => false, 'error' => 'Fehler beim Erstellen des Benutzers: ' . $e4->getMessage()];
                }
            }
        }
    }
    
    return ['success' => false, 'error' => 'Fehler beim Erstellen des Benutzers.'];
}

function updateUser($userId, $username, $email, $password = null, $role = null) {
    global $pdo;
    
    // Check if user is protected admin (cannot be edited)
    $user = getUserById($userId);
    if ($user && isset($user['email']) && $user['email'] === 'elonberisha1999@gmail.com') {
        return ['success' => false, 'error' => 'Dieser Benutzer kann nicht bearbeitet werden.'];
    }
    
    // Validate inputs
    if (empty($username) || empty($email)) {
        return ['success' => false, 'error' => 'Benutzername und E-Mail sind erforderlich.'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Ungültige E-Mail-Adresse.'];
    }
    
    // Check if username already exists (excluding current user)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id LIMIT 1");
    $stmt->execute(['username' => $username, 'id' => $userId]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Benutzername bereits vergeben.'];
    }
    
    // Check if email already exists (excluding current user)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1");
    $stmt->execute(['email' => $email, 'id' => $userId]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'E-Mail-Adresse bereits vergeben.'];
    }
    
    // Build update query
    $fields = [];
    $params = ['id' => $userId];
    
    $fields[] = "username = :username";
    $params['username'] = $username;
    
    $fields[] = "email = :email";
    $params['email'] = $email;
    
    if ($password !== null && !empty($password)) {
        if (strlen($password) < 6) {
            return ['success' => false, 'error' => 'Das Passwort muss mindestens 6 Zeichen lang sein.'];
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = $hash;
        } catch (Exception $e) {
            $fields[] = "password = :password";
            $params['password'] = $hash;
        }
    }
    
    if ($role !== null) {
        // Validate role
        $allowedRoles = ['Administrator', 'Editor', 'Viewer'];
        if (!in_array($role, $allowedRoles)) {
            $role = 'Administrator'; // Default to Administrator if invalid role
        }
        
        // Check if role column exists before trying to update it
        try {
            $checkStmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
            if ($checkStmt->fetch()) {
                $fields[] = "role = :role";
                $params['role'] = $role;
            }
        } catch (PDOException $e) {
            // Role column doesn't exist, skip it
        }
    }
    
    // Update user
    try {
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            return ['success' => true, 'message' => 'Benutzer erfolgreich aktualisiert.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Fehler beim Aktualisieren des Benutzers: ' . $e->getMessage()];
    }
    
    return ['success' => false, 'error' => 'Fehler beim Aktualisieren des Benutzers.'];
}

function deleteUser($userId) {
    global $pdo;
    
    // Check if user is protected admin (cannot be deleted)
    $user = getUserById($userId);
    if ($user && isset($user['email']) && $user['email'] === 'elonberisha1999@gmail.com') {
        return ['success' => false, 'error' => 'Dieser Benutzer kann nicht gelöscht werden.'];
    }
    
    // Prevent deleting yourself
    if (isLoggedIn() && $_SESSION['user_id'] == $userId) {
        return ['success' => false, 'error' => 'Sie können sich nicht selbst löschen.'];
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $result = $stmt->execute(['id' => $userId]);
        
        if ($result && $stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Benutzer erfolgreich gelöscht.'];
        } else {
            return ['success' => false, 'error' => 'Benutzer nicht gefunden.'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Fehler beim Löschen des Benutzers: ' . $e->getMessage()];
    }
}

// --- DATA MANAGEMENT FUNCTIONS ---

function getSectionData($table) {
    global $pdo;
    $allowedTables = ['hero_section', 'about_section', 'contact_section', 'legal_section', 'services_section', 'catalogs_section', 'portfolio_section'];
    if (!in_array($table, $allowedTables)) return [];

    try {
        $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
        $data = $stmt->fetch();
        return $data ? $data : [];
    } catch (PDOException $e) {
        return [];
    }
}

function updateSectionData($table, $data) {
    global $pdo;
    $allowedTables = ['hero_section', 'about_section', 'contact_section', 'legal_section', 'services_section', 'catalogs_section', 'portfolio_section'];
    if (!in_array($table, $allowedTables)) return false;

    $stmt = $pdo->query("SELECT id FROM $table LIMIT 1");
    $exists = $stmt->fetch();

    if ($exists) {
        $fields = [];
        $params = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        $params['id'] = $exists['id'];
        $sql = "UPDATE $table SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } else {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($data);
    }
}

function sanitize($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function uploadImage($file, $folder = 'uploads', $customName = '') {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) return ['success' => false, 'error' => 'Nicht erlaubtes Format.'];

    $targetDir = dirname(__DIR__) . '/' . $folder . '/';
    if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = !empty($customName) ? preg_replace('/[^a-zA-Z0-9_-]/', '', $customName) . '.' . $extension : uniqid() . '_' . time() . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
        return ['success' => true, 'path' => $folder . '/' . $filename];
    }
    return ['success' => false, 'error' => 'Fehler beim Hochladen.'];
}

function deleteImage($path) {
    if (empty($path)) return true;
    $realPath = realpath(dirname(__DIR__) . '/' . $path);
    if ($realPath && file_exists($realPath) && strpos($realPath, realpath(dirname(__DIR__))) === 0) {
        return unlink($realPath);
    }
    return false;
}

// --- UPDATED STATS FUNCTION ---

function getStats() {
    global $pdo;
    
    // Initialize default values to avoid "Undefined array key" errors
    $stats = [
        'projects' => 0,
        'services' => 0,
        'catalogs' => 0,
        'reviews_total' => 0,
        'reviews_pending' => 0
    ];

    try {
        // Projects
        $stats['projects'] = $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();

        // Services
        $stats['services'] = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
        
        // Catalogs
        $stats['catalogs'] = $pdo->query("SELECT COUNT(*) FROM catalogs")->fetchColumn();

        // Reviews Total
        $stats['reviews_total'] = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
        
        // Reviews Pending
        $stats['reviews_pending'] = $pdo->query("SELECT COUNT(*) FROM reviews WHERE status = 'pending'")->fetchColumn();
        
    } catch (PDOException $e) {
        // In case of error (e.g. table missing), values remain 0
    }

    return $stats;
}
?>