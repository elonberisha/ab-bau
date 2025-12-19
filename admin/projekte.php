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

// Handle section update FIRST (before CRUD)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_section') {
    try {
        // First, ensure the table exists
        try {
            $pdo->query("SELECT 1 FROM portfolio_section LIMIT 1");
        } catch (PDOException $e) {
            // Table doesn't exist, create it
            $createTableSQL = "CREATE TABLE IF NOT EXISTS `portfolio_section` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `hero_image` varchar(500) DEFAULT NULL,
                `show_in_index` tinyint(1) DEFAULT 1,
                `max_items_index` int(11) DEFAULT 6,
                `index_title` varchar(255) DEFAULT NULL,
                `index_description` text DEFAULT NULL,
                `full_title` varchar(255) DEFAULT NULL,
                `full_description` text DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($createTableSQL);
        }
        
        $data = [
            'hero_image' => sanitize($_POST['hero_image'] ?? ''),
            'show_in_index' => isset($_POST['show_in_index']) ? 1 : 0,
            'max_items_index' => (int)($_POST['max_items_index'] ?? 6),
            'index_title' => sanitize($_POST['index_title'] ?? ''),
            'index_description' => sanitize($_POST['index_description'] ?? ''),
            'full_title' => sanitize($_POST['full_title'] ?? ''),
            'full_description' => sanitize($_POST['full_description'] ?? '')
        ];
        
        if (updateSectionData('portfolio_section', $data)) {
            $_SESSION['message'] = 'Seiteneinstellungen wurden erfolgreich aktualisiert!';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Fehler beim Aktualisieren der Einstellungen. Bitte versuchen Sie es erneut.';
            $_SESSION['message_type'] = 'error';
        }
        $_SESSION['open_section'] = 'pageSettings';
        header("Location: projekte.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = 'Fehler beim Aktualisieren der Einstellungen: ' . htmlspecialchars($e->getMessage());
        $_SESSION['message_type'] = 'error';
        $_SESSION['open_section'] = 'pageSettings';
        header("Location: projekte.php");
        exit;
    }
}

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create' || $_POST['action'] === 'update') {
            $title = sanitize($_POST['title']);
            $description = sanitize($_POST['description']);
            $image = sanitize($_POST['image']);
            $type = sanitize($_POST['type']); // e.g. 'residential', 'commercial'
            $date = sanitize($_POST['date']);
            $active = isset($_POST['active']) ? 1 : 0;
            
            if (empty($date)) $date = date('Y-m-d');

            if ($_POST['action'] === 'create') {
                $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, type, date, active) VALUES (:title, :desc, :img, :type, :date, :active)");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'img' => $image, 'type' => $type, 'date' => $date, 'active' => $active])) {
                    $_SESSION['message'] = 'Projekt wurde hinzugefügt!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Fehler beim Hinzufügen.';
                    $_SESSION['message_type'] = 'error';
                }
            } else {
                $id = (int)$_POST['id'];
                $stmt = $pdo->prepare("UPDATE projects SET title = :title, description = :desc, image = :img, type = :type, date = :date, active = :active WHERE id = :id");
                if ($stmt->execute(['title' => $title, 'desc' => $description, 'img' => $image, 'type' => $type, 'date' => $date, 'active' => $active, 'id' => $id])) {
                    $_SESSION['message'] = 'Projekt wurde aktualisiert!';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Fehler beim Aktualisieren.';
                    $_SESSION['message_type'] = 'error';
                }
            }
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                $_SESSION['message'] = 'Projekt wurde gelöscht!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Fehler beim Löschen.';
                $_SESSION['message_type'] = 'error';
            }
        }
        
        header("Location: projekte.php");
        exit;
    }
}

$projects = $pdo->query("SELECT * FROM projects ORDER BY date DESC")->fetchAll();

// Get portfolio section data
$sectionData = getSectionData('portfolio_section');

