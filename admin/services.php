<?php
require_once 'functions.php';
requireLogin();

$services = readJson('services.json');
$customization = readJson('customization.json');
$message = '';
$messageType = '';
$pageTitle = 'Menaxho Shërbimet';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Handle settings update
    if ($action === 'update_settings') {
        $customization['services']['hero_image'] = sanitize($_POST['services_hero_image'] ?? '');
        $customization['services']['section_subtitle'] = sanitize($_POST['services_section_subtitle'] ?? '');
        $customization['services']['section_title_line1'] = sanitize($_POST['services_section_title_line1'] ?? '');
        $customization['services']['section_title_line2'] = sanitize($_POST['services_section_title_line2'] ?? '');
        $customization['services']['section_description'] = sanitize($_POST['services_section_description'] ?? '');
        $customization['services']['show_in_index'] = isset($_POST['services_show_in_index']);
        $customization['services']['max_cards_index'] = intval($_POST['services_max_cards_index'] ?? 6);
        $customization['services']['hero_image'] = sanitize($_POST['services_hero_image'] ?? '');
        $customization['services']['full_title'] = sanitize($_POST['services_full_title'] ?? '');
        $customization['services']['full_description'] = sanitize($_POST['services_full_description'] ?? '');
        // Page-specific fields (services.html)
        $customization['services']['page_subtitle'] = sanitize($_POST['services_page_subtitle'] ?? '');
        $customization['services']['page_title_line1'] = sanitize($_POST['services_page_title_line1'] ?? '');
        $customization['services']['page_title_line2'] = sanitize($_POST['services_page_title_line2'] ?? '');
        $customization['services']['page_description'] = sanitize($_POST['services_page_description'] ?? '');
        // Additional services section
        $customization['services']['additional_title'] = sanitize($_POST['services_additional_title'] ?? '');
        $customization['services']['additional_cards'] = [
            [
                'icon' => 'fa-tools',
                'title' => sanitize($_POST['services_additional_card1_title'] ?? ''),
                'text' => sanitize($_POST['services_additional_card1_text'] ?? '')
            ],
            [
                'icon' => 'fa-spray-can',
                'title' => sanitize($_POST['services_additional_card2_title'] ?? ''),
                'text' => sanitize($_POST['services_additional_card2_text'] ?? '')
            ],
            [
                'icon' => 'fa-broom',
                'title' => sanitize($_POST['services_additional_card3_title'] ?? ''),
                'text' => sanitize($_POST['services_additional_card3_text'] ?? '')
            ],
            [
                'icon' => 'fa-comments',
                'title' => sanitize($_POST['services_additional_card4_title'] ?? ''),
                'text' => sanitize($_POST['services_additional_card4_text'] ?? '')
            ]
        ];
        // Why Choose Us section (services.html only)
        $customization['services']['why_title'] = sanitize($_POST['services_why_title'] ?? '');
        $customization['services']['why_description'] = sanitize($_POST['services_why_description'] ?? '');
        $customization['services']['why_cards'] = [
            [
                'icon' => 'fa-check-circle',
                'title' => sanitize($_POST['services_why_card1_title'] ?? ''),
                'text' => sanitize($_POST['services_why_card1_text'] ?? '')
            ],
            [
                'icon' => 'fa-award',
                'title' => sanitize($_POST['services_why_card2_title'] ?? ''),
                'text' => sanitize($_POST['services_why_card2_text'] ?? '')
            ],
            [
                'icon' => 'fa-users',
                'title' => sanitize($_POST['services_why_card3_title'] ?? ''),
                'text' => sanitize($_POST['services_why_card3_text'] ?? '')
            ]
        ];
        // Process section (services.html only)
        $customization['services']['process_title'] = sanitize($_POST['services_process_title'] ?? '');
        $customization['services']['process_description'] = sanitize($_POST['services_process_description'] ?? '');
        $customization['services']['process_steps'] = [
            [
                'number' => '01',
                'title' => sanitize($_POST['services_process_step1_title'] ?? ''),
                'text' => sanitize($_POST['services_process_step1_text'] ?? '')
            ],
            [
                'number' => '02',
                'title' => sanitize($_POST['services_process_step2_title'] ?? ''),
                'text' => sanitize($_POST['services_process_step2_text'] ?? '')
            ],
            [
                'number' => '03',
                'title' => sanitize($_POST['services_process_step3_title'] ?? ''),
                'text' => sanitize($_POST['services_process_step3_text'] ?? '')
            ],
            [
                'number' => '04',
                'title' => sanitize($_POST['services_process_step4_title'] ?? ''),
                'text' => sanitize($_POST['services_process_step4_text'] ?? '')
            ]
        ];
        
        if (writeJson('customization.json', $customization)) {
            $message = 'Einstellungen wurden erfolgreich gespeichert! Änderungen werden in index.html übernommen.';
            $messageType = 'success';
        } else {
            $message = 'Fehler beim Speichern!';
            $messageType = 'error';
        }
        $customization = readJson('customization.json');
    }
    
    if ($action === 'add') {
        $features = [];
        if (isset($_POST['features']) && is_array($_POST['features'])) {
            foreach ($_POST['features'] as $feature) {
                $feature = trim(sanitize($feature));
                if (!empty($feature)) {
                    $features[] = $feature;
                }
            }
        }
        
        $newService = [
            'id' => uniqid(),
            'title' => sanitize($_POST['title'] ?? ''),
            'subtitle' => sanitize($_POST['subtitle'] ?? ''),
            'description' => sanitize($_POST['description'] ?? ''),
            'features' => $features,
            'image' => sanitize($_POST['image'] ?? ''),
            'active' => isset($_POST['active'])
        ];
        
        $services[] = $newService;
        writeJson('services.json', $services);
        $message = 'Service wurde erfolgreich hinzugefügt! Änderungen werden in index.html übernommen.';
        $messageType = 'success';
        $services = readJson('services.json');
    } elseif ($action === 'edit') {
        $id = $_POST['id'] ?? '';
        foreach ($services as $key => $service) {
            if ($service['id'] === $id) {
                $features = [];
                if (isset($_POST['features']) && is_array($_POST['features'])) {
                    foreach ($_POST['features'] as $feature) {
                        $feature = trim(sanitize($feature));
                        if (!empty($feature)) {
                            $features[] = $feature;
                        }
                    }
                }
                
                $services[$key]['title'] = sanitize($_POST['title'] ?? '');
                $services[$key]['subtitle'] = sanitize($_POST['subtitle'] ?? '');
                $services[$key]['description'] = sanitize($_POST['description'] ?? '');
                $services[$key]['features'] = $features;
                $services[$key]['image'] = sanitize($_POST['image'] ?? '');
                $services[$key]['active'] = isset($_POST['active']);
                writeJson('services.json', $services);
                $message = 'Service wurde erfolgreich aktualisiert! Änderungen werden in index.html übernommen.';
                $messageType = 'success';
                break;
            }
        }
        $services = readJson('services.json');
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        foreach ($services as $key => $service) {
            if ($service['id'] === $id) {
                unset($services[$key]);
                $services = array_values($services);
                writeJson('services.json', $services);
                $message = 'Shërbimi u fshi me sukses! Ndryshimet reflektohen në index.html';
                $messageType = 'success';
                break;
            }
        }
        $services = readJson('services.json');
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

        <!-- Services Settings -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-cog text-primary mr-2"></i>
                Services Settings
            </h2>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="update_settings">
                
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
                                <span class="text-primary font-bold mr-1">*</span>Section Subtitle
                            </label>
                            <input type="text" name="services_section_subtitle" placeholder="Unsere Expertise" value="<?php echo htmlspecialchars($customization['services']['section_subtitle'] ?? 'Unsere Expertise'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Section Title Line 1
                                </label>
                                <input type="text" name="services_section_title_line1" placeholder="Premium Materialien," value="<?php echo htmlspecialchars($customization['services']['section_title_line1'] ?? 'Premium Materialien,'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="text-primary font-bold mr-1">*</span>Section Title Line 2
                                </label>
                                <input type="text" name="services_section_title_line2" placeholder="Professionelle Verlegung" value="<?php echo htmlspecialchars($customization['services']['section_title_line2'] ?? 'Professionelle Verlegung'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Section Description
                            </label>
                            <textarea name="services_section_description" rows="2" placeholder="Wir spezialisieren uns auf die Verlegung und Bearbeitung von hochwertigen Natursteinen und Keramik."
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['services']['section_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="flex items-center space-x-4 pt-2">
                            <label class="flex items-center">
                                <span class="text-primary font-bold mr-1">*</span>
                                <input type="checkbox" name="services_show_in_index" <?php echo ($customization['services']['show_in_index'] ?? true) ? 'checked' : ''; ?> class="mr-2">
                                <span class="text-sm">Shfaq Services në Index</span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Max Cards në Index
                            </label>
                            <input type="number" name="services_max_cards_index" value="<?php echo $customization['services']['max_cards_index'] ?? 6; ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: SERVICES.HTML ONLY -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                        <h3 class="text-lg font-bold text-gray-700">VETËM PËR SERVICES.HTML</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Section</label>
                            <div class="space-y-3">
                                <input type="text" name="services_hero_image" data-media-picker="image" placeholder="Hero Image URL"
                                       value="<?php echo htmlspecialchars($customization['services']['hero_image'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <input type="text" name="services_full_title" placeholder="Hero Title" value="<?php echo htmlspecialchars($customization['services']['full_title'] ?? 'Unsere Leistungen'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="services_full_description" rows="2" placeholder="Hero Subtitle"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['services']['full_description'] ?? 'Premium Materialien, Professionelle Verlegung'); ?></textarea>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Main Section (Mund të jenë të ndryshme nga index.html)</label>
                            <div class="space-y-3">
                                <input type="text" name="services_page_subtitle" placeholder="Section Subtitle (për services.html)" value="<?php echo htmlspecialchars($customization['services']['page_subtitle'] ?? ''); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <input type="text" name="services_page_title_line1" placeholder="Section Title Line 1 (për services.html)" value="<?php echo htmlspecialchars($customization['services']['page_title_line1'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                    <input type="text" name="services_page_title_line2" placeholder="Section Title Line 2 (për services.html)" value="<?php echo htmlspecialchars($customization['services']['page_title_line2'] ?? ''); ?>" 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <textarea name="services_page_description" rows="2" placeholder="Section Description (për services.html)"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['services']['page_description'] ?? ''); ?></textarea>
                                <p class="text-xs text-gray-500">Nëse këto fushat lihen bosh, do të përdoren vlerat nga index.html</p>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Zusätzliche Leistungen Section</label>
                            <div class="space-y-3">
                                <input type="text" name="services_additional_title" placeholder="Section Title" value="<?php echo htmlspecialchars($customization['services']['additional_title'] ?? 'Zusätzliche Leistungen'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <?php 
                                    $additionalCards = $customization['services']['additional_cards'] ?? [
                                        ['icon' => 'fa-tools', 'title' => 'Reparaturen', 'text' => ''],
                                        ['icon' => 'fa-spray-can', 'title' => 'Imprägnierung', 'text' => ''],
                                        ['icon' => 'fa-broom', 'title' => 'Reinigung', 'text' => ''],
                                        ['icon' => 'fa-comments', 'title' => 'Beratung', 'text' => '']
                                    ];
                                    ?>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 1 Title</label>
                                        <input type="text" name="services_additional_card1_title" placeholder="Reparaturen" value="<?php echo htmlspecialchars($additionalCards[0]['title'] ?? 'Reparaturen'); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 1 Text</label>
                                        <textarea name="services_additional_card1_text" rows="2" placeholder="Schnelle und professionelle Reparatur..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($additionalCards[0]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 2 Title</label>
                                        <input type="text" name="services_additional_card2_title" placeholder="Imprägnierung" value="<?php echo htmlspecialchars($additionalCards[1]['title'] ?? 'Imprägnierung'); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 2 Text</label>
                                        <textarea name="services_additional_card2_text" rows="2" placeholder="Professionelle Imprägnierung..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($additionalCards[1]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 3 Title</label>
                                        <input type="text" name="services_additional_card3_title" placeholder="Reinigung" value="<?php echo htmlspecialchars($additionalCards[2]['title'] ?? 'Reinigung'); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 3 Text</label>
                                        <textarea name="services_additional_card3_text" rows="2" placeholder="Gründliche Reinigung..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($additionalCards[2]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 4 Title</label>
                                        <input type="text" name="services_additional_card4_title" placeholder="Beratung" value="<?php echo htmlspecialchars($additionalCards[3]['title'] ?? 'Beratung'); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 4 Text</label>
                                        <textarea name="services_additional_card4_text" rows="2" placeholder="Kostenlose Beratung..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($additionalCards[3]['text'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Why Choose Us Section</label>
                            <div class="space-y-3">
                                <input type="text" name="services_why_title" placeholder="Section Title" value="<?php echo htmlspecialchars($customization['services']['why_title'] ?? 'Warum uns wählen?'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="services_why_description" rows="2" placeholder="Section Description"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['services']['why_description'] ?? ''); ?></textarea>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <?php 
                                    $whyCards = $customization['services']['why_cards'] ?? [
                                        ['icon' => 'fa-check-circle', 'title' => '', 'text' => ''],
                                        ['icon' => 'fa-award', 'title' => '', 'text' => ''],
                                        ['icon' => 'fa-users', 'title' => '', 'text' => '']
                                    ];
                                    ?>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 1 Title</label>
                                        <input type="text" name="services_why_card1_title" placeholder="Title" value="<?php echo htmlspecialchars($whyCards[0]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 1 Text</label>
                                        <textarea name="services_why_card1_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($whyCards[0]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 2 Title</label>
                                        <input type="text" name="services_why_card2_title" placeholder="Title" value="<?php echo htmlspecialchars($whyCards[1]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 2 Text</label>
                                        <textarea name="services_why_card2_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($whyCards[1]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Card 3 Title</label>
                                        <input type="text" name="services_why_card3_title" placeholder="Title" value="<?php echo htmlspecialchars($whyCards[2]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Card 3 Text</label>
                                        <textarea name="services_why_card3_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($whyCards[2]['text'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Process Section</label>
                            <div class="space-y-3">
                                <input type="text" name="services_process_title" placeholder="Section Title" value="<?php echo htmlspecialchars($customization['services']['process_title'] ?? 'Unser Arbeitsprozess'); ?>" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <textarea name="services_process_description" rows="2" placeholder="Section Description"
                                          class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['services']['process_description'] ?? ''); ?></textarea>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <?php 
                                    $processSteps = $customization['services']['process_steps'] ?? [
                                        ['number' => '01', 'title' => '', 'text' => ''],
                                        ['number' => '02', 'title' => '', 'text' => ''],
                                        ['number' => '03', 'title' => '', 'text' => ''],
                                        ['number' => '04', 'title' => '', 'text' => '']
                                    ];
                                    ?>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Step 1 Title</label>
                                        <input type="text" name="services_process_step1_title" placeholder="Title" value="<?php echo htmlspecialchars($processSteps[0]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Step 1 Text</label>
                                        <textarea name="services_process_step1_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($processSteps[0]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Step 2 Title</label>
                                        <input type="text" name="services_process_step2_title" placeholder="Title" value="<?php echo htmlspecialchars($processSteps[1]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Step 2 Text</label>
                                        <textarea name="services_process_step2_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($processSteps[1]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Step 3 Title</label>
                                        <input type="text" name="services_process_step3_title" placeholder="Title" value="<?php echo htmlspecialchars($processSteps[2]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Step 3 Text</label>
                                        <textarea name="services_process_step3_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($processSteps[2]['text'] ?? ''); ?></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 mb-1">Step 4 Title</label>
                                        <input type="text" name="services_process_step4_title" placeholder="Title" value="<?php echo htmlspecialchars($processSteps[3]['title'] ?? ''); ?>" 
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <label class="block text-xs text-gray-600 mb-1 mt-2">Step 4 Text</label>
                                        <textarea name="services_process_step4_text" rows="3" placeholder="Description..."
                                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($processSteps[3]['text'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all mt-4">
                    <i class="fas fa-save mr-2"></i>Ruaj Settings
                </button>
            </form>
        </div>

        <!-- Add New Service Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary mr-2"></i>
                Shto Shërbim të Ri
            </h2>
            <form method="POST" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titulli (p.sh. Keramik Fliesen)</label>
                        <input type="text" name="title" required placeholder="Keramik Fliesen"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle (p.sh. Keramik)</label>
                        <input type="text" name="subtitle" required placeholder="Keramik"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                    <textarea name="description" required rows="3" placeholder="Professionelle Verlegung von Keramikfliesen für Bäder, Küchen, Wohnräume und Außenbereiche."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Features (Kolona - shto një për rresht)</label>
                    <div id="features-container" class="space-y-2">
                        <input type="text" name="features[]" placeholder="Badezimmer und Küchenfliesen"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <button type="button" onclick="addFeature()" class="mt-2 text-primary hover:underline text-sm">
                        <i class="fas fa-plus mr-1"></i>Shto Feature tjetër
                    </button>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto URL</label>
                    <input type="text" name="image" id="image_path" data-media-picker="image" placeholder="uploads/image.jpg"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="active" checked>
                        <span>Aktiv</span>
                    </label>
                </div>
                <div>
                    <button type="submit" class="w-full bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Ruaj Shërbim
                    </button>
                </div>
            </form>
            
            <script>
                function addFeature() {
                    const container = document.getElementById('features-container');
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'features[]';
                    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg';
                    input.placeholder = 'Feature...';
                    container.appendChild(input);
                }
            </script>
        </div>

        <!-- Services List -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-list text-primary mr-2"></i>
                Lista e Shërbimeve (<?php echo count($services); ?>)
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Titulli</th>
                            <th class="px-4 py-3 text-left">Përshkrimi</th>
                            <th class="px-4 py-3 text-left">Imazhi</th>
                            <th class="px-4 py-3 text-left">Statusi</th>
                            <th class="px-4 py-3 text-left">Veprime</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars($service['title']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($service['description']); ?></td>
                                <td class="px-4 py-3">
                                    <?php if ($service['image']): ?>
                                        <img src="../<?php echo htmlspecialchars($service['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($service['title']); ?>"
                                             class="w-16 h-16 object-cover rounded">
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php if ($service['active']): ?>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Aktiv</span>
                                    <?php else: ?>
                                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm">Jo Aktiv</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <button onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)" 
                                                class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="inline" onsubmit="return confirm('A jeni të sigurt?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                            <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-blue-900 mb-2">Informacion</h3>
                    <p class="text-blue-800 mb-3">
                        Shërbimet që shtoni ose ndryshoni këtu reflektohen automatikisht në <strong>index.html</strong> përmes API-s. 
                        Vetëm shërbimet aktive shfaqen në faqen publike.
                    </p>
                    <a href="../index.html" target="_blank" class="inline-flex items-center bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                        <i class="fas fa-external-link-alt mr-2"></i>Shiko Faqen Publike
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-bold mb-4">Ndrysho Shërbimin</h2>
            <form method="POST" id="editForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="editId">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Titulli</label>
                        <input type="text" name="title" id="editTitle" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                        <input type="text" name="subtitle" id="editSubtitle" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Përshkrimi</label>
                    <textarea name="description" id="editDescription" required rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Features</label>
                    <div id="editFeaturesContainer" class="space-y-2"></div>
                    <button type="button" onclick="addEditFeature()" class="mt-2 text-primary hover:underline text-sm">
                        <i class="fas fa-plus mr-1"></i>Shto Feature tjetër
                    </button>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto URL</label>
                    <input type="text" name="image" id="editImage" data-media-picker="image" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div class="mt-4">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="active" id="editActive">
                        <span>Aktiv</span>
                    </label>
                </div>
                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" onclick="closeEditModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Anulo
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-semibold shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>Ruaj Ndryshimet
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editService(service) {
            document.getElementById('editId').value = service.id;
            document.getElementById('editTitle').value = service.title || '';
            document.getElementById('editSubtitle').value = service.subtitle || '';
            document.getElementById('editDescription').value = service.description || '';
            document.getElementById('editImage').value = service.image || '';
            document.getElementById('editActive').checked = service.active || false;
            
            // Load features
            const container = document.getElementById('editFeaturesContainer');
            container.innerHTML = '';
            if (service.features && Array.isArray(service.features)) {
                service.features.forEach(feature => {
                    const input = document.createElement('input');
                    input.type = 'text';
                    input.name = 'features[]';
                    input.value = feature;
                    input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg';
                    container.appendChild(input);
                });
            } else {
                addEditFeature();
            }
            
            document.getElementById('editModal').classList.remove('hidden');
        }
        
        function addEditFeature() {
            const container = document.getElementById('editFeaturesContainer');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'features[]';
            input.className = 'w-full px-4 py-2 border border-gray-300 rounded-lg';
            input.placeholder = 'Feature...';
            container.appendChild(input);
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>
