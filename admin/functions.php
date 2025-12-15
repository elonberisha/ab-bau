<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Get data directory path
function getDataPath($file) {
    return dirname(__DIR__) . '/data/' . $file;
}

// Read JSON file with UTF-8 encoding
function readJson($file) {
    $path = getDataPath($file);
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    // Remove UTF-8 BOM if present
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    // Ensure UTF-8 encoding
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
    }
    return json_decode($content, true) ?: [];
}

// Write JSON file with UTF-8 encoding
function writeJson($file, $data) {
    $path = getDataPath($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    // Encode with proper UTF-8 handling and preserve all characters
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS);
    // Ensure UTF-8 BOM is not added and file is saved as UTF-8
    return file_put_contents($path, $json, LOCK_EX);
}

// Verify password
function verifyPassword($password) {
    $config = readJson('config.json');
    if (isset($config['password_hash']) && !empty($config['password_hash'])) {
        $verified = password_verify($password, $config['password_hash']);
        if ($verified) {
            return true;
        }
    }
    // Fallback for default password (if hash verification fails or no hash exists)
    if (isset($config['default_password'])) {
        return $password === $config['default_password'];
    }
    // Final fallback
    return $password === 'admin123';
}

// Hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Update password
function updatePassword($newPassword) {
    $config = readJson('config.json');
    $config['password_hash'] = hashPassword($newPassword);
    return writeJson('config.json', $config);
}

// Sanitize input - preserve text structure but clean it
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    // Trim whitespace but preserve newlines and special characters
    $data = trim($data);
    // Don't strip tags or special characters - we want to preserve the text as-is
    // Only remove null bytes and other dangerous characters
    $data = str_replace("\0", '', $data);
    return $data;
}

// Upload image
function uploadImage($file, $folder = 'uploads', $customName = '') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'File nuk u ngarkua saktë'];
    }
    
    // Validate file type by extension (more reliable)
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($extension, $allowedExtensions)) {
        return ['success' => false, 'error' => 'Format i palejuar! Formatet e lejuara: JPG, JPEG, PNG, GIF, WEBP'];
    }
    
    // Validate MIME type
    $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowedMimes)) {
        return ['success' => false, 'error' => 'Lloji i file nuk është imazh i vlefshëm'];
    }
    
    // Check file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Fotoja është shumë e madhe! Maksimumi: 5MB'];
    }
    
    // Create directory if doesn't exist
    $dir = dirname(__DIR__) . '/' . $folder;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Check if file already exists (by checking if same file content exists)
    $existingFiles = glob($dir . '/*.' . $extension);
    $uploadedFileHash = md5_file($file['tmp_name']);
    
    foreach ($existingFiles as $existingFile) {
        if (file_exists($existingFile) && md5_file($existingFile) === $uploadedFileHash) {
            // File already exists, return existing path
            return ['success' => true, 'path' => $folder . '/' . basename($existingFile), 'duplicate' => true];
        }
    }
    
    // Generate filename
    if (!empty($customName)) {
        // Use custom name, sanitize it
        $cleanName = strtolower(trim($customName));
        $cleanName = preg_replace('/[^a-z0-9-_]/', '-', $cleanName);
        $cleanName = preg_replace('/-+/', '-', $cleanName);
        $cleanName = trim($cleanName, '-');
        
        if (empty($cleanName)) {
            $cleanName = 'image-' . time();
        }
        
        $filename = $cleanName . '.' . $extension;
        
        // Check if filename already exists, add number suffix
        $counter = 1;
        while (file_exists($dir . '/' . $filename)) {
            $filename = $cleanName . '-' . $counter . '.' . $extension;
            $counter++;
        }
    } else {
        // Use unique ID + timestamp
        $filename = uniqid() . '_' . time() . '.' . $extension;
    }
    
    $uploadPath = $dir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'path' => $folder . '/' . $filename, 'duplicate' => false];
    }
    
    return ['success' => false, 'error' => 'Gabim në ruajtjen e fotos në server'];
}

// Upload PDF file
function uploadPDF($file, $folder = 'uploads/pdfs') {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'error' => 'File nuk u ngarkua saktë'];
    }
    
    // Validate file type by extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        return ['success' => false, 'error' => 'Format i palejuar! Vetëm PDF është i lejuar'];
    }
    
    // Validate MIME type
    $allowed = ['application/pdf'];
    $fileType = $file['type'];
    
    if (!in_array($fileType, $allowed)) {
        return ['success' => false, 'error' => 'Lloji i file nuk është PDF i vlefshëm'];
    }
    
    // Check file size (max 10MB for PDFs)
    if ($file['size'] > 10 * 1024 * 1024) {
        return ['success' => false, 'error' => 'PDF është shumë i madh! Maksimumi: 10MB'];
    }
    
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = dirname(__DIR__) . '/' . $folder . '/' . $filename;
    
    // Create directory if it doesn't exist
    $dir = dirname($uploadPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['success' => true, 'path' => $folder . '/' . $filename];
    }
    
    return ['success' => false, 'error' => 'Gabim në ruajtjen e PDF në server'];
}

