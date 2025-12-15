<?php
require_once 'functions.php';
requireLogin();

$gallery = readJson('gallery.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Galerinë';

// Handle success messages from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'added') {
        $message = 'Fotoja u shtua me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
    } elseif ($_GET['success'] === 'deleted') {
        $message = 'Fotoja u fshi me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
    } elseif ($_GET['success'] === 'moved') {
        $message = 'Fotoja u zhvendos me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
    }
}

// Handle image from media library
if (isset($_GET['use_image'])) {
    $_POST['image_path'] = $_GET['use_image'];
    $_POST['action'] = 'add';
    $_POST['type'] = 'home';
}

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Handle settings update
    if ($action === 'update_settings') {
        $customization['gallery']['hero_image'] = sanitize($_POST['gallery_hero_image'] ?? '');
        $customization['gallery']['show_in_index'] = isset($_POST['gallery_show_in_index']);
        $customization['gallery']['max_images_index'] = intval($_POST['gallery_max_images_index'] ?? 6);
        $customization['gallery']['index_title'] = sanitize($_POST['gallery_index_title'] ?? '');
        $customization['gallery']['index_description'] = sanitize($_POST['gallery_index_description'] ?? '');
        $customization['gallery']['full_title'] = sanitize($_POST['gallery_full_title'] ?? '');
        $customization['gallery']['full_description'] = sanitize($_POST['gallery_full_description'] ?? '');
        
        if (writeJson('customization.json', $customization)) {
            $message = 'Settings u ruajtën me sukses! Ndryshimet reflektohen në index.html';
            $messageType = 'success';
        } else {
            $message = 'Gabim në ruajtje!';
            $messageType = 'error';
        }
        $customization = readJson('customization.json');
    }
    
    if ($action === 'add') {
        // Only use path from media library
        if (isset($_POST['image_path']) && !empty($_POST['image_path'])) {
            $imagePath = sanitize($_POST['image_path']);
            // Verify file exists
            $fullPath = dirname(__DIR__) . '/' . $imagePath;
            if (!file_exists($fullPath)) {
                $message = 'Fotoja nuk u gjet në path-in e dhënë! Kontrollo path-in në Media Library.';
                $messageType = 'error';
            } else {
                $type = $_POST['type'] ?? 'home';
                $title = sanitize($_POST['title'] ?? '');
                
                $newImage = [
                    'id' => uniqid(),
                    'path' => $imagePath,
                    'title' => $title,
                    'date' => date('Y-m-d H:i:s')
                ];
                
                $gallery[$type][] = $newImage;
                writeJson('gallery.json', $gallery);
                header('Location: gallery.php?success=added&type=' . $type);
                exit;
            }
        } else {
            $message = 'Ju lutem jepni path-in e fotos!';
            $messageType = 'error';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $type = $_POST['type'] ?? '';
        
        foreach ($gallery[$type] as $key => $img) {
            if ($img['id'] === $id) {
                deleteImage($img['path']);
                unset($gallery[$type][$key]);
                $gallery[$type] = array_values($gallery[$type]);
                writeJson('gallery.json', $gallery);
                header('Location: gallery.php?success=deleted');
                exit;
            }
        }
    } elseif ($action === 'move') {
        $id = $_POST['id'] ?? '';
        $fromType = $_POST['from_type'] ?? '';
        $toType = $_POST['to_type'] ?? '';
        
        foreach ($gallery[$fromType] as $key => $img) {
            if ($img['id'] === $id) {
                $gallery[$toType][] = $img;
                unset($gallery[$fromType][$key]);
                $gallery[$fromType] = array_values($gallery[$fromType]);
                writeJson('gallery.json', $gallery);
                header('Location: gallery.php?success=moved&to=' . $toType);
                exit;
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
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <script src="js/media-picker.js"></script>
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="ml-64 pt-16 p-6">
        <?php if ($message): ?>
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span>
                    <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                    <?php echo htmlspecialchars($message); ?>
                </span>
                <a href="../index.html" target="_blank" class="text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 hover:underline font-semibold">
                    <i class="fas fa-external-link-alt mr-1"></i>Shiko Faqen
                </a>
            </div>
        <?php endif; ?>

        <!-- Gallery Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-cog text-primary mr-2"></i>
                Gallery Settings
            </h2>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_settings">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-primary font-bold mr-1">*</span>Hero Image URL
                    </label>
                    <input type="text" name="gallery_hero_image" data-media-picker="image"
                           value="<?php echo htmlspecialchars($customization['gallery']['hero_image'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">URL e fotos së hero në faqen Gallery / Projekte</p>
                </div>
                
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="gallery_show_in_index" <?php echo ($customization['gallery']['show_in_index'] ?? false) ? 'checked' : ''; ?> class="mr-2">
                        <span>Shfaq Gallery në Index</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Images në Index</label>
                    <input type="number" name="gallery_max_images_index" value="<?php echo $customization['gallery']['max_images_index'] ?? 6; ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titulli në Index</label>
                    <input type="text" name="gallery_index_title" value="<?php echo htmlspecialchars($customization['gallery']['index_title'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi në Index</label>
                    <textarea name="gallery_index_description" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['gallery']['index_description'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titulli në Faqen e Plotë</label>
                    <input type="text" name="gallery_full_title" value="<?php echo htmlspecialchars($customization['gallery']['full_title'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi në Faqen e Plotë</label>
                    <textarea name="gallery_full_description" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['gallery']['full_description'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i>Ruaj Settings
                </button>
            </form>
        </div>

        <!-- Add New Image Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Shto Foto në Galeri
            </h2>
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Tip:</strong> Uploado fotot në <a href="media-library.php" class="underline font-semibold">Media Library</a> së pari, 
                    pastaj kopjo path-in dhe vendose këtu.
                </p>
            </div>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="add">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Path i Fotos</label>
                    <input type="text" name="image_path" placeholder="P.sh. uploads/foto.png" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <a href="media-library.php" target="_blank" class="text-xs text-primary hover:underline mt-1 block">
                        <i class="fas fa-external-link-alt mr-1"></i>Shiko Media Library për të kopjuar path
                    </a>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                    <input type="text" name="title" placeholder="Titulli i fotos"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lloji</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="home">Kryesore</option>
                        <option value="portfolio">Portfolio</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Ruaj Foto
                    </button>
                </div>
            </form>
        </div>

        <!-- Home Images -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-home text-primary mr-2"></i>
                Fotot Kryesore (<?php echo count($gallery['home'] ?? []); ?>)
            </h2>
            <?php if (empty($gallery['home'])): ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 text-lg mb-2">Nuk ka foto në këtë kategori</p>
                    <p class="text-gray-500 text-sm">Shto foto të reja duke përdorur formularin e sipërm</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($gallery['home'] ?? [] as $img): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-all bg-white">
                            <div class="relative">
                                <img src="../<?php echo htmlspecialchars($img['path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['title'] ?? ''); ?>"
                                     class="w-full h-48 object-cover"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23ddd%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2218%22 dy=%2210.5%22 font-weight=%22bold%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3EFoto%3C/text%3E%3C/svg%3E';">
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium mb-2 text-gray-800"><?php echo htmlspecialchars($img['title'] ?? 'Pa titull'); ?></p>
                                <p class="text-xs text-gray-500 mb-3"><?php echo htmlspecialchars($img['date'] ?? ''); ?></p>
                                <div class="flex flex-col space-y-2">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="move">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($img['id']); ?>">
                                        <input type="hidden" name="from_type" value="home">
                                        <input type="hidden" name="to_type" value="portfolio">
                                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                                            <i class="fas fa-save mr-2"></i>Ruaj & Zhvendos në Portfolio
                                        </button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('A jeni të sigurt që dëshironi ta fshini këtë foto?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($img['id']); ?>">
                                        <input type="hidden" name="type" value="home">
                                        <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                                            <i class="fas fa-trash mr-2"></i>Fshi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Portfolio Images -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-images text-primary mr-2"></i>
                Fotot Portfolio (<?php echo count($gallery['portfolio'] ?? []); ?>)
            </h2>
            <?php if (empty($gallery['portfolio'])): ?>
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600 text-lg mb-2">Nuk ka foto në këtë kategori</p>
                    <p class="text-gray-500 text-sm">Shto foto të reja duke përdorur formularin e sipërm ose zhvendos nga "Kryesore"</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <?php foreach ($gallery['portfolio'] ?? [] as $img): ?>
                        <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-all bg-white">
                            <div class="relative">
                                <img src="../<?php echo htmlspecialchars($img['path']); ?>" 
                                     alt="<?php echo htmlspecialchars($img['title'] ?? ''); ?>"
                                     class="w-full h-48 object-cover"
                                     onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23ddd%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2218%22 dy=%2210.5%22 font-weight=%22bold%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3EFoto%3C/text%3E%3C/svg%3E';">
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-medium mb-2 text-gray-800"><?php echo htmlspecialchars($img['title'] ?? 'Pa titull'); ?></p>
                                <p class="text-xs text-gray-500 mb-3"><?php echo htmlspecialchars($img['date'] ?? ''); ?></p>
                                <div class="flex flex-col space-y-2">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="move">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($img['id']); ?>">
                                        <input type="hidden" name="from_type" value="portfolio">
                                        <input type="hidden" name="to_type" value="home">
                                        <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                                            <i class="fas fa-save mr-2"></i>Ruaj & Zhvendos në Kryesore
                                        </button>
                                    </form>
                                    <form method="POST" onsubmit="return confirm('A jeni të sigurt që dëshironi ta fshini këtë foto?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($img['id']); ?>">
                                        <input type="hidden" name="type" value="portfolio">
                                        <button type="submit" class="w-full bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 font-semibold shadow-md hover:shadow-lg transition-all text-sm">
                                            <i class="fas fa-trash mr-2"></i>Fshi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Informacion</h3>
                    <p class="text-blue-800 mb-3">
                        Fotot që shtoni këtu reflektohen automatikisht në <strong>index.html</strong> përmes API-s. 
                        Fotot "Kryesore" shfaqen në faqen kryesore, ndërsa "Portfolio" shfaqen në faqen e portfolio-s.
                    </p>
                    <a href="../index.html" target="_blank" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-external-link-alt mr-2"></i>Shiko Faqen Publike
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
