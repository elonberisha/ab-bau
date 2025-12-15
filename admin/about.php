<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'About Us';

// Load customization data
$customization = readJson('customization.json');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customization['about']['page_hero_image'] = sanitize($_POST['about_page_hero_image'] ?? '');
    $customization['about']['title'] = sanitize($_POST['about_title'] ?? '');
    $customization['about']['description1'] = sanitize($_POST['about_description1'] ?? '');
    $customization['about']['description2'] = sanitize($_POST['about_description2'] ?? '');
    $customization['about']['shop_title'] = sanitize($_POST['about_shop_title'] ?? '');
    $customization['about']['shop_text'] = sanitize($_POST['about_shop_text'] ?? '');
    $customization['about']['processing_title'] = sanitize($_POST['about_processing_title'] ?? '');
    $customization['about']['processing_text'] = sanitize($_POST['about_processing_text'] ?? '');
    $customization['about']['image1'] = sanitize($_POST['about_image1'] ?? '');
    $customization['about']['image2'] = sanitize($_POST['about_image2'] ?? '');
    $customization['about']['image3'] = sanitize($_POST['about_image3'] ?? '');
    $customization['about']['card1_title'] = sanitize($_POST['about_card1_title'] ?? '');
    $customization['about']['card1_text'] = sanitize($_POST['about_card1_text'] ?? '');
    $customization['about']['card2_title'] = sanitize($_POST['about_card2_title'] ?? '');
    $customization['about']['card2_text'] = sanitize($_POST['about_card2_text'] ?? '');
    $customization['about']['card3_title'] = sanitize($_POST['about_card3_title'] ?? '');
    $customization['about']['card3_text'] = sanitize($_POST['about_card3_text'] ?? '');
    
    // Full page content (only about.html)
    $customization['about']['page_hero_title'] = sanitize($_POST['about_page_hero_title'] ?? '');
    $customization['about']['page_hero_subtitle'] = sanitize($_POST['about_page_hero_subtitle'] ?? '');
    $customization['about']['full_content']['title'] = sanitize($_POST['about_full_title'] ?? '');
    $customization['about']['full_content']['description1'] = sanitize($_POST['about_full_description1'] ?? '');
    $customization['about']['full_content']['description2'] = sanitize($_POST['about_full_description2'] ?? '');
    $customization['about']['full_content']['description3'] = sanitize($_POST['about_full_description3'] ?? '');
    // Story section (Unsere Geschichte)
    $customization['about']['story_title'] = sanitize($_POST['about_story_title'] ?? '');
    $customization['about']['story_paragraph1'] = sanitize($_POST['about_story_paragraph1'] ?? '');
    $customization['about']['story_paragraph2'] = sanitize($_POST['about_story_paragraph2'] ?? '');
    $customization['about']['story_paragraph3'] = sanitize($_POST['about_story_paragraph3'] ?? '');
    
    // Stats
    $customization['about']['stats']['stat1_number'] = sanitize($_POST['about_stat1_number'] ?? '500+');
    $customization['about']['stats']['stat1_text'] = sanitize($_POST['about_stat1_text'] ?? 'Premium Produkte');
    $customization['about']['stats']['stat2_number'] = sanitize($_POST['about_stat2_number'] ?? '200+');
    $customization['about']['stats']['stat2_text'] = sanitize($_POST['about_stat2_text'] ?? 'Projekte realisiert');
    $customization['about']['stats']['stat3_number'] = sanitize($_POST['about_stat3_number'] ?? '15+');
    $customization['about']['stats']['stat3_text'] = sanitize($_POST['about_stat3_text'] ?? 'Jahre perfekte Handwerkskunst');
    
    $customization['about']['show_in_index'] = isset($_POST['about_show_in_index']);
    $customization['about']['index_text_length'] = sanitize($_POST['about_index_text_length'] ?? 'short');
    
    if (writeJson('customization.json', $customization)) {
        $message = 'About section u ruajt me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
    } else {
        $message = 'Gabim në ruajtje!';
        $messageType = 'error';
    }
    
    // Reload customization after save
    $customization = readJson('customization.json');
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

        <!-- About Section Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-info-circle text-primary mr-2"></i>
                Menaxho About Us Section
            </h2>
            <form method="POST" class="space-y-6">
                <!-- SECTION 1: INDEX.HTML ONLY -->
                <div class="bg-blue-50 border-l-4 border-primary p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-home text-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-primary">VETËM PËR INDEX.HTML</h3>
                        <span class="ml-2 text-xs bg-primary text-white px-2 py-1 rounded">*</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Title
                            </label>
                            <input type="text" name="about_title" value="<?php echo htmlspecialchars($customization['about']['title'] ?? ''); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Description 1
                            </label>
                            <textarea name="about_description1" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['description1'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Description 2
                            </label>
                            <textarea name="about_description2" rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['description2'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Shop Title
                                </label>
                                <input type="text" name="about_shop_title" value="<?php echo htmlspecialchars($customization['about']['shop_title'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Shop Text
                                </label>
                                <textarea name="about_shop_text" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['shop_text'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Processing Title
                                </label>
                                <input type="text" name="about_processing_title" value="<?php echo htmlspecialchars($customization['about']['processing_title'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Processing Text
                                </label>
                                <textarea name="about_processing_text" rows="3" 
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['processing_text'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Images (Collage - 3 foto)
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 1 (Top)</label>
                                    <input type="text" name="about_image1" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image1'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 2 (Bottom Left)</label>
                                    <input type="text" name="about_image2" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image2'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 3 (Bottom Right)</label>
                                    <input type="text" name="about_image3" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image3'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Stats
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <input type="text" name="about_stat1_number" placeholder="Stat 1 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat1_number'] ?? '500+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat1_text" placeholder="Stat 1 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat1_text'] ?? 'Premium Produkte'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="text" name="about_stat2_number" placeholder="Stat 2 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat2_number'] ?? '200+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat2_text" placeholder="Stat 2 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat2_text'] ?? 'Projekte realisiert'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="text" name="about_stat3_number" placeholder="Stat 3 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat3_number'] ?? '15+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat3_text" placeholder="Stat 3 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat3_text'] ?? 'Jahre perfekte Handwerkskunst'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4 pt-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="about_show_in_index" <?php echo ($customization['about']['show_in_index'] ?? true) ? 'checked' : ''; ?> class="mr-2">
                                <span class="text-sm">Shfaq në Index</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: ABOUT.HTML ONLY -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-700">VETËM PËR ABOUT.HTML</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Section</label>
                            <div class="space-y-3">
                                <input type="text" name="about_page_hero_image" data-media-picker="image" placeholder="Hero Image URL"
                                       value="<?php echo htmlspecialchars($customization['about']['page_hero_image'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <input type="text" name="about_page_hero_title" placeholder="Hero Title" value="<?php echo htmlspecialchars($customization['about']['page_hero_title'] ?? 'Über uns'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="about_page_hero_subtitle" rows="2" placeholder="Hero Subtitle"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['page_hero_subtitle'] ?? 'Unsere Geschichte, unsere Werte und unsere Leidenschaft für Perfektion'); ?></textarea>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Content</label>
                            <div class="space-y-3">
                                <input type="text" name="about_full_title" placeholder="Title" value="<?php echo htmlspecialchars($customization['about']['full_content']['title'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="about_full_description1" rows="3" placeholder="Description 1"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['full_content']['description1'] ?? ''); ?></textarea>
                                <textarea name="about_full_description2" rows="3" placeholder="Description 2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['full_content']['description2'] ?? ''); ?></textarea>
                                <textarea name="about_full_description3" rows="3" placeholder="Description 3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['full_content']['description3'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Images (Collage - 3 foto)</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 1 (Top)</label>
                                    <input type="text" name="about_image1" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image1'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 2 (Bottom Left)</label>
                                    <input type="text" name="about_image2" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image2'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Image 3 (Bottom Right)</label>
                                    <input type="text" name="about_image3" data-media-picker="image"
                                           value="<?php echo htmlspecialchars($customization['about']['image3'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cards (Qualität, Vertrauen, Leidenschaft)</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <input type="text" name="about_card1_title" placeholder="Card 1 Title" value="<?php echo htmlspecialchars($customization['about']['card1_title'] ?? 'Qualität'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <textarea name="about_card1_text" rows="3" placeholder="Card 1 Text"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['card1_text'] ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <input type="text" name="about_card2_title" placeholder="Card 2 Title" value="<?php echo htmlspecialchars($customization['about']['card2_title'] ?? 'Vertrauen'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <textarea name="about_card2_text" rows="3" placeholder="Card 2 Text"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['card2_text'] ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <input type="text" name="about_card3_title" placeholder="Card 3 Title" value="<?php echo htmlspecialchars($customization['about']['card3_title'] ?? 'Leidenschaft'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <textarea name="about_card3_text" rows="3" placeholder="Card 3 Text"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['card3_text'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stats</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <input type="text" name="about_stat1_number" placeholder="Stat 1 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat1_number'] ?? '500+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat1_text" placeholder="Stat 1 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat1_text'] ?? 'Premium Produkte'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="text" name="about_stat2_number" placeholder="Stat 2 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat2_number'] ?? '200+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat2_text" placeholder="Stat 2 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat2_text'] ?? 'Projekte realisiert'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <input type="text" name="about_stat3_number" placeholder="Stat 3 Number" value="<?php echo htmlspecialchars($customization['about']['stats']['stat3_number'] ?? '15+'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg mb-2">
                                    <input type="text" name="about_stat3_text" placeholder="Stat 3 Text" value="<?php echo htmlspecialchars($customization['about']['stats']['stat3_text'] ?? 'Jahre perfekte Handwerkskunst'); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Unsere Geschichte Section</label>
                            <div class="space-y-3">
                                <input type="text" name="about_story_title" placeholder="Story Title" value="<?php echo htmlspecialchars($customization['about']['story_title'] ?? 'Unsere Geschichte'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="about_story_paragraph1" rows="3" placeholder="Story Paragraph 1"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['story_paragraph1'] ?? ''); ?></textarea>
                                <textarea name="about_story_paragraph2" rows="3" placeholder="Story Paragraph 2"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['story_paragraph2'] ?? ''); ?></textarea>
                                <textarea name="about_story_paragraph3" rows="3" placeholder="Story Paragraph 3"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['about']['story_paragraph3'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all mt-6">
                    <i class="fas fa-save mr-2"></i>Ruaj About Section
                </button>
            </form>
        </div>
    </div>

</body>
</html>