// Check if section should be open (before unsetting)
$pageSettingsOpen = false;
if (isset($_SESSION['open_section']) && $_SESSION['open_section'] === 'pageSettings') {
    $pageSettingsOpen = true;
}
// Unset after checking
if (isset($_SESSION['open_section'])) {
    unset($_SESSION['open_section']);
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <script>
        function openModal(mode, data = null) {
            const modal = document.getElementById('projectModal');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');
            const btn = document.getElementById('modalBtn');
            
            modal.classList.remove('hidden');
            
            if (mode === 'edit' && data) {
                title.textContent = 'Projekt bearbeiten';
                btn.textContent = 'Änderungen speichern';
                form.elements['action'].value = 'update';
                form.elements['id'].value = data.id;
                form.elements['title'].value = data.title;
                form.elements['description'].value = data.description;
                form.elements['type'].value = data.type;
                form.elements['date'].value = data.date;
                form.elements['image'].value = data.image;
                form.elements['active'].checked = data.active == 1;
                
                const placeholderSvg = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'18\'%3ENo Image%3C/text%3E%3C/svg%3E';
                document.getElementById('image_preview').src = data.image ? '../' + data.image : placeholderSvg;
            } else {
                title.textContent = 'Neues Projekt hinzufügen';
                btn.textContent = 'Projekt erstellen';
                form.reset();
                form.elements['action'].value = 'create';
                form.elements['id'].value = '';
                form.elements['date'].value = new Date().toISOString().split('T')[0];
                const placeholderSvg = 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'18\'%3ENo Image%3C/text%3E%3C/svg%3E';
                document.getElementById('image_preview').src = placeholderSvg;
            }
        }

        function closeModal() {
            document.getElementById('projectModal').classList.add('hidden');
        }

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById('icon-' + sectionId);
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                section.classList.add('hidden');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }

        // Auto-open section if there's a message and section should be open
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($pageSettingsOpen): ?>
            // Open the section if it should be open
            const section = document.getElementById('pageSettings');
            const icon = document.getElementById('icon-pageSettings');
            if (section && section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            }
            <?php endif; ?>
        });

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
                        <span class="bg-indigo-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-briefcase text-indigo-600"></i>
                        </span>
                        Portfolio
                    </h1>
                </div>
                <button onclick="openModal('create')" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all text-sm">
                    <i class="fas fa-plus mr-2"></i> Projekt hinzufügen
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
                    <div class="bg-indigo-50/50 p-4 border-b border-indigo-100 flex justify-between items-center cursor-pointer" onclick="toggleSection('pageSettings')">
                        <h2 class="text-lg font-bold text-indigo-900 flex items-center">
                            <i class="fas fa-cog mr-2"></i> Seiteneinstellungen (Titel & Texte)
                        </h2>
                        <i id="icon-pageSettings" class="fas <?php echo $pageSettingsOpen ? 'fa-chevron-up' : 'fa-chevron-down'; ?> text-indigo-400"></i>
                    </div>
                    
                    <div id="pageSettings" class="<?php echo $pageSettingsOpen ? '' : 'hidden'; ?> border-t border-gray-100">
                        <form method="POST" action="projekte.php" class="p-6">
                            <input type="hidden" name="action" value="update_section">
                            
                            <div class="space-y-8">
                                <!-- Index Section -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2" style="font-family: inherit;">Bereich auf der Startseite (Index)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div class="flex items-center mb-4 col-span-2">
                                            <input type="checkbox" name="show_in_index" id="show_in_index" <?php echo ($sectionData['show_in_index'] ?? 1) ? 'checked' : ''; ?> class="h-5 w-5 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                                            <label for="show_in_index" class="ml-2 text-gray-700 font-medium" style="font-family: inherit;">Auf der Startseite anzeigen</label>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Titel auf der Startseite (blog-title)</label>
                                            <input type="text" name="index_title" value="<?php echo htmlspecialchars($sectionData['index_title'] ?? 'Unser Baujournal'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" style="font-family: inherit;">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Anzahl der Projekte auf der Startseite</label>
                                            <input type="number" name="max_items_index" value="<?php echo htmlspecialchars($sectionData['max_items_index'] ?? 6); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" style="font-family: inherit;">
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Beschreibung auf der Startseite (blog-description)</label>
                                            <textarea name="index_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" style="font-family: inherit;"><?php echo htmlspecialchars($sectionData['index_description'] ?? 'Aktuelle Projekte und Inspirationen.'); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Portfolio Page Hero -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4 border-b pb-2" style="font-family: inherit;">Vollständige Seite (Portfolio.html)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Haupttitel (portfolio-hero-title)</label>
                                            <input type="text" name="full_title" value="<?php echo htmlspecialchars($sectionData['full_title'] ?? 'Unsere Projekte'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" style="font-family: inherit;">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Hero-Bild</label>
                                            <div class="flex gap-2">
                                                <input type="text" id="hero_image" name="hero_image" value="<?php echo htmlspecialchars($sectionData['hero_image'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" readonly style="font-family: inherit;">
                                                <button type="button" onclick="openMediaPicker('hero_image')" class="bg-gray-100 px-3 py-2 rounded border border-gray-300 hover:bg-gray-200 transition-colors"><i class="fas fa-image"></i></button>
                                            </div>
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1" style="font-family: inherit;">Beschreibung (portfolio-hero-description)</label>
                                            <textarea name="full_description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" style="font-family: inherit;"><?php echo htmlspecialchars($sectionData['full_description'] ?? 'Eine Auswahl unserer erfolgreich abgeschlossenen Projekte'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end border-t border-gray-200 pt-6">
                                <button type="submit" id="saveSettingsBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 flex items-center">
                                    <i class="fas fa-save mr-2"></i> Einstellungen speichern
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Projects Grid -->
                <?php if (empty($projects)): ?>
                    <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-folder-open text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">Keine Projekte</h3>
                        <p class="text-gray-500 mt-1">Fügen Sie Ihre realisierten Projekte hinzu.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($projects as $proj): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group relative flex flex-col">
                                <div class="relative h-48 bg-gray-100">
                                    <?php 
                                    $imgPath = !empty($proj['image']) ? '../' . htmlspecialchars($proj['image']) : 'data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'18\'%3ENo Image%3C/text%3E%3C/svg%3E';
                                    ?>
                                    <img src="<?php echo $imgPath; ?>" 
                                         class="w-full h-full object-cover" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23e5e7eb\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'18\'%3ENo Image%3C/text%3E%3C/svg%3E'">
                                    
                                    <div class="absolute top-2 right-2">
                                        <span class="bg-white/90 text-gray-800 text-xs font-bold px-2 py-1 rounded shadow-sm uppercase tracking-wide">
                                            <?php echo htmlspecialchars($proj['type']); ?>
                                        </span>
                                    </div>

                                    <?php if (!$proj['active']): ?>
                                        <div class="absolute inset-0 bg-white/80 backdrop-blur-sm flex items-center justify-center">
                                            <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-sm font-bold border border-gray-300">Inaktiv</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-4 flex-1 flex flex-col">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate"><?php echo htmlspecialchars($proj['title']); ?></h3>
                                    <div class="text-xs text-gray-500 mb-3 flex items-center">
                                        <i class="far fa-calendar-alt mr-1"></i> 
                                        <?php echo date('d.m.Y', strtotime($proj['date'])); ?>
                                    </div>
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-4 flex-1"><?php echo htmlspecialchars($proj['description']); ?></p>
                                    
                                    <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 mt-auto">
                                        <button onclick='openModal("edit", <?php echo json_encode($proj); ?>)' class="bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-edit mr-1"></i>
                                        </button>
                                        <form method="POST" onsubmit="return confirm('Projekt löschen?');" class="inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $proj['id']; ?>">
                                            <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Modal -->
    <div id="projectModal" class="fixed inset-0 bg-black/50 z-50 hidden backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Projekt hinzufügen</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 rounded-lg p-1 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="projectForm" method="POST" class="p-6 space-y-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" value="">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Projekttitel</label>
                        <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                    </div>
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                        <textarea name="description" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategorie</label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary bg-white">
                            <option value="residential">Wohngebäude</option>
                            <option value="commercial">Gewerbe</option>
                            <option value="renovation">Renovierung</option>
                            <option value="other">Sonstiges</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fertigstellungsdatum</label>
                        <input type="date" name="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Projektbild</label>
                        <div class="relative group cursor-pointer border-2 border-dashed border-gray-300 rounded-lg hover:border-primary transition-colors bg-gray-50 p-1" onclick="openMediaPicker('modal_image')">
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='400' height='300'%3E%3Crect fill='%23e5e7eb' width='400' height='300'/%3E%3Ctext x='50%25' y='50%25' text-anchor='middle' dy='.3em' fill='%239ca3af' font-family='sans-serif' font-size='18'%3ENo Image%3C/text%3E%3C/svg%3E" id="image_preview" class="w-full h-48 object-cover rounded">
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/10">
                                <span class="bg-white text-gray-800 text-xs font-bold px-2 py-1 rounded shadow">Auswählen</span>
                            </div>
                        </div>
                        <input type="hidden" id="modal_image" name="image">
                    </div>
                    
                    <div class="col-span-2 pt-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="active" class="form-checkbox h-5 w-5 text-primary rounded border-gray-300 focus:ring-primary">
                            <span class="ml-2 text-gray-700 font-medium">Projekt aktiv</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal()" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                        Abbrechen
                    </button>
                    <button type="submit" id="modalBtn" class="bg-primary hover:bg-primary-dark text-white font-bold py-2.5 px-6 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all">
                        Projekt erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="js/media-picker.js"></script>
</body>
</html>