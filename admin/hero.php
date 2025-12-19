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

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Përgatisim të dhënat për tabelën hero_section
    $data = [
        'title' => sanitize($_POST['title']),
        'subtitle' => sanitize($_POST['subtitle']),
        'image' => sanitize($_POST['image']),
        'mini_text' => sanitize($_POST['mini_text'] ?? ''),
        'button1_text' => sanitize($_POST['button1_text']),
        'button1_link' => sanitize($_POST['button1_link']),
        'button2_text' => sanitize($_POST['button2_text']),
        'button2_link' => sanitize($_POST['button2_link']),
        'stat1_number' => sanitize($_POST['stat1_number']),
        'stat1_text' => sanitize($_POST['stat1_text']),
        'stat2_number' => sanitize($_POST['stat2_number']),
        'stat2_text' => sanitize($_POST['stat2_text']),
        'stat3_number' => sanitize($_POST['stat3_number']),
        'stat3_text' => sanitize($_POST['stat3_text'])
    ];

    if (updateSectionData('hero_section', $data)) {
        $_SESSION['message'] = 'Hero-Bereich wurde erfolgreich aktualisiert!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Ein Fehler ist beim Aktualisieren aufgetreten.';
        $_SESSION['message_type'] = 'error';
    }
    
    header("Location: hero.php");
    exit;
}

// Get Current Data
$hero = getSectionData('hero_section');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero-Bereich verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <style>
        .preview-image {
            max-height: 200px;
            object-fit: cover;
            border-radius: 8px;
            display: none;
        }
        .preview-image[src]:not([src=""]) {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <h1 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-home mr-2 text-primary"></i>Hero Section
                </h1>
            </header>
            
            <!-- Scrollable Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200'; ?> flex items-center shadow-sm">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> mr-3 text-xl"></i>
                        <span class="font-medium"><?php echo $message; ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6 max-w-5xl mx-auto">
                    
                    <!-- Main Content Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
                            <i class="fas fa-edit mr-2 text-yellow-500"></i>Hauptinhalt
                        </h2>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Mini Text -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mini Text (Badge)</label>
                                <input type="text" name="mini_text" value="<?php echo htmlspecialchars($hero['mini_text'] ?? 'PREMIUM QUALITÄT SEIT 2010'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="z.B. PREMIUM QUALITÄT SEIT 2010">
                                <p class="text-xs text-gray-500 mt-1">Kleiner Text der über dem Haupttitel angezeigt wird</p>
                            </div>

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Haupttitel</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($hero['title'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="z.B. Wir bauen die Zukunft">
                            </div>

                            <!-- Subtitle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Untertitel / Beschreibung</label>
                                <textarea name="subtitle" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                          placeholder="Kurze Beschreibung..."><?php echo htmlspecialchars($hero['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Image Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
                            <i class="fas fa-image mr-2 text-blue-500"></i>Hintergrundbild
                        </h2>
                        
                        <div class="flex flex-col md:flex-row gap-6 items-start">
                            <div class="flex-1 w-full">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bild-URL</label>
                                <div class="flex gap-2">
                                    <input type="text" id="hero_image" name="image" value="<?php echo htmlspecialchars($hero['image'] ?? ''); ?>" 
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm"
                                           placeholder="uploads/...">
                                    <button type="button" onclick="openMediaPicker('hero_image')" 
                                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 transition-colors flex items-center gap-2">
                                        <i class="fas fa-folder-open"></i> Auswählen
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Wählen Sie ein Bild aus der Media Library oder geben Sie die URL manuell ein.</p>
                            </div>
                            
                            <div class="w-full md:w-1/3 bg-gray-50 rounded-lg border border-dashed border-gray-300 p-2 flex items-center justify-center min-h-[150px]">
                                <?php if (!empty($hero['image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($hero['image']); ?>" id="hero_image_preview" class="preview-image w-full h-auto rounded shadow-sm">
                                <?php else: ?>
                                    <img src="" id="hero_image_preview" class="preview-image hidden">
                                    <div class="text-center text-gray-400 placeholder-text">
                                        <i class="fas fa-image text-3xl mb-2"></i>
                                        <p class="text-sm">Preview</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
                            <i class="fas fa-link mr-2 text-green-500"></i>Buttons
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Button 1 -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-medium text-gray-700 mb-3 border-b border-gray-200 pb-2">Hauptbutton (Links)</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Text</label>
                                        <input type="text" name="button1_text" value="<?php echo htmlspecialchars($hero['button1_text'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Link</label>
                                        <input type="text" name="button1_link" value="<?php echo htmlspecialchars($hero['button1_link'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    </div>
                                </div>
                            </div>

                            <!-- Button 2 -->
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-medium text-gray-700 mb-3 border-b border-gray-200 pb-2">Sekundärbutton (Rechts)</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Text</label>
                                        <input type="text" name="button2_text" value="<?php echo htmlspecialchars($hero['button2_text'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Link</label>
                                        <input type="text" name="button2_link" value="<?php echo htmlspecialchars($hero['button2_link'] ?? ''); ?>" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stats Section -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-purple-500"></i>Statistiken (Optional)
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Stat 1 -->
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Nummer 1</label>
                                    <input type="text" name="stat1_number" value="<?php echo htmlspecialchars($hero['stat1_number'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm" placeholder="500+">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Text 1</label>
                                    <input type="text" name="stat1_text" value="<?php echo htmlspecialchars($hero['stat1_text'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm" placeholder="Projekte">
                                </div>
                            </div>

                            <!-- Stat 2 -->
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Nummer 2</label>
                                    <input type="text" name="stat2_number" value="<?php echo htmlspecialchars($hero['stat2_number'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Text 2</label>
                                    <input type="text" name="stat2_text" value="<?php echo htmlspecialchars($hero['stat2_text'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm">
                                </div>
                            </div>

                            <!-- Stat 3 -->
                            <div class="p-3 border border-gray-200 rounded-lg">
                                <div class="mb-2">
                                    <label class="block text-xs text-gray-500 mb-1">Nummer 3</label>
                                    <input type="text" name="stat3_number" value="<?php echo htmlspecialchars($hero['stat3_number'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500 mb-1">Text 3</label>
                                    <input type="text" name="stat3_text" value="<?php echo htmlspecialchars($hero['stat3_text'] ?? ''); ?>" 
                                           class="w-full px-3 py-1.5 border border-gray-300 rounded text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end pt-4 pb-8">
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-semibold py-3 px-8 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i> Änderungen speichern
                        </button>
                    </div>

                </form>
            </main>
        </div>
    </div>

    <!-- Media Picker Scripts -->
    <script src="js/media-picker.js"></script>
</body>
</html>