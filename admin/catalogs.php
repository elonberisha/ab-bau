<?php
require_once 'functions.php';
requireLogin();

$catalogs = readJson('catalogs.json');
$customization = readJson('customization.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Katalogje';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Handle settings update
    if ($action === 'update_settings') {
        $customization['catalogs']['hero_image'] = sanitize($_POST['catalogs_hero_image'] ?? '');
        $customization['catalogs']['show_in_index'] = isset($_POST['catalogs_show_in_index']);
        $customization['catalogs']['max_catalogs_index'] = intval($_POST['catalogs_max_catalogs_index'] ?? 3);
        $customization['catalogs']['index_title'] = sanitize($_POST['catalogs_index_title'] ?? '');
        $customization['catalogs']['index_description'] = sanitize($_POST['catalogs_index_description'] ?? '');
        $customization['catalogs']['full_title'] = sanitize($_POST['catalogs_full_title'] ?? '');
        $customization['catalogs']['full_description'] = sanitize($_POST['catalogs_full_description'] ?? '');
        
        if (writeJson('customization.json', $customization)) {
            $message = 'Einstellungen erfolgreich gespeichert!';
            $messageType = 'success';
        } else {
            $message = 'Fehler beim Speichern!';
            $messageType = 'error';
        }
        $customization = readJson('customization.json');
    }
    
    if ($action === 'add') {
        $newCatalog = [
            'id' => uniqid(),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'cover_image' => sanitize($_POST['cover_image'] ?? ''),
            'category' => sanitize($_POST['category'] ?? ''),
            'pdf_file' => sanitize($_POST['pdf_path'] ?? ''),
            'products' => [],
            'active' => isset($_POST['active']),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if (!isset($catalogs['catalogs'])) {
            $catalogs['catalogs'] = [];
        }
        $catalogs['catalogs'][] = $newCatalog;
        writeJson('catalogs.json', $catalogs);
        $message = 'Katalog erfolgreich hinzugefügt!';
        $messageType = 'success';
        $catalogs = readJson('catalogs.json');
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $id) {
                    $catalogs['catalogs'][$key]['title'] = sanitize($_POST['title'] ?? '');
                    $catalogs['catalogs'][$key]['description'] = sanitize($_POST['description'] ?? '');
                    $catalogs['catalogs'][$key]['cover_image'] = sanitize($_POST['cover_image'] ?? '');
                    $catalogs['catalogs'][$key]['category'] = sanitize($_POST['category'] ?? '');
                    $catalogs['catalogs'][$key]['active'] = isset($_POST['active']);
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Katalog erfolgreich aktualisiert!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $id) {
                    unset($catalogs['catalogs'][$key]);
                    $catalogs['catalogs'] = array_values($catalogs['catalogs']);
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Katalog erfolgreich gelöscht!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'add_product') {
        $catalogId = $_POST['catalog_id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $catalogId) {
                    $newProduct = [
                        'id' => uniqid(),
                        'name' => sanitize($_POST['name'] ?? ''),
                        'description' => sanitize($_POST['product_description'] ?? ''),
                        'image' => sanitize($_POST['product_image'] ?? ''),
                        'price' => sanitize($_POST['price'] ?? ''),
                        'specifications' => sanitize($_POST['specifications'] ?? ''),
                        'active' => isset($_POST['product_active'])
                    ];
                    
                    if (!isset($catalogs['catalogs'][$key]['products'])) {
                        $catalogs['catalogs'][$key]['products'] = [];
                    }
                    $catalogs['catalogs'][$key]['products'][] = $newProduct;
                    writeJson('catalogs.json', $catalogs);
                    $message = 'Produkt erfolgreich hinzugefügt!';
                    $messageType = 'success';
                    $catalogs = readJson('catalogs.json');
                    break;
                }
            }
        }
    } elseif ($action === 'delete_product') {
        $catalogId = $_POST['catalog_id'] ?? '';
        $productId = $_POST['product_id'] ?? '';
        if (isset($catalogs['catalogs'])) {
            foreach ($catalogs['catalogs'] as $key => $catalog) {
                if ($catalog['id'] === $catalogId && isset($catalog['products'])) {
                    foreach ($catalog['products'] as $pKey => $product) {
                        if ($product['id'] === $productId) {
                            unset($catalogs['catalogs'][$key]['products'][$pKey]);
                            $catalogs['catalogs'][$key]['products'] = array_values($catalogs['catalogs'][$key]['products']);
                            writeJson('catalogs.json', $catalogs);
                            $message = 'Produkt erfolgreich gelöscht!';
                            $messageType = 'success';
                            $catalogs = readJson('catalogs.json');
                            break 2;
                        }
                    }
                }
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
            <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-100 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-400 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Catalog Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-cog text-primary mr-2"></i>
                Katalog Einstellungen
            </h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_settings">
                
                <!-- SECTION 1: INDEX.HTML ONLY -->
                <div class="bg-blue-50 border-l-4 border-primary p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-home text-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-primary">NUR FÜR INDEX.HTML</h3>
                        <span class="ml-2 text-xs bg-primary text-white px-2 py-1 rounded">*</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4 pt-2">
                            <label class="flex items-center">
                                <span class="text-primary font-bold mr-1">*</span>
                                <input type="checkbox" name="catalogs_show_in_index" <?php echo ($customization['catalogs']['show_in_index'] ?? true) ? 'checked' : ''; ?> class="mr-2">
                                <span class="text-sm">Kataloge im Index anzeigen</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Max Kataloge im Index
                            </label>
                            <input type="number" name="catalogs_max_catalogs_index"
                                   value="<?php echo htmlspecialchars($customization['catalogs']['max_catalogs_index'] ?? 3); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Titel im Index
                            </label>
                            <input type="text" name="catalogs_index_title"
                                   value="<?php echo htmlspecialchars($customization['catalogs']['index_title'] ?? 'Unsere Produktkataloge'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Beschreibung im Index
                            </label>
                            <textarea name="catalogs_index_description" rows="2"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['catalogs']['index_description'] ?? 'Durchstöbern Sie unsere umfangreichen Kataloge mit Premium-Materialien'); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: CATALOGS.HTML ONLY -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-700">NUR FÜR CATALOGS.HTML</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Section</label>
                            <div class="space-y-3">
                                <input type="text" name="catalogs_hero_image" data-media-picker="image" placeholder="Hero Image URL"
                                       value="<?php echo htmlspecialchars($customization['catalogs']['hero_image'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <input type="text" name="catalogs_full_title" placeholder="Hero Title"
                                       value="<?php echo htmlspecialchars($customization['catalogs']['full_title'] ?? 'Produktkataloge'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="catalogs_full_description" rows="2" placeholder="Hero Description"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['catalogs']['full_description'] ?? 'Entdecken Sie unsere vollständige Auswahl an Premium-Materialien'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i>Einstellungen speichern
                </button>
            </form>
        </div>

        <!-- Add New Catalog Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Neuen Katalog hinzufügen
            </h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                        <input type="text" name="title" required placeholder="z.B. Keramik Katalog 2024"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategorie</label>
                        <input type="text" name="category" required placeholder="z.B. Keramik, Marmor, Granit"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF Katalogu URL</label>
                    <input type="text" name="pdf_path" placeholder="uploads/catalogs/catalog.pdf"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titelbild</label>
                    <input type="text" name="cover_image" data-media-picker="image" required placeholder="z.B. uploads/katalog-cover.png"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                    <textarea name="description" required rows="3" placeholder="Katalog Beschreibung"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="active" checked>
                        <span>Aktiv</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Katalog speichern
                    </button>
                </div>
            </form>
        </div>

        <!-- Catalogs List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-book text-primary mr-2"></i>
                Katalog Liste (<?php echo count($catalogs['catalogs'] ?? []); ?>)
            </h2>
            
            <?php if (empty($catalogs['catalogs'])): ?>
                <p class="text-gray-500 text-center py-8">Derzeit sind keine Kataloge verfügbar.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($catalogs['catalogs'] as $index => $catalog): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <!-- Header - Always Visible -->
                            <div class="bg-white p-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-3 flex-1">
                                    <button onclick="toggleCatalog(<?php echo $index; ?>)" 
                                            class="text-gray-600 hover:text-primary transition-colors">
                                        <i id="icon-<?php echo $index; ?>" class="fas fa-chevron-down transition-transform"></i>
                                    </button>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($catalog['title']); ?></h3>
                                    <span class="px-2 py-1 rounded text-xs <?php echo $catalog['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $catalog['active'] ? 'Aktiv' : 'Inaktiv'; ?>
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        (<?php echo count($catalog['products'] ?? []); ?> Produkte)
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editCatalog(<?php echo htmlspecialchars(json_encode($catalog)); ?>)" 
                                            class="bg-blue-500 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie löschen möchten?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?php echo $catalog['id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded text-sm hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Collapsible Content -->
                            <div id="catalog-content-<?php echo $index; ?>" class="hidden border-t bg-gray-50">
                                <div class="p-4 space-y-4">
                                    <!-- Catalog Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="font-semibold text-gray-700">Kategorie:</span>
                                            <span class="text-gray-600 ml-2"><?php echo htmlspecialchars($catalog['category']); ?></span>
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-700">Produkte:</span>
                                            <span class="text-gray-600 ml-2"><?php echo count($catalog['products'] ?? []); ?></span>
                                        </div>
                                        <?php if ($catalog['description']): ?>
                                            <div class="md:col-span-2">
                                                <span class="font-semibold text-gray-700">Beschreibung:</span>
                                                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($catalog['description']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($catalog['cover_image']): ?>
                                            <div class="md:col-span-2">
                                                <span class="font-semibold text-gray-700">Foto:</span>
                                                <img src="../<?php echo htmlspecialchars($catalog['cover_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($catalog['title']); ?>"
                                                     class="w-32 h-32 object-cover rounded mt-2 border">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Add Product Form -->
                                    <div class="bg-white p-4 rounded-lg border">
                                <h4 class="font-semibold mb-3">Neues Produkt hinzufügen</h4>
                                <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="hidden" name="action" value="add_product">
                                    <input type="hidden" name="catalog_id" value="<?php echo $catalog['id']; ?>">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Produktname</label>
                                        <input type="text" name="name" required placeholder="z.B. Premium Keramik"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto URL</label>
                                        <input type="text" name="product_image" data-media-picker="image" placeholder="uploads/products/produkt.png"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                                        <textarea name="product_description" required rows="2" placeholder="Produkt Beschreibung"
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Preis</label>
                                        <input type="text" name="price" placeholder="z.B. €25/m² oder €50"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Spezifikationen</label>
                                        <input type="text" name="specifications" placeholder="z.B. 30x30cm, Rutschfest"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="product_active" checked>
                                            <span class="text-sm">Aktiv</span>
                                        </label>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 font-semibold text-sm">
                                            <i class="fas fa-plus mr-2"></i>Produkt hinzufügen
                                        </button>
                                    </div>
                                </form>
                                    </div>

                                    <!-- Products List -->
                                    <?php if (!empty($catalog['products'])): ?>
                                        <div class="bg-white p-4 rounded-lg border">
                                            <h4 class="font-semibold mb-3">Produkte (<?php echo count($catalog['products']); ?>)</h4>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                                <?php foreach ($catalog['products'] as $product): ?>
                                                    <div class="border rounded-lg p-2 hover:shadow-md transition-all">
                                                        <?php if ($product['image']): ?>
                                                            <img src="../<?php echo htmlspecialchars($product['image']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                                 class="w-full h-24 object-cover rounded mb-2">
                                                        <?php endif; ?>
                                                        <h5 class="font-semibold text-sm mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                        <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie löschen möchten?');">
                                                            <input type="hidden" name="action" value="delete_product">
                                                            <input type="hidden" name="catalog_id" value="<?php echo $catalog['id']; ?>">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            <button type="submit" class="w-full bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">
                                                                <i class="fas fa-trash mr-1"></i>Löschen
                                                            </button>
                                                        </form>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Catalog Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Katalog bearbeiten</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                        <input type="text" name="title" id="editTitle" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kategorie</label>
                        <input type="text" name="category" id="editCategory" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Imazhi i Kopertinës</label>
                        <input type="text" name="cover_image" id="editCoverImage" data-media-picker="image" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                        <textarea name="description" id="editDescription" required rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="active" id="editActive">
                            <span>Aktiv</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Abbrechen
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-semibold shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Änderungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleCatalog(index) {
            const content = document.getElementById('catalog-content-' + index);
            const icon = document.getElementById('icon-' + index);
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
        
        function editCatalog(catalog) {
            document.getElementById('editId').value = catalog.id;
            document.getElementById('editTitle').value = catalog.title;
            document.getElementById('editDescription').value = catalog.description;
            document.getElementById('editCoverImage').value = catalog.cover_image;
            document.getElementById('editCategory').value = catalog.category;
            document.getElementById('editActive').checked = catalog.active;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>