// Delete image file
function deleteImage($path) {
    // Normalize path
    $path = str_replace('\\', '/', $path);
    $path = ltrim($path, './\\');
    
    // If path doesn't start with uploads/, add it
    if (strpos($path, 'uploads/') !== 0) {
        $path = 'uploads/' . ltrim($path, '/\\');
    }
    
    $baseDir = dirname(__DIR__);
    $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
    
    // Try alternative paths if first doesn't exist
    if (!file_exists($fullPath)) {
        $altPath1 = $baseDir . '/' . $path;
        $altPath2 = $baseDir . '\\' . str_replace('/', '\\', $path);
        
        if (file_exists($altPath1)) {
            $fullPath = $altPath1;
        } elseif (file_exists($altPath2)) {
            $fullPath = $altPath2;
        }
    }
    
    if (file_exists($fullPath) && is_file($fullPath)) {
        return @unlink($fullPath);
    }
    return false;
}

// Check if image is used in any JSON files
function isImageUsed($imagePath) {
    $jsonFiles = ['gallery.json', 'services.json', 'catalogs.json', 'customization.json', 'reviews.json'];
    
    foreach ($jsonFiles as $jsonFile) {
        $data = readJson($jsonFile);
        $jsonString = json_encode($data);
        
        if (strpos($jsonString, $imagePath) !== false) {
            return true;
        }
    }
    
    return false;
}

// Delete image from JSON files
function removeImageFromJson($imagePath) {
    // Remove from gallery
    $gallery = readJson('gallery.json');
    $gallery['home'] = array_filter($gallery['home'] ?? [], function($item) use ($imagePath) {
        return $item['image'] !== $imagePath;
    });
    $gallery['portfolio'] = array_filter($gallery['portfolio'] ?? [], function($item) use ($imagePath) {
        return $item['image'] !== $imagePath;
    });
    writeJson('gallery.json', $gallery);
    
    // Remove from services
    $services = readJson('services.json');
    foreach ($services as $key => $service) {
        if (isset($service['image']) && $service['image'] === $imagePath) {
            $services[$key]['image'] = '';
        }
    }
    writeJson('services.json', $services);
    
    // Remove from catalogs
    $catalogs = readJson('catalogs.json');
    foreach ($catalogs as $key => $catalog) {
        if (isset($catalog['cover_image']) && $catalog['cover_image'] === $imagePath) {
            $catalogs[$key]['cover_image'] = '';
        }
        // Check products
        if (isset($catalog['products'])) {
            foreach ($catalog['products'] as $pKey => $product) {
                if (isset($product['image']) && $product['image'] === $imagePath) {
                    $catalogs[$key]['products'][$pKey]['image'] = '';
                }
            }
        }
    }
    writeJson('catalogs.json', $catalogs);
    
    // Remove from customization
    $customization = readJson('customization.json');
    $sections = ['hero', 'about', 'services', 'catalogs', 'gallery', 'portfolio'];
    foreach ($sections as $section) {
        if (isset($customization[$section])) {
            foreach ($customization[$section] as $key => $value) {
                if (is_string($value) && $value === $imagePath) {
                    $customization[$section][$key] = '';
                } elseif ($key === 'partners' && is_array($value)) {
                    // Remove image path from partners array
                    $customization[$section][$key] = array_values(array_filter($value, function($partnerPath) use ($imagePath) {
                        return $partnerPath !== $imagePath;
                    }));
                }
            }
        }
    }
    writeJson('customization.json', $customization);
}

// Get statistics
function getStats() {
    $gallery = readJson('gallery.json');
    $services = readJson('services.json');
    $reviews = readJson('reviews.json');
    $activities = readJson('activities.json');
    $catalogs = readJson('catalogs.json');
    
    $totalActivityServices = 0;
    foreach ($activities as $activity) {
        $totalActivityServices += count($activity['services'] ?? []);
    }
    
    return [
        'home_images' => count($gallery['home'] ?? []),
        'portfolio_images' => count($gallery['portfolio'] ?? []),
        'services' => count($services),
        'activities' => count(array_filter($activities, function($a) { return $a['active'] ?? false; })),
        'activity_services' => $totalActivityServices,
        'catalogs' => count($catalogs['catalogs'] ?? []),
        'pending_reviews' => count($reviews['pending'] ?? []),
        'approved_reviews' => count($reviews['approved'] ?? [])
    ];
}

