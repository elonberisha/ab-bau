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
    $data = [
        // Index Only Fields
        'title' => sanitize($_POST['title']),
        'description1' => sanitize($_POST['description1']),
        'description2' => sanitize($_POST['description2']),
        'shop_title' => sanitize($_POST['shop_title']),
        'shop_text' => sanitize($_POST['shop_text']),
        'processing_title' => sanitize($_POST['processing_title']),
        'processing_text' => sanitize($_POST['processing_text']),
        'image1' => sanitize($_POST['image1']),
        'image2' => sanitize($_POST['image2']),
        'image3' => sanitize($_POST['image3']),
        'show_in_index' => isset($_POST['show_in_index']) ? 1 : 0,

        // Full Page Fields
        'page_hero_image' => sanitize($_POST['page_hero_image']),
        'page_hero_title' => sanitize($_POST['page_hero_title']),
        'page_hero_subtitle' => sanitize($_POST['page_hero_subtitle']),
        'full_title' => sanitize($_POST['full_title']),
        'full_desc1' => sanitize($_POST['full_desc1']),
        'full_desc2' => sanitize($_POST['full_desc2']),
        'full_desc3' => sanitize($_POST['full_desc3']),
        'story_title' => sanitize($_POST['story_title']),
        'story_p1' => sanitize($_POST['story_p1']),
        'story_p2' => sanitize($_POST['story_p2']),
        'story_p3' => sanitize($_POST['story_p3']),
        
        // Cards
        'card1_title' => sanitize($_POST['card1_title']),
        'card1_text' => sanitize($_POST['card1_text']),
        'card2_title' => sanitize($_POST['card2_title']),
        'card2_text' => sanitize($_POST['card2_text']),
        'card3_title' => sanitize($_POST['card3_title']),
        'card3_text' => sanitize($_POST['card3_text']),

        // Stats
        'stat1_num' => sanitize($_POST['stat1_num']),
        'stat1_text' => sanitize($_POST['stat1_text']),
        'stat2_num' => sanitize($_POST['stat2_num']),
        'stat2_text' => sanitize($_POST['stat2_text']),
        'stat3_num' => sanitize($_POST['stat3_num']),
        'stat3_text' => sanitize($_POST['stat3_text'])
    ];

    if (updateSectionData('about_section', $data)) {
        $_SESSION['message'] = 'Daten wurden erfolgreich aktualisiert!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Fehler beim Aktualisieren.';
        $_SESSION['message_type'] = 'error';
    }
    
    header("Location: about.php");
    exit;
}

