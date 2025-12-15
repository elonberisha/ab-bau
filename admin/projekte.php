<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'Projekte';

// Load data
$customization = readJson('customization.json');
$projects = readJson('projects.json');
if (!isset($projects['projects']) || !is_array($projects['projects'])) {
    $projects['projects'] = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_settings') {
        $customization['portfolio']['hero_image'] = sanitize($_POST['portfolio_hero_image'] ?? '');
        $customization['portfolio']['show_in_index'] = isset($_POST['portfolio_show_in_index']);
        $customization['portfolio']['max_items_index'] = intval($_POST['portfolio_max_items_index'] ?? 6);
        $customization['portfolio']['index_title'] = sanitize($_POST['portfolio_index_title'] ?? '');
        $customization['portfolio']['index_description'] = sanitize($_POST['portfolio_index_description'] ?? '');
        $customization['portfolio']['full_title'] = sanitize($_POST['portfolio_full_title'] ?? '');
        $customization['portfolio']['full_description'] = sanitize($_POST['portfolio_full_description'] ?? '');
        
        if (writeJson('customization.json', $customization)) {
            $message = 'Projekte Einstellungen erfolgreich gespeichert!';
            $messageType = 'success';
        } else {
            $message = 'Fehler beim Speichern!';
            $messageType = 'error';
        }
        $customization = readJson('customization.json');
    } elseif ($action === 'add_project') {
        $newProject = [
            'id' => uniqid(),
            'title' => sanitize($_POST['title'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'path' => sanitize($_POST['image'] ?? ''),
            'date' => sanitize($_POST['date'] ?? ''),
            'type' => 'portfolio',
            'active' => isset($_POST['active'])
        ];
        $projects['projects'][] = $newProject;
        if (writeJson('projects.json', $projects)) {
            $message = 'Projekt erfolgreich hinzugefügt!';
            $messageType = 'success';
            $projects = readJson('projects.json');
        } else {
            $message = 'Fehler beim Hinzufügen des Projekts!';
            $messageType = 'error';
        }
    } elseif ($action === 'edit_project') {
        $id = $_POST['id'] ?? '';
        foreach ($projects['projects'] as $k => $proj) {
            if ($proj['id'] === $id) {
                $projects['projects'][$k]['title'] = sanitize($_POST['title'] ?? '');
                $projects['projects'][$k]['description'] = sanitize($_POST['description'] ?? '');
                $projects['projects'][$k]['path'] = sanitize($_POST['image'] ?? '');
                $projects['projects'][$k]['date'] = sanitize($_POST['date'] ?? '');
                $projects['projects'][$k]['active'] = isset($_POST['active']);
                break;
            }
        }
        if (writeJson('projects.json', $projects)) {
            $message = 'Projekt erfolgreich aktualisiert!';
            $messageType = 'success';
            $projects = readJson('projects.json');
        } else {
            $message = 'Fehler beim Aktualisieren!';
            $messageType = 'error';
        }
    } elseif ($action === 'delete_project') {
        $id = $_POST['id'] ?? '';
        $projects['projects'] = array_values(array_filter($projects['projects'], function($p) use ($id) {
            return $p['id'] !== $id;
        }));
        if (writeJson('projects.json', $projects)) {
            $message = 'Projekt erfolgreich gelöscht!';
            $messageType = 'success';
            $projects = readJson('projects.json');
        } else {
            $message = 'Fehler beim Löschen!';
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
                    <i class="fas fa-external-link-alt mr-1"></i>Seite ansehen
                </a>
            </div>
        <?php endif; ?>

        <!-- Projekte Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-briefcase text-primary mr-2"></i>
                Projekte Einstellungen
            </h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="save_settings">
                
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
                                <input type="checkbox" name="portfolio_show_in_index" <?php echo ($customization['portfolio']['show_in_index'] ?? false) ? 'checked' : ''; ?> class="mr-2">
                                <span class="text-sm">Projekte im Index anzeigen</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Max Items im Index
                            </label>
                            <input type="number" name="portfolio_max_items_index" value="<?php echo $customization['portfolio']['max_items_index'] ?? 6; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Titel im Index
                            </label>
                            <input type="text" name="portfolio_index_title" value="<?php echo htmlspecialchars($customization['portfolio']['index_title'] ?? 'Unser Baujournal'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Beschreibung im Index
                            </label>
                            <textarea name="portfolio_index_description" rows="2" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['portfolio']['index_description'] ?? 'Eine Auswahl unserer erfolgreich abgeschlossenen Projekte'); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: PORTFOLIO.HTML ONLY -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-700">NUR FÜR PORTFOLIO.HTML</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Section</label>
                            <div class="space-y-3">
                                <input type="text" name="portfolio_hero_image" data-media-picker="image" placeholder="Hero Image URL"
                                       value="<?php echo htmlspecialchars($customization['portfolio']['hero_image'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <input type="text" name="portfolio_full_title" placeholder="Hero Title"
                                       value="<?php echo htmlspecialchars($customization['portfolio']['full_title'] ?? 'Unsere Projekte'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="portfolio_full_description" rows="2" placeholder="Hero Description"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['portfolio']['full_description'] ?? 'Sehen Sie sich unsere vollständige Projektsammlung an'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i>Einstellungen speichern
                </button>
            </form>
        </div>

        <!-- Neues Projekt hinzufügen -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Neues Projekt hinzufügen
            </h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="hidden" name="action" value="add_project">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><span class="text-primary font-bold mr-1">*</span>Titel</label>
                    <input type="text" name="title" required placeholder="z.B. Badezimmer Renovierung" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datum</label>
                    <input type="text" name="date" placeholder="z.B. 2025" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2"><span class="text-primary font-bold mr-1">*</span>Bild URL</label>
                    <input type="text" name="image" data-media-picker="image" placeholder="uploads/projekt.png" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                    <textarea name="description" rows="3" placeholder="Projekt Beschreibung" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="active" checked>
                        <span>Aktiv</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Projekt speichern
                    </button>
                </div>
            </form>
        </div>

        <!-- Projekt Liste -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-list text-primary mr-2"></i>
                Projekt Liste (<?php echo count($projects['projects']); ?>)
            </h2>

            <?php if (empty($projects['projects'])): ?>
                <p class="text-gray-500 text-center py-8">Derzeit sind keine Projekte verfügbar.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($projects['projects'] as $index => $proj): ?>
                        <div class="border rounded-lg overflow-hidden">
                            <!-- Header - Always Visible -->
                            <div class="bg-white p-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <div class="flex items-center space-x-3 flex-1">
                                    <button onclick="toggleProject(<?php echo $index; ?>)" 
                                            class="text-gray-600 hover:text-primary transition-colors">
                                        <i id="project-icon-<?php echo $index; ?>" class="fas fa-chevron-down transition-transform"></i>
                                    </button>
                                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($proj['title']); ?></h3>
                                    <span class="px-2 py-1 rounded text-xs <?php echo !isset($proj['active']) || $proj['active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo !isset($proj['active']) || $proj['active'] ? 'Aktiv' : 'Inaktiv'; ?>
                                    </span>
                                    <?php if (!empty($proj['date'])): ?>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i><?php echo htmlspecialchars($proj['date']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="editProject(<?php echo htmlspecialchars(json_encode($proj)); ?>)" 
                                            class="bg-blue-500 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher, dass Sie löschen möchten?');">
                                        <input type="hidden" name="action" value="delete_project">
                                        <input type="hidden" name="id" value="<?php echo $proj['id']; ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded text-sm hover:bg-red-600 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Collapsible Content -->
                            <div id="project-content-<?php echo $index; ?>" class="hidden border-t bg-gray-50">
                                <div class="p-4 space-y-4">
                                    <!-- Project Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <?php if (!empty($proj['date'])): ?>
                                            <div>
                                                <span class="font-semibold text-gray-700">Datum:</span>
                                                <span class="text-gray-600 ml-2"><?php echo htmlspecialchars($proj['date']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($proj['description'])): ?>
                                            <div class="md:col-span-2">
                                                <span class="font-semibold text-gray-700">Beschreibung:</span>
                                                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($proj['description']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($proj['path'])): ?>
                                            <div class="md:col-span-2">
                                                <span class="font-semibold text-gray-700">Bild:</span>
                                                <img src="../<?php echo htmlspecialchars($proj['path']); ?>" 
                                                     alt="<?php echo htmlspecialchars($proj['title']); ?>"
                                                     class="w-32 h-32 object-cover rounded mt-2 border">
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Edit Form -->
                                    <div class="bg-white p-4 rounded-lg border">
                                        <h4 class="font-semibold mb-3">Projekt bearbeiten</h4>
                                        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <input type="hidden" name="action" value="edit_project">
                                            <input type="hidden" name="id" value="<?php echo $proj['id']; ?>">
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Titel</label>
                                                <input type="text" name="title" value="<?php echo htmlspecialchars($proj['title']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-600 mb-1">Datum</label>
                                                <input type="text" name="date" value="<?php echo htmlspecialchars($proj['date'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded text-sm">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-gray-600 mb-1">Bild URL</label>
                                                <input type="text" name="image" value="<?php echo htmlspecialchars($proj['path'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded text-sm" data-media-picker="image">
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="block text-xs text-gray-600 mb-1">Beschreibung</label>
                                                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><?php echo htmlspecialchars($proj['description'] ?? ''); ?></textarea>
                                            </div>
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" name="active" <?php echo (!isset($proj['active']) || $proj['active']) ? 'checked' : ''; ?>>
                                                <span class="text-sm">Aktiv</span>
                                            </div>
                                            <div>
                                                <button type="submit" class="w-full bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900 font-semibold text-sm">
                                                    <i class="fas fa-save mr-1"></i>Speichern
                                                </button>
                                            </div>
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

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h2 class="text-xl font-bold mb-4">Projekt bearbeiten</h2>
            <form method="POST" id="editProjectForm">
                <input type="hidden" name="action" value="edit_project">
                <input type="hidden" name="id" id="editProjectId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                        <input type="text" name="title" id="editProjectTitle" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Datum</label>
                        <input type="text" name="date" id="editProjectDate"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bild URL</label>
                        <input type="text" name="image" id="editProjectImage" data-media-picker="image" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                        <textarea name="description" id="editProjectDescription" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="active" id="editProjectActive">
                            <span>Aktiv</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeEditProjectModal()" 
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
        function toggleProject(index) {
            const content = document.getElementById('project-content-' + index);
            const icon = document.getElementById('project-icon-' + index);
            
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
        
        function editProject(project) {
            document.getElementById('editProjectId').value = project.id;
            document.getElementById('editProjectTitle').value = project.title;
            document.getElementById('editProjectDate').value = project.date || '';
            document.getElementById('editProjectImage').value = project.path || '';
            document.getElementById('editProjectDescription').value = project.description || '';
            document.getElementById('editProjectActive').checked = project.active !== false;
            document.getElementById('editProjectModal').classList.remove('hidden');
        }

        function closeEditProjectModal() {
            document.getElementById('editProjectModal').classList.add('hidden');
        }
    </script>
</body>
</html>

