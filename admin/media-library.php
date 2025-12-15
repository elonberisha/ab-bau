<?php
require_once 'functions.php';
requireLogin();

$pageTitle = 'Media Library';
$message = '';
$messageType = '';

// Get current folder from query string
$currentFolder = isset($_GET['folder']) ? sanitize($_GET['folder']) : '';
$currentFolderPath = $currentFolder ? 'uploads/' . $currentFolder : 'uploads';

// Handle folder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_folder') {
    $folderName = sanitize($_POST['folder_name'] ?? '');
    if (empty($folderName)) {
        $message = 'Emri i folderit nuk mund të jetë bosh!';
        $messageType = 'error';
    } else {
        // Sanitize folder name
        $folderName = preg_replace('/[^a-zA-Z0-9_-]/', '', $folderName);
        $folderPath = dirname(__DIR__) . '/' . $currentFolderPath . '/' . $folderName;
        
        if (file_exists($folderPath)) {
            $message = 'Ky folder ekziston tashmë!';
            $messageType = 'error';
        } else {
            if (mkdir($folderPath, 0755, true)) {
                header('Location: media-library.php?folder=' . urlencode($currentFolder) . '&success=folder_created');
                exit;
            } else {
                $message = 'Gabim në krijimin e folderit!';
                $messageType = 'error';
            }
        }
    }
}

// Handle folder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_folder') {
    $folderToDelete = sanitize($_POST['folder_path'] ?? '');
    if (!empty($folderToDelete)) {
        // Normalize path - ensure it starts with uploads/
        $folderPath = str_replace('\\', '/', $folderToDelete);
        $folderPath = ltrim($folderPath, './\\');
        
        // If path doesn't start with uploads/, add it
        if (strpos($folderPath, 'uploads/') !== 0) {
            $folderPath = 'uploads/' . ltrim($folderPath, '/\\');
        }
        
        // Build full path
        $baseDir = dirname(__DIR__);
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $folderPath);
        
        // Try alternative paths if first doesn't exist
        if (!is_dir($fullPath)) {
            $altPath1 = $baseDir . '/' . $folderPath;
            $altPath2 = $baseDir . '\\' . str_replace('/', '\\', $folderPath);
            
            if (is_dir($altPath1)) {
                $fullPath = $altPath1;
            } elseif (is_dir($altPath2)) {
                $fullPath = $altPath2;
            }
        }
        
        if (is_dir($fullPath)) {
            // Check if folder is empty
            $files = array_diff(scandir($fullPath), ['.', '..']);
            if (empty($files)) {
                if (@rmdir($fullPath)) {
                    header('Location: media-library.php?folder=' . urlencode($currentFolder) . '&success=folder_deleted');
                    exit;
                } else {
                    $error = error_get_last();
                    $errorMsg = $error ? $error['message'] : 'Unknown error';
                    $message = 'Gabim në fshirjen e folderit! ' . htmlspecialchars($errorMsg);
                    $messageType = 'error';
                }
            } else {
                $message = 'Folderi nuk është bosh! Fshini fillimisht të gjitha fotot.';
                $messageType = 'error';
            }
        } else {
            $message = 'Folderi nuk ekziston! Path: ' . htmlspecialchars($folderPath);
            $messageType = 'error';
        }
    }
}

// Handle file deletion (must be checked before upload to avoid conflicts)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $rawPath = isset($_POST['path']) ? $_POST['path'] : '';
    $path = trim($rawPath);
    
    // Debug - show in message for now
    $debugInfo = [];
    $debugInfo[] = "POST received!";
    $debugInfo[] = "Action: " . ($_POST['action'] ?? 'NOT SET');
    $debugInfo[] = "Raw path: " . $rawPath;
    $debugInfo[] = "Trimmed path: " . $path;
    
    if (empty($path)) {
        $message = 'Path-i i fotos është bosh!';
        $messageType = 'error';
    } else {
        // Normalize path - remove any leading slashes, dots, or backslashes
        $path = str_replace('\\', '/', $path); // Normalize slashes for Windows
        $path = ltrim($path, './\\');
        
        // Preserve the original path format as stored
        $originalPath = $path;
        
        // If path doesn't start with uploads/, add it
        if (strpos($path, 'uploads/') !== 0) {
            $path = 'uploads/' . ltrim($path, '/\\');
        }
        
        // Build full path - use DIRECTORY_SEPARATOR for cross-platform compatibility
        $baseDir = dirname(__DIR__);
        $fullPath = $baseDir . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $path);
        
        $debugInfo[] = "Normalized: " . $path;
        $debugInfo[] = "Full path: " . $fullPath;
        $debugInfo[] = "Base dir: " . $baseDir;
        
        // Check if file exists
        $fileExists = file_exists($fullPath);
        $isFile = $fileExists ? is_file($fullPath) : false;
        $isWritable = $fileExists ? is_writable($fullPath) : false;
        
        if (!$fileExists) {
            // Try alternative path formats
            $altPath1 = $baseDir . '/' . $path;
            $altPath2 = $baseDir . '\\' . str_replace('/', '\\', $path);
            $altPath3 = $baseDir . '/' . $originalPath;
            
            $debugInfo[] = "Alt1: " . $altPath1 . " - " . (file_exists($altPath1) ? 'EXISTS' : 'NOT FOUND');
            $debugInfo[] = "Alt2: " . $altPath2 . " - " . (file_exists($altPath2) ? 'EXISTS' : 'NOT FOUND');
            $debugInfo[] = "Alt3: " . $altPath3 . " - " . (file_exists($altPath3) ? 'EXISTS' : 'NOT FOUND');
            
            if (file_exists($altPath1)) {
                $fullPath = $altPath1;
                $fileExists = true;
                $isFile = is_file($fullPath);
                $isWritable = is_writable($fullPath);
            } elseif (file_exists($altPath2)) {
                $fullPath = $altPath2;
                $fileExists = true;
                $isFile = is_file($fullPath);
                $isWritable = is_writable($fullPath);
            } elseif (file_exists($altPath3)) {
                $fullPath = $altPath3;
                $fileExists = true;
                $isFile = is_file($fullPath);
                $isWritable = is_writable($fullPath);
            }
        }
        
        if (!$fileExists) {
            $message = 'Fotoja nuk ekziston!<br><br>Debug Info:<br>' . implode('<br>', array_map('htmlspecialchars', $debugInfo));
            $messageType = 'error';
        } elseif (!$isFile) {
            $message = 'Path-i nuk është një file: ' . htmlspecialchars($path);
            $messageType = 'error';
        } elseif (!$isWritable && $fileExists) {
            $message = 'Fotoja nuk mund të fshihet! Nuk ka permissions për fshirje. Path: ' . htmlspecialchars($path);
            $messageType = 'error';
        } else {
            // Check if image is used and remove from JSON files
            if (isImageUsed($path)) {
                removeImageFromJson($path);
            }
            
            // Delete the physical file
            $deleted = @unlink($fullPath);
            $debugInfo[] = "Unlink result: " . ($deleted ? 'SUCCESS' : 'FAILED');
            
            if ($deleted) {
                // Redirect to prevent form resubmission on refresh
                $redirectUrl = 'media-library.php?success=deleted';
                if ($currentFolder) {
                    $redirectUrl .= '&folder=' . urlencode($currentFolder);
                }
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                $error = error_get_last();
                $errorMsg = $error ? $error['message'] : 'Unknown error';
                $perms = $fileExists ? substr(sprintf('%o', fileperms($fullPath)), -4) : 'N/A';
                $debugInfo[] = "Error: " . $errorMsg;
                $debugInfo[] = "Permissions: " . $perms;
                $message = 'Gabim në fshirjen e fotos!<br><br>Debug Info:<br>' . implode('<br>', array_map('htmlspecialchars', $debugInfo));
                $messageType = 'error';
            }
        }
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === 0) {
        $customFilename = sanitize($_POST['custom_filename'] ?? '');
        $uploadFolder = sanitize($_POST['upload_folder'] ?? '');
        $targetFolder = $uploadFolder ? 'uploads/' . $uploadFolder : 'uploads';
        $result = uploadImage($_FILES['upload_file'], $targetFolder, $customFilename);
        
        if ($result['success']) {
            $imagePath = $result['path'];
            if (isset($result['duplicate']) && $result['duplicate']) {
                $message = 'Kjo foto ekziston tashmë! Path: ' . $imagePath;
                $messageType = 'success';
            } else {
                $message = 'Fotoja u uploadua me sukses! Path: ' . $imagePath;
                $messageType = 'success';
            }
            
            // Redirect to prevent form resubmission on refresh
            $redirectUrl = 'media-library.php?success=uploaded&path=' . urlencode($imagePath);
            if ($currentFolder) {
                $redirectUrl .= '&folder=' . urlencode($currentFolder);
            }
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $message = $result['error'] ?? 'Gabim në upload të fotos!';
            $messageType = 'error';
        }
    } elseif (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] !== 0) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'Fotoja është shumë e madhe (server limit)',
            UPLOAD_ERR_FORM_SIZE => 'Fotoja është shumë e madhe',
            UPLOAD_ERR_PARTIAL => 'Fotoja u uploadua vetëm pjesërisht',
            UPLOAD_ERR_NO_FILE => 'Nuk u zgjodh asnjë foto',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder i përkohshëm mungon',
            UPLOAD_ERR_CANT_WRITE => 'Gabim në shkrim në disk',
            UPLOAD_ERR_EXTENSION => 'Upload u ndal nga një extension'
        ];
        $message = $errorMessages[$_FILES['upload_file']['error']] ?? 'Gabim i panjohur në upload';
        $messageType = 'error';
    }
}

// Handle success messages from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'uploaded') {
        $uploadedPath = $_GET['path'] ?? '';
        $message = 'Fotoja u uploadua me sukses! Path: ' . $uploadedPath;
        $messageType = 'success';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Fotoja u fshi me sukses nga folderi dhe të gjitha referencat!';
        $messageType = 'success';
    } elseif ($_GET['success'] === 'folder_created') {
        $message = 'Folderi u krijua me sukses!';
        $messageType = 'success';
    } elseif ($_GET['success'] === 'folder_deleted') {
        $message = 'Folderi u fshi me sukses!';
        $messageType = 'success';
    }
}

// Get current folder from query string
$currentFolder = isset($_GET['folder']) ? sanitize($_GET['folder']) : '';
$currentFolderPath = $currentFolder ? 'uploads/' . $currentFolder : 'uploads';

// Get all folders and images in current directory
$currentDir = dirname(__DIR__) . '/' . $currentFolderPath;
$folders = [];
$images = [];
$seenPaths = []; // Track seen paths to avoid duplicates

if (is_dir($currentDir)) {
    $items = scandir($currentDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $itemPath = $currentDir . '/' . $item;
        $relativePath = $currentFolderPath . '/' . $item;
        
        if (is_dir($itemPath)) {
            // Count images in folder
            $imageCount = 0;
            $subItems = scandir($itemPath);
            foreach ($subItems as $subItem) {
                if ($subItem !== '.' && $subItem !== '..') {
                    $ext = strtolower(pathinfo($subItem, PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $imageCount++;
                    }
                }
            }
            
            $folders[] = [
                'name' => $item,
                'path' => $currentFolder ? $currentFolder . '/' . $item : $item,
                'image_count' => $imageCount
            ];
        } elseif (is_file($itemPath) && in_array(strtolower(pathinfo($item, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            // Skip if already seen (prevent duplicates)
            if (in_array($relativePath, $seenPaths)) {
                continue;
            }
            
            $seenPaths[] = $relativePath;
            $images[] = [
                'name' => $item,
                'path' => $relativePath,
                'size' => filesize($itemPath),
                'date' => date('Y-m-d H:i:s', filemtime($itemPath))
            ];
        }
    }
    
    // Sort folders by name
    usort($folders, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    // Sort images by date, newest first
    usort($images, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// Get breadcrumb path
$breadcrumbs = [];
if ($currentFolder) {
    $parts = explode('/', $currentFolder);
    $path = '';
    foreach ($parts as $part) {
        $path .= ($path ? '/' : '') . $part;
        $breadcrumbs[] = [
            'name' => $part,
            'path' => $path
        ];
    }
}

// Format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
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
    <?php 
    $isPickerMode = isset($_GET['picker']) && $_GET['picker'] === 'true';
    $targetInput = $_GET['target'] ?? '';
    
    if (!$isPickerMode): 
        include 'includes/sidebar.php';
        include 'includes/header.php';
    endif;
    ?>

    <div class="<?php echo $isPickerMode ? 'p-6' : 'ml-64 pt-16 p-6'; ?>">
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <?php echo $message; // Allow HTML for debug messages ?>
            </div>
        <?php endif; ?>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <strong>DEBUG:</strong> POST request received!<br>
                Action: <?php echo htmlspecialchars($_POST['action'] ?? 'NOT SET'); ?><br>
                Path: <?php echo htmlspecialchars($_POST['path'] ?? 'NOT SET'); ?><br>
                All POST data: <pre><?php echo htmlspecialchars(print_r($_POST, true)); ?></pre>
            </div>
        <?php endif; ?>

        <!-- Breadcrumb Navigation -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex items-center space-x-2 text-sm">
                <a href="media-library.php" class="text-primary hover:underline">
                    <i class="fas fa-home"></i> Media Library
                </a>
                <?php foreach ($breadcrumbs as $crumb): ?>
                    <span class="text-gray-400">/</span>
                    <a href="media-library.php?folder=<?php echo urlencode($crumb['path']); ?>" class="text-primary hover:underline">
                        <?php echo htmlspecialchars($crumb['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Create Folder Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-4">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-folder-plus text-primary mr-2"></i>
                Krijoni Folder të Ri
            </h2>
            <form method="POST" class="flex items-end space-x-4">
                <input type="hidden" name="action" value="create_folder">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Emri i Folderit</label>
                    <input type="text" name="folder_name" required
                           pattern="[a-zA-Z0-9_-]+"
                           placeholder="p.sh. hero-images, products, gallery"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">Vetëm shkronja, numra, _ dhe -</p>
                </div>
                <button type="submit" class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-primary-dark font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-folder-plus mr-2"></i>Krijo Folder
                </button>
            </form>
        </div>

        <!-- Upload Section -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-upload text-primary mr-2"></i>
                Upload Foto të Re
            </h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Zgjidh Foto</label>
                        <input type="file" name="upload_file" accept="image/*" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                               onchange="updateFilename(this)">
                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG, GIF, WEBP (max 5MB)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Emri i Fotos (opsionale)</label>
                        <input type="text" name="custom_filename" id="customFilename" 
                               placeholder="p.sh. hero-image, logo, product-1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">Lër bosh për emër automatik</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Folder (aktual: <?php echo htmlspecialchars($currentFolder ?: 'uploads'); ?>)</label>
                        <input type="hidden" name="upload_folder" value="<?php echo htmlspecialchars($currentFolder); ?>">
                        <div class="px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                            <span class="text-sm text-gray-600"><?php echo htmlspecialchars($currentFolder ?: 'uploads'); ?></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Foto do të ruhet në folderin aktual</p>
                    </div>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-upload mr-2"></i>Upload Foto
                </button>
            </form>
        </div>
        
        <script>
            function updateFilename(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];
                    const originalName = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                    const customInput = document.getElementById('customFilename');
                    if (!customInput.value) {
                        // Suggest a clean filename
                        const cleanName = originalName.toLowerCase()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        customInput.placeholder = cleanName;
                    }
                }
            }
        </script>

        <!-- Media Library -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-images text-primary mr-2"></i>
                Media Library 
                <span class="ml-2 text-sm font-normal text-gray-600">
                    (<?php echo count($folders); ?> folder<?php echo count($folders) !== 1 ? 'a' : ''; ?>, <?php echo count($images); ?> foto)
                </span>
            </h2>
            
            <?php if (empty($folders) && empty($images)): ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 text-lg mb-2">Nuk ka foldera ose foto</p>
                    <p class="text-gray-500 text-sm">Krijoni folder ose uploadoni foto duke përdorur formularët e sipërm</p>
                </div>
            <?php else: ?>
                <!-- Folders -->
                <?php if (!empty($folders)): ?>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-700">
                            <i class="fas fa-folder text-yellow-500 mr-2"></i>Foldera
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <?php foreach ($folders as $folder): ?>
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-all bg-white">
                                    <a href="media-library.php?folder=<?php echo urlencode($folder['path']); ?>" class="block">
                                        <div class="p-4 text-center">
                                            <i class="fas fa-folder text-6xl text-yellow-500 mb-3"></i>
                                            <p class="text-sm font-medium mb-1 text-gray-800 truncate" title="<?php echo htmlspecialchars($folder['name']); ?>">
                                                <?php echo htmlspecialchars($folder['name']); ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?php echo $folder['image_count']; ?> foto</p>
                                        </div>
                                    </a>
                                    <div class="px-3 pb-3">
                                        <button onclick="deleteFolder('<?php echo htmlspecialchars($folder['path']); ?>', '<?php echo htmlspecialchars($folder['name']); ?>')" 
                                                class="w-full bg-red-500 text-white px-3 py-2 rounded text-sm hover:bg-red-600">
                                            <i class="fas fa-trash mr-1"></i>Fshi Folder
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Images -->
                <?php if (!empty($images)): ?>
                    <div>
                        <h3 class="text-lg font-semibold mb-3 text-gray-700">
                            <i class="fas fa-images text-blue-500 mr-2"></i>Foto
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            <?php foreach ($images as $image): ?>
                                <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-all bg-white">
                                    <div class="relative">
                                        <img src="../<?php echo htmlspecialchars($image['path']); ?>" 
                                             alt="<?php echo htmlspecialchars($image['name']); ?>"
                                             class="w-full h-48 object-cover">
                                        <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                                            <?php echo formatFileSize($image['size']); ?>
                                        </div>
                                    </div>
                                    <div class="p-3">
                                        <p class="text-sm font-medium mb-2 text-gray-800 truncate" title="<?php echo htmlspecialchars($image['name']); ?>">
                                            <?php echo htmlspecialchars($image['name']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500 mb-3"><?php echo htmlspecialchars($image['date']); ?></p>
                                        
                                        <!-- Path Input (Copyable) -->
                                        <div class="mb-3">
                                            <label class="block text-xs font-medium text-gray-700 mb-1">Path:</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="text" 
                                                       value="<?php echo htmlspecialchars($image['path']); ?>" 
                                                       readonly
                                                       class="flex-1 px-2 py-1 text-xs border border-gray-300 rounded bg-gray-50"
                                                       id="path-<?php echo md5($image['path']); ?>">
                                                <button onclick="copyPath('<?php echo md5($image['path']); ?>')" 
                                                        class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600"
                                                        title="Copy Path">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <?php if ($isPickerMode): ?>
                                                <button onclick="selectAndClose('<?php echo htmlspecialchars($image['path']); ?>')" 
                                                        class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600"
                                                        title="Select">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Quick Actions -->
                                        <div class="flex space-x-2">
                                            <a href="gallery.php?use_image=<?php echo urlencode($image['path']); ?>" 
                                               class="flex-1 bg-primary text-white px-3 py-2 rounded text-sm hover:bg-primary-dark text-center">
                                                <i class="fas fa-plus mr-1"></i>Përdor në Galeri
                                            </a>
                                            <button onclick="deleteImage('<?php echo htmlspecialchars($image['path']); ?>', '<?php echo htmlspecialchars($image['name']); ?>')" 
                                                    class="bg-red-500 text-white px-3 py-2 rounded text-sm hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function deleteFolder(path, name) {
            if (confirm('A jeni të sigurt që dëshironi ta fshini këtë folder?\n\n' + name + '\n\nVërejtje: Folderi duhet të jetë bosh për ta fshirë.')) {
                const form = document.createElement('form');
                form.method = 'POST';
                let actionUrl = 'media-library.php';
                <?php if ($currentFolder): ?>
                actionUrl += '?folder=<?php echo urlencode($currentFolder); ?>';
                <?php endif; ?>
                form.action = actionUrl;
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete_folder';
                form.appendChild(actionInput);
                
                const pathInput = document.createElement('input');
                pathInput.type = 'hidden';
                pathInput.name = 'folder_path';
                // Ensure path is sent correctly (it should be relative like "About" or "About/SubFolder")
                pathInput.value = path;
                form.appendChild(pathInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function copyPath(id) {
            const input = document.getElementById('path-' + id);
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            
            // Show feedback
            const button = input.nextElementSibling;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('bg-blue-500');
            button.classList.add('bg-green-500');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500');
            }, 2000);
        }
        
        function selectAndClose(path) {
            if (window.opener && window.opener.selectMediaPath) {
                window.opener.selectMediaPath(path);
                window.close();
            } else {
                // Fallback: copy to clipboard
                copyPath(path);
                alert('Path u kopjua: ' + path);
            }
        }
        
        function deleteImage(path, name) {
            console.log('deleteImage called with path:', path, 'name:', name);
            
            if (!confirm('A jeni të sigurt që dëshironi ta fshini këtë foto?\n\n' + name + '\n\nPath: ' + path)) {
                console.log('User cancelled deletion');
                return;
            }
            
            console.log('User confirmed deletion, creating form...');
            
            // Create a form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.enctype = 'application/x-www-form-urlencoded';
            let actionUrl = 'media-library.php';
            <?php if ($isPickerMode): ?>
            actionUrl += '?picker=true&target=<?php echo urlencode($targetInput); ?>';
            <?php elseif ($currentFolder): ?>
            actionUrl += '?folder=<?php echo urlencode($currentFolder); ?>';
            <?php endif; ?>
            form.action = actionUrl;
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            form.appendChild(actionInput);
            
            const pathInput = document.createElement('input');
            pathInput.type = 'hidden';
            pathInput.name = 'path';
            pathInput.value = path;
            form.appendChild(pathInput);
            
            // Add form to body
            form.style.display = 'none';
            document.body.appendChild(form);
            
            // Debug
            console.log('Deleting image:', path);
            console.log('Form action:', actionUrl);
            console.log('Form element:', form);
            console.log('Action input value:', actionInput.value);
            console.log('Path input value:', pathInput.value);
            
            // Submit form immediately
            try {
                console.log('Submitting form...');
                form.submit();
                console.log('Form submitted successfully');
            } catch (e) {
                console.error('Error submitting form:', e);
                alert('Gabim në dërgimin e formës: ' + e.message);
            }
        }
    </script>
</body>
</html>