// Get Data
$about = getSectionData('about_section');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Über uns verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <style>
        .preview-img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 6px;
            background-color: #f3f4f6;
            border: 1px dashed #d1d5db;
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
                    <i class="fas fa-info-circle mr-2 text-primary"></i>Über uns
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

                <form method="POST" class="space-y-8 max-w-5xl mx-auto">
                    
                    <!-- 1. INDEX PAGE SECTION -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-blue-50/50 p-4 border-b border-blue-100 flex items-center justify-between">
                            <h2 class="text-lg font-bold text-blue-900 flex items-center">
                                <span class="bg-blue-100 text-blue-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                Bereich auf der Startseite (Index)
                            </h2>
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="show_in_index" <?php echo ($about['show_in_index'] ?? 1) ? 'checked' : ''; ?> class="form-checkbox h-5 w-5 text-blue-600 rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-700 font-medium">Auf der Startseite anzeigen</span>
                            </label>
                        </div>
                        
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Haupttitel</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($about['title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Beschreibung Links</label>
                                <textarea name="description1" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"><?php echo htmlspecialchars($about['description1'] ?? ''); ?></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Beschreibung Rechts</label>
                                <textarea name="description2" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500"><?php echo htmlspecialchars($about['description2'] ?? ''); ?></textarea>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-3 flex items-center"><i class="fas fa-store text-primary mr-2"></i> Shop Info</h3>
                                <input type="text" name="shop_title" placeholder="Titel (z.B. Shop:)" value="<?php echo htmlspecialchars($about['shop_title'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded mb-2 text-sm font-medium">
                                <textarea name="shop_text" rows="3" placeholder="Beschreibung..." class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><?php echo htmlspecialchars($about['shop_text'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <h3 class="font-bold text-gray-800 mb-3 flex items-center"><i class="fas fa-tools text-primary mr-2"></i> Processing Info</h3>
                                <input type="text" name="processing_title" placeholder="Titel (z.B. Processing:)" value="<?php echo htmlspecialchars($about['processing_title'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded mb-2 text-sm font-medium">
                                <textarea name="processing_text" rows="3" placeholder="Beschreibung..." class="w-full px-3 py-2 border border-gray-300 rounded text-sm"><?php echo htmlspecialchars($about['processing_text'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- 2. FULL PAGE SECTION -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-purple-50/50 p-4 border-b border-purple-100">
                            <h2 class="text-lg font-bold text-purple-900 flex items-center">
                                <span class="bg-purple-100 text-purple-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">2</span>
                                Vollständige Seite (About.html)
                            </h2>
                        </div>
                        
                        <div class="p-6 space-y-6">
                            <!-- Hero Settings -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hero-Titel</label>
                                    <input type="text" name="page_hero_title" value="<?php echo htmlspecialchars($about['page_hero_title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <div class="mt-4">
                                        <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hero-Untertitel</label>
                                        <textarea name="page_hero_subtitle" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($about['page_hero_subtitle'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Hintergrundbild</label>
                                    <div class="relative group cursor-pointer" onclick="openMediaPicker('page_hero_image')">
                                        <img src="../<?php echo !empty($about['page_hero_image']) ? htmlspecialchars($about['page_hero_image']) : 'assets/img/placeholder.png'; ?>" 
                                             id="page_hero_image_preview" 
                                             class="w-full h-32 object-cover rounded-lg border-2 border-dashed border-gray-300 hover:border-purple-500 transition-colors">
                                        <div class="absolute inset-0 flex items-center justify-center bg-black/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg">
                                            <span class="bg-white/90 text-gray-800 px-3 py-1 rounded-full text-xs font-bold shadow-sm">Bild ändern</span>
                                        </div>
                                    </div>
                                    <input type="hidden" id="page_hero_image" name="page_hero_image" value="<?php echo htmlspecialchars($about['page_hero_image'] ?? ''); ?>">
                                </div>
                            </div>

                            <!-- Main Content -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Hauptinhalt</label>
                                <input type="text" name="full_title" placeholder="Titel des Abschnitts" value="<?php echo htmlspecialchars($about['full_title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-3">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <textarea name="full_desc1" rows="4" placeholder="Absatz 1" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?php echo htmlspecialchars($about['full_desc1'] ?? ''); ?></textarea>
                                    <textarea name="full_desc2" rows="4" placeholder="Absatz 2" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?php echo htmlspecialchars($about['full_desc2'] ?? ''); ?></textarea>
                                    <textarea name="full_desc3" rows="4" placeholder="Absatz 3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"><?php echo htmlspecialchars($about['full_desc3'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <!-- Story -->
                            <div class="bg-gray-50 p-5 rounded-xl border border-gray-200">
                                <div class="flex items-center mb-4">
                                    <i class="fas fa-history text-purple-500 mr-2 text-lg"></i>
                                    <h3 class="font-bold text-gray-800">Unsere Geschichte</h3>
                                </div>
                                <input type="text" name="story_title" placeholder="Titel der Geschichte" value="<?php echo htmlspecialchars($about['story_title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-4 bg-white">
                                <div class="space-y-3">
                                    <textarea name="story_p1" rows="2" placeholder="Teil 1..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><?php echo htmlspecialchars($about['story_p1'] ?? ''); ?></textarea>
                                    <textarea name="story_p2" rows="2" placeholder="Teil 2..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><?php echo htmlspecialchars($about['story_p2'] ?? ''); ?></textarea>
                                    <textarea name="story_p3" rows="2" placeholder="Teil 3..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white"><?php echo htmlspecialchars($about['story_p3'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. SHARED RESOURCES (IMAGES, CARDS, STATS) -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-green-50/50 p-4 border-b border-green-100">
                            <h2 class="text-lg font-bold text-green-900 flex items-center">
                                <span class="bg-green-100 text-green-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">3</span>
                                Gemeinsame Ressourcen (Shared)
                            </h2>
                        </div>

                        <div class="p-6 space-y-8">
                            
                            <!-- 3.1 IMAGES COLLAGE -->
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center">
                                    <i class="fas fa-images mr-2 text-gray-400"></i> Fotocollage
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <?php for($i=1; $i<=3; $i++): ?>
                                    <div class="relative group">
                                        <div class="bg-gray-100 rounded-lg p-2 border border-gray-200 text-center cursor-pointer hover:border-green-500 transition-colors" onclick="openMediaPicker('image<?php echo $i; ?>')">
                                            <div class="h-32 mb-2 bg-white rounded flex items-center justify-center overflow-hidden">
                                                <img src="../<?php echo !empty($about["image$i"]) ? htmlspecialchars($about["image$i"]) : 'assets/img/placeholder.png'; ?>" 
                                                     id="image<?php echo $i; ?>_preview" 
                                                     class="w-full h-full object-cover">
                                            </div>
                                            <span class="text-xs font-bold text-gray-500 group-hover:text-green-600">Bild <?php echo $i; ?> <i class="fas fa-edit ml-1"></i></span>
                                        </div>
                                        <input type="hidden" id="image<?php echo $i; ?>" name="image<?php echo $i; ?>" value="<?php echo htmlspecialchars($about["image$i"] ?? ''); ?>">
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- 3.2 CARDS -->
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center">
                                    <i class="fas fa-th-large mr-2 text-gray-400"></i> Wertkarten
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Card 1 -->
                                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-award text-blue-500 mr-2"></i>
                                            <input type="text" name="card1_title" value="<?php echo htmlspecialchars($about['card1_title'] ?? ''); ?>" class="bg-transparent font-bold text-gray-800 text-sm w-full focus:outline-none border-b border-transparent focus:border-blue-300" placeholder="Titel 1">
                                        </div>
                                        <textarea name="card1_text" rows="3" class="w-full bg-white border border-gray-200 rounded p-2 text-xs focus:ring-1 focus:ring-blue-500" placeholder="Text..."><?php echo htmlspecialchars($about['card1_text'] ?? ''); ?></textarea>
                                    </div>
                                    <!-- Card 2 -->
                                    <div class="bg-green-50/50 p-4 rounded-xl border border-green-100">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-handshake text-green-500 mr-2"></i>
                                            <input type="text" name="card2_title" value="<?php echo htmlspecialchars($about['card2_title'] ?? ''); ?>" class="bg-transparent font-bold text-gray-800 text-sm w-full focus:outline-none border-b border-transparent focus:border-green-300" placeholder="Titel 2">
                                        </div>
                                        <textarea name="card2_text" rows="3" class="w-full bg-white border border-gray-200 rounded p-2 text-xs focus:ring-1 focus:ring-green-500" placeholder="Text..."><?php echo htmlspecialchars($about['card2_text'] ?? ''); ?></textarea>
                                    </div>
                                    <!-- Card 3 -->
                                    <div class="bg-red-50/50 p-4 rounded-xl border border-red-100">
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-heart text-red-500 mr-2"></i>
                                            <input type="text" name="card3_title" value="<?php echo htmlspecialchars($about['card3_title'] ?? ''); ?>" class="bg-transparent font-bold text-gray-800 text-sm w-full focus:outline-none border-b border-transparent focus:border-red-300" placeholder="Titel 3">
                                        </div>
                                        <textarea name="card3_text" rows="3" class="w-full bg-white border border-gray-200 rounded p-2 text-xs focus:ring-1 focus:ring-red-500" placeholder="Text..."><?php echo htmlspecialchars($about['card3_text'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- 3.3 STATS -->
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3 flex items-center">
                                    <i class="fas fa-chart-bar mr-2 text-gray-400"></i> Statistiken
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <?php for($i=1; $i<=3; $i++): ?>
                                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-lg border border-gray-200">
                                        <div class="bg-white p-2 rounded shadow-sm border border-gray-100 w-16 text-center">
                                            <input type="text" name="stat<?php echo $i; ?>_num" value="<?php echo htmlspecialchars($about["stat{$i}_num"] ?? ''); ?>" class="w-full text-center font-black text-gray-800 focus:outline-none text-sm" placeholder="Nr">
                                        </div>
                                        <div class="flex-1">
                                            <input type="text" name="stat<?php echo $i; ?>_text" value="<?php echo htmlspecialchars($about["stat{$i}_text"] ?? ''); ?>" class="w-full bg-transparent border-b border-gray-300 focus:border-primary focus:outline-none text-sm pb-1" placeholder="Statistikbeschreibung">
                                        </div>
                                    </div>
                                    <?php endfor; ?>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Sticky Save Bar -->
                    <div class="sticky bottom-4 bg-white/90 backdrop-blur shadow-2xl border border-gray-200 rounded-xl p-4 flex justify-between items-center z-40">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i> Vergessen Sie nicht, die Änderungen zu speichern!
                        </div>
                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-8 rounded-lg shadow-lg transform hover:-translate-y-0.5 transition-all flex items-center">
                            <i class="fas fa-save mr-2"></i> ALLES SPEICHERN
                        </button>
                    </div>

                </form>
            </main>
        </div>
    </div>
    
    <script src="js/media-picker.js"></script>
</body>
</html>