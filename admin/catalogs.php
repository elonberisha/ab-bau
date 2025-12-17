<?php
require_once 'functions.php';
requireLogin();

// Initialize message from session if available
$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';

// Clear session messages after retrieving
unset($_SESSION['message']);
unset($_SESSION['messageType']);

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $success = false;

    // --- PAGE SETTINGS UPDATE ---
    if ($action === 'update_section') {
        $data = [
            'hero_image' => sanitize($_POST['hero_image']),
            'show_in_index' => isset($_POST['show_in_index']) ? 1 : 0,
            'max_catalogs_index' => (int)$_POST['max_catalogs_index'],
            'index_title' => sanitize($_POST['index_title']),
            'index_description' => sanitize($_POST['index_description']),
            'full_title' => sanitize($_POST['full_title']),
            'full_description' => sanitize($_POST['full_description'])
        ];

        if (updateSectionData('catalogs_section', $data)) {
            $_SESSION['message'] = 'Seiteneinstellungen wurden erfolgreich aktualisiert!';
            $_SESSION['messageType'] = 'success';
            // Flag to keep section open
            $_SESSION['openSection'] = 'pageSettings';
        } else {
            $_SESSION['message'] = 'Fehler beim Aktualisieren der Einstellungen.';
            $_SESSION['messageType'] = 'error';
        }
    }
    // --- CATALOG CRUD ---
    elseif ($action === 'create_catalog' || $action === 'update_catalog') {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $category = sanitize($_POST['category']);
        $cover_image = sanitize($_POST['cover_image']);
        $pdf_file = sanitize($_POST['pdf_file'] ?? '');
        $active = isset($_POST['active']) ? 1 : 0;
        
        if ($action === 'create_catalog') {
            $stmt = $pdo->prepare("INSERT INTO catalogs (title, description, category, cover_image, pdf_file, active) VALUES (:title, :desc, :cat, :img, :pdf, :active)");
            if ($stmt->execute(['title' => $title, 'desc' => $description, 'cat' => $category, 'img' => $cover_image, 'pdf' => $pdf_file, 'active' => $active])) {
                $_SESSION['message'] = 'Katalog wurde erfolgreich erstellt!';
                $_SESSION['messageType'] = 'success';
            } else {
                $_SESSION['message'] = 'Fehler beim Erstellen.';
                $_SESSION['messageType'] = 'error';
            }
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE catalogs SET title = :title, description = :desc, category = :cat, cover_image = :img, pdf_file = :pdf, active = :active WHERE id = :id");
            if ($stmt->execute(['title' => $title, 'desc' => $description, 'cat' => $category, 'img' => $cover_image, 'pdf' => $pdf_file, 'active' => $active, 'id' => $id])) {
                $_SESSION['message'] = 'Katalog wurde erfolgreich aktualisiert!';
                $_SESSION['messageType'] = 'success';
            } else {
                $_SESSION['message'] = 'Fehler beim Aktualisieren.';
                $_SESSION['messageType'] = 'error';
            }
        }
    } elseif ($action === 'delete_catalog') {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM catalogs WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            $_SESSION['message'] = 'Katalog wurde erfolgreich gelöscht!';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Fehler beim Löschen.';
            $_SESSION['messageType'] = 'error';
        }
    }
    // --- PRODUCT CRUD ---
    elseif ($action === 'create_product' || $action === 'update_product') {
        $catalog_id = (int)$_POST['catalog_id'];
        $name = sanitize($_POST['name']);
        $description = sanitize($_POST['description']);
        $image = sanitize($_POST['image']);
        $price = sanitize($_POST['price']);
        $specifications = sanitize($_POST['specifications']);
        $active = isset($_POST['active']) ? 1 : 0;
        
        if ($action === 'create_product') {
            $stmt = $pdo->prepare("INSERT INTO catalog_products (catalog_id, name, description, image, price, specifications, active) VALUES (:cid, :name, :desc, :img, :price, :spec, :active)");
            if ($stmt->execute(['cid' => $catalog_id, 'name' => $name, 'desc' => $description, 'img' => $image, 'price' => $price, 'spec' => $specifications, 'active' => $active])) {
                $_SESSION['message'] = 'Produkt wurde erfolgreich hinzugefügt!';
                $_SESSION['messageType'] = 'success';
                $_SESSION['openSection'] = 'cat-' . $catalog_id;
            } else {
                $_SESSION['message'] = 'Fehler beim Hinzufügen des Produkts.';
                $_SESSION['messageType'] = 'error';
            }
        } else {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE catalog_products SET name = :name, description = :desc, image = :img, price = :price, specifications = :spec, active = :active WHERE id = :id");
            if ($stmt->execute(['name' => $name, 'desc' => $description, 'img' => $image, 'price' => $price, 'spec' => $specifications, 'active' => $active, 'id' => $id])) {
                $_SESSION['message'] = 'Produkt wurde erfolgreich aktualisiert!';
                $_SESSION['messageType'] = 'success';
                $_SESSION['openSection'] = 'cat-' . $catalog_id;
            } else {
                $_SESSION['message'] = 'Fehler beim Aktualisieren.';
                $_SESSION['messageType'] = 'error';
            }
        }
    } elseif ($action === 'delete_product') {
        $id = (int)$_POST['id'];
        
        // Find catalog id first to keep section open
        $stmt = $pdo->prepare("SELECT catalog_id FROM catalog_products WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $prod = $stmt->fetch();
        if ($prod) {
            $_SESSION['openSection'] = 'cat-' . $prod['catalog_id'];
        }

        $stmt = $pdo->prepare("DELETE FROM catalog_products WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            $_SESSION['message'] = 'Produkt wurde erfolgreich gelöscht!';
            $_SESSION['messageType'] = 'success';
        } else {
            $_SESSION['message'] = 'Fehler beim Löschen.';
            $_SESSION['messageType'] = 'error';
        }
    }

    // Redirect to prevent form resubmission
    header("Location: catalogs.php");
    exit;
}

// Fetch Section Data
$sectionData = getSectionData('catalogs_section');

// Fetch Catalogs
$catalogs = $pdo->query("SELECT * FROM catalogs ORDER BY sort_order ASC, id DESC")->fetchAll();

// Fetch Products for all catalogs (grouped by catalog_id)
$productsByCatalog = [];
$products = $pdo->query("SELECT * FROM catalog_products ORDER BY sort_order ASC, id DESC")->fetchAll();
foreach ($products as $product) {
    $productsByCatalog[$product['catalog_id']][] = $product;
}

$pageSettingsOpen = false;
$openSectionId = $_SESSION['openSection'] ?? null;
unset($_SESSION['openSection']);

if ($openSectionId === 'pageSettings') {
    $pageSettingsOpen = true;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kataloge verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <script src="js/media-picker.js"></script>
    <script>
        function toggleSection(id) {
            const el = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            if (el.classList.contains('hidden')) {
                el.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                el.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        // Auto open section from session
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($openSectionId && $openSectionId !== 'pageSettings'): ?>
                toggleSection('<?php echo $openSectionId; ?>');
            <?php endif; ?>
        });

        // Modal Functions
        function openCatalogModal(mode, data = null) {
            const modal = document.getElementById('catalogModal');
            const form = document.getElementById('catalogForm');
            const title = document.getElementById('catalogModalTitle');
            const btn = document.getElementById('catalogModalBtn');
            
            modal.classList.remove('hidden');
            
            if (mode === 'edit' && data) {
                title.textContent = 'Katalog bearbeiten';
                btn.textContent = 'Änderungen speichern';
                form.elements['action'].value = 'update_catalog';
                form.elements['id'].value = data.id;
                form.elements['title'].value = data.title;
                form.elements['description'].value = data.description;
                form.elements['category'].value = data.category;
                form.elements['cover_image'].value = data.cover_image;
                form.elements['pdf_file'].value = data.pdf_file;
                form.elements['active'].checked = data.active == 1;
                document.getElementById('cat_image_preview').src = data.cover_image ? '../' + data.cover_image : 'assets/img/placeholder.png';
            } else {
                title.textContent = 'Neuen Katalog hinzufügen';
                btn.textContent = 'Katalog erstellen';
                form.reset();
                form.elements['action'].value = 'create_catalog';
                form.elements['id'].value = '';
                document.getElementById('cat_image_preview').src = 'assets/img/placeholder.png';
            }
        }

        function closeCatalogModal() {
            document.getElementById('catalogModal').classList.add('hidden');
        }

        function openProductModal(mode, catalogId, data = null) {
            const modal = document.getElementById('productModal');
            const form = document.getElementById('productForm');
            const title = document.getElementById('productModalTitle');
            const btn = document.getElementById('productModalBtn');
            
            modal.classList.remove('hidden');
            form.elements['catalog_id'].value = catalogId;
            
            if (mode === 'edit' && data) {
                title.textContent = 'Produkt bearbeiten';
                btn.textContent = 'Änderungen speichern';
                form.elements['action'].value = 'update_product';
                form.elements['id'].value = data.id;
                form.elements['name'].value = data.name;
                form.elements['description'].value = data.description;
                form.elements['image'].value = data.image;
                form.elements['price'].value = data.price;
                form.elements['specifications'].value = data.specifications;
                form.elements['active'].checked = data.active == 1;
                document.getElementById('prod_image_preview').src = data.image ? '../' + data.image : 'assets/img/placeholder.png';
            } else {
                title.textContent = 'Neues Produkt hinzufügen';
                btn.textContent = 'Produkt hinzufügen';
                form.reset();
                form.elements['action'].value = 'create_product';
                form.elements['id'].value = '';
                form.elements['catalog_id'].value = catalogId;
                document.getElementById('prod_image_preview').src = 'assets/img/placeholder.png';
            }
        }

        function closeProductModal() {
            document.getElementById('productModal').classList.add('hidden');
        }
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-900">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0 bg-white border-r border-gray-200">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center">
                        <span class="bg-orange-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-book text-orange-600"></i>
                        </span>
                        Kataloge verwalten
                    </h1>
                </div>
                <button onclick="openCatalogModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center text-sm">
                    <i class="fas fa-plus mr-2"></i> Katalog hinzufügen
                </button>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?> flex items-center shadow-sm animate-fade-in">
                        <div class="flex-shrink-0 mr-3">
                            <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> text-xl"></i>
                        </div>
                        <span class="font-medium"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <!-- PAGE CUSTOMIZATION SECTION -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
                    <div class="bg-orange-50/50 p-4 border-b border-orange-100 flex justify-between items-center cursor-pointer" onclick="toggleSection('pageSettings')">
                        <h2 class="text-lg font-bold text-orange-900 flex items-center">
                            <i class="fas fa-cog mr-2"></i> Seiteneinstellungen (Titel & Texte)
                        </h2>
                        <i id="icon-pageSettings" class="fas <?php echo $pageSettingsOpen ? 'fa-chevron-up' : 'fa-chevron-down'; ?> text-orange-400"></i>
                    </div>
                    
                    <div id="pageSettings" class="<?php echo $pageSettingsOpen ? '' : 'hidden'; ?> border-t border-gray-100">
                        <form method="POST" class="p-6">
                            <input type="hidden" name="action" value="update_section">
                            
                            <div class="space-y-8">
                                <!-- Index Section -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Bereich auf der Startseite (Index)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex items-center mb-4 col-span-2">
                                            <input type="checkbox" name="show_in_index" id="show_in_index" <?php echo ($sectionData['show_in_index'] ?? 1) ? 'checked' : ''; ?> class="form-checkbox h-5 w-5 text-orange-600 rounded">
                                            <label for="show_in_index" class="ml-2 text-gray-700 font-medium">Auf der Startseite anzeigen</label>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Titel auf der Startseite</label>
                                            <input type="text" name="index_title" value="<?php echo htmlspecialchars($sectionData['index_title'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Anzahl der Kataloge auf der Startseite</label>
                                            <input type="number" name="max_catalogs_index" value="<?php echo htmlspecialchars($sectionData['max_catalogs_index'] ?? 3); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Beschreibung auf der Startseite</label>
                                            <textarea name="index_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($sectionData['index_description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Page Hero -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2">Vollständige Seite (Catalogs.html)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Haupttitel</label>
                                            <input type="text" name="full_title" value="<?php echo htmlspecialchars($sectionData['full_title'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hero-Bild</label>
                                            <div class="flex gap-2">
                                                <input type="text" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($sectionData['hero_image'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg" readonly>
                                                <button type="button" onclick="openMediaPicker('hero_image')" class="bg-gray-100 px-3 py-2 rounded border border-gray-300 hover:bg-gray-200"><i class="fas fa-image"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Beschreibung (Untertitel)</label>
                                            <textarea name="full_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($sectionData['full_description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-black font-bold py-2 px-6 rounded-lg shadow-md transform hover:-translate-y-0.5 transition-all">
                                    <i class="fas fa-save mr-2"></i> Einstellungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Catalogs List -->
                <?php if (empty($catalogs)): ?>
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-book-open text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Keine Kataloge registriert</h3>
                        <p class="text-gray-500 mt-1">Beginnen Sie, indem Sie einen neuen Katalog hinzufügen.</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($catalogs as $index => $catalog): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 flex items-center justify-between bg-gray-50 hover:bg-white transition-colors cursor-pointer" onclick="toggleSection('cat-<?php echo $catalog['id']; ?>')">
                                    <div class="flex items-center space-x-4">
                                        <i id="icon-cat-<?php echo $catalog['id']; ?>" class="fas fa-chevron-down text-gray-400"></i>
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            <?php if ($catalog['cover_image']): ?>
                                                <img src="../<?php echo htmlspecialchars($catalog['cover_image']); ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <div class="flex items-center justify-center h-full text-gray-400"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($catalog['title']); ?></h3>
                                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                                <span class="bg-gray-200 px-2 py-0.5 rounded text-xs text-gray-700"><?php echo htmlspecialchars($catalog['category']); ?></span>
                                                <span>• <?php echo isset($productsByCatalog[$catalog['id']]) ? count($productsByCatalog[$catalog['id']]) : 0; ?> Produkte</span>
                                                <?php if (!$catalog['active']): ?>
                                                    <span class="text-red-500 font-medium">• Inaktiv</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="../catalog-detail.html?id=<?php echo $catalog['id']; ?>" target="_blank" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Auf Website anzeigen">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <button onclick="event.stopPropagation(); openCatalogModal('edit', <?php echo htmlspecialchars(json_encode($catalog), ENT_QUOTES, 'UTF-8'); ?>)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Bearbeiten">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Katalog löschen möchten?');" onclick="event.stopPropagation();">
                                            <input type="hidden" name="action" value="delete_catalog">
                                            <input type="hidden" name="id" value="<?php echo $catalog['id']; ?>">
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Löschen">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <!-- Catalog Details & Products -->
                                <div id="cat-<?php echo $catalog['id']; ?>" class="hidden border-t border-gray-100 p-6">
                                    <div class="mb-6">
                                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Beschreibung</h4>
                                        <p class="text-gray-600"><?php echo htmlspecialchars($catalog['description']); ?></p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-sm font-bold text-gray-500 uppercase">Produkte im Katalog</h4>
                                        <button onclick="openProductModal('create', <?php echo $catalog['id']; ?>)" class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-1.5 px-3 rounded-lg transition-colors">
                                            <i class="fas fa-plus mr-1"></i> Produkt hinzufügen
                                        </button>
                                    </div>
                                    
                                    <?php if (empty($productsByCatalog[$catalog['id']] ?? [])): ?>
                                        <div class="bg-gray-50 rounded-lg p-4 text-center text-gray-500 text-sm">
                                            Dieser Katalog hat noch keine Produkte.
                                        </div>
                                    <?php else: ?>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            <?php foreach ($productsByCatalog[$catalog['id']] as $product): ?>
                                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex gap-3 group hover:border-blue-100 transition-colors">
                                                    <div class="w-16 h-16 bg-white rounded-md overflow-hidden flex-shrink-0 border border-gray-200">
                                                        <?php if ($product['image']): ?>
                                                            <img src="../<?php echo htmlspecialchars($product['image']); ?>" class="w-full h-full object-cover">
                                                        <?php else: ?>
                                                            <div class="flex items-center justify-center h-full text-gray-300"><i class="fas fa-image"></i></div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <h5 class="font-bold text-gray-900 truncate"><?php echo htmlspecialchars($product['name']); ?></h5>
                                                        <p class="text-xs text-gray-500 line-clamp-1"><?php echo htmlspecialchars($product['description']); ?></p>
                                                        <div class="flex justify-between items-center mt-1">
                                                            <span class="text-xs font-medium text-orange-600"><?php echo htmlspecialchars($product['price']); ?></span>
                                                            <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                                <button onclick="openProductModal('edit', <?php echo $catalog['id']; ?>, <?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8'); ?>)" class="text-blue-600 hover:text-blue-800 p-1">
                                                                    <i class="fas fa-pencil-alt text-xs"></i>
                                                                </button>
                                                                <form method="POST" class="inline" onsubmit="return confirm('Produkt löschen?');">
                                                                    <input type="hidden" name="action" value="delete_product">
                                                                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1">
                                                                        <i class="fas fa-times text-xs"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Catalog Modal -->
    <div id="catalogModal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="catalogModalTitle">Shto Katalog</h3>
                <button onclick="closeCatalogModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="catalogForm" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" value="create_catalog">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie</label>
                        <input type="text" name="category" placeholder="z.B. Keramik" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PDF-Link (Optional)</label>
                        <input type="text" name="pdf_file" placeholder="Link zum Download" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Përshkrimi</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                    </div>

                    <!-- Image Picker -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Titelbild</label>
                        <div class="flex gap-4">
                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-primary transition-colors bg-gray-50 p-1 w-32 h-32 flex-shrink-0" onclick="openMediaPicker('cat_cover_image')">
                                <img src="assets/img/placeholder.png" id="cat_image_preview" class="w-full h-full object-cover rounded">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10">
                                    <span class="bg-white text-gray-800 text-xs font-bold px-2 py-1 rounded shadow">Auswählen</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="text" id="cat_cover_image" name="cover_image" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Bild-URL" readonly>
                                <p class="text-xs text-gray-500">Klicken Sie auf das Bild links, um aus der Bibliothek auszuwählen.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-span-2 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-gray-700 font-medium">Katalog aktiv</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeCatalogModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit" id="catalogModalBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Katalog erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="productModalTitle">Shto Produkt</h3>
                <button onclick="closeProductModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="productForm" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" value="create_product">
                <input type="hidden" name="id" value="">
                <input type="hidden" name="catalog_id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produktname</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preis</label>
                        <input type="text" name="price" placeholder="z.B. €25/m²" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Spezifikationen</label>
                        <input type="text" name="specifications" placeholder="z.B. 60x60cm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Përshkrimi</label>
                        <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                    </div>

                    <!-- Image Picker -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produktbild</label>
                        <div class="flex gap-4">
                            <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-primary transition-colors bg-gray-50 p-1 w-32 h-32 flex-shrink-0" onclick="openMediaPicker('prod_image')">
                                <img src="assets/img/placeholder.png" id="prod_image_preview" class="w-full h-full object-cover rounded">
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10">
                                    <span class="bg-white text-gray-800 text-xs font-bold px-2 py-1 rounded shadow">Auswählen</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="text" id="prod_image" name="image" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2" placeholder="Bild-URL" readonly>
                                <p class="text-xs text-gray-500">Klicken Sie auf das Bild links, um aus der Bibliothek auszuwählen.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-span-2 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-gray-700 font-medium">Produkt aktiv</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeProductModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit" id="productModalBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Produkt hinzufügen
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
