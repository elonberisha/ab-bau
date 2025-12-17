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
        'title' => sanitize($_POST['title']),
        'subtitle' => sanitize($_POST['subtitle']),
        'address_line1' => sanitize($_POST['address_line1']),
        'address_line2' => sanitize($_POST['address_line2']),
        'phone1' => sanitize($_POST['phone1']),
        'phone2' => sanitize($_POST['phone2']),
        'email' => sanitize($_POST['email']),
        'whatsapp_number' => sanitize($_POST['whatsapp_number']),
        
        // Projektleitung
        'project_manager_title' => sanitize($_POST['project_manager_title']),
        'project_manager_name' => sanitize($_POST['project_manager_name']),
        'project_manager_description' => sanitize($_POST['project_manager_description']),
        
        // Öffnungszeiten
        'opening_hours_title' => sanitize($_POST['opening_hours_title']),
        'opening_hours_monday_friday' => sanitize($_POST['opening_hours_monday_friday']),
        'opening_hours_saturday' => sanitize($_POST['opening_hours_saturday']),
        'opening_hours_sunday' => sanitize($_POST['opening_hours_sunday']),
        
        // Form Settings
        'form_title' => sanitize($_POST['form_title']),
        'form_button' => sanitize($_POST['form_button']),

        // Social Media
        'facebook_link' => sanitize($_POST['facebook_link']),
        'instagram_link' => sanitize($_POST['instagram_link']),
        'linkedin_link' => sanitize($_POST['linkedin_link']),
        
        // Map
        'map_embed_code' => $_POST['map_embed_code'] // Allow HTML for map
    ];

    if (updateSectionData('contact_section', $data)) {
        $_SESSION['message'] = 'Kontaktdaten wurden erfolgreich aktualisiert!';
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = 'Fehler beim Aktualisieren.';
        $_SESSION['message_type'] = 'error';
    }
    
    header("Location: contact.php");
    exit;
}

// Get Data
$contact = getSectionData('contact_section');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt verwalten - Admin Panel</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
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
                    <i class="fas fa-address-book mr-2 text-primary"></i>Kontakt
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

                <form method="POST" class="space-y-6 max-w-4xl mx-auto">
                    
                    <!-- General Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-heading mr-2 text-blue-500"></i>Titel und Beschreibung
                        </h2>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Seitentitel</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($contact['title'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Untertitel / Beschreibung</label>
                                <textarea name="subtitle" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500/20"><?php echo htmlspecialchars($contact['subtitle'] ?? ''); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Details -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-info-circle mr-2 text-green-500"></i>Kontaktdaten
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Adresse Zeile 1</label>
                                <input type="text" name="address_line1" value="<?php echo htmlspecialchars($contact['address_line1'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Adresse Zeile 2</label>
                                <input type="text" name="address_line2" value="<?php echo htmlspecialchars($contact['address_line2'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-phone text-gray-400 mr-1"></i> Telefon 1</label>
                                <input type="text" name="phone1" value="<?php echo htmlspecialchars($contact['phone1'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-phone text-gray-400 mr-1"></i> Telefon 2</label>
                                <input type="text" name="phone2" value="<?php echo htmlspecialchars($contact['phone2'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fas fa-envelope text-gray-400 mr-1"></i> Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($contact['email'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-whatsapp text-gray-400 mr-1"></i> WhatsApp Number</label>
                                <input type="text" name="whatsapp_number" value="<?php echo htmlspecialchars($contact['whatsapp_number'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Project Management & Opening Hours -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Projektleitung -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <i class="fas fa-user-tie mr-2 text-indigo-500"></i>Projektleitung
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Titel</label>
                                    <input type="text" name="project_manager_title" value="<?php echo htmlspecialchars($contact['project_manager_title'] ?? 'Projektleitung'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                    <input type="text" name="project_manager_name" value="<?php echo htmlspecialchars($contact['project_manager_name'] ?? 'Anduena Blakaj'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung</label>
                                    <textarea name="project_manager_description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($contact['project_manager_description'] ?? 'Mit über 15 Jahren Erfahrung in der Branche'); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Öffnungszeiten -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <i class="fas fa-clock mr-2 text-orange-500"></i>Öffnungszeiten
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Titulli</label>
                                    <input type="text" name="opening_hours_title" value="<?php echo htmlspecialchars($contact['opening_hours_title'] ?? 'Öffnungszeiten'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Montag - Freitag</label>
                                    <input type="text" name="opening_hours_monday_friday" value="<?php echo htmlspecialchars($contact['opening_hours_monday_friday'] ?? '8:00 - 18:00 Uhr'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Samstag</label>
                                    <input type="text" name="opening_hours_saturday" value="<?php echo htmlspecialchars($contact['opening_hours_saturday'] ?? '9:00 - 14:00 Uhr'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sonntag</label>
                                    <input type="text" name="opening_hours_sunday" value="<?php echo htmlspecialchars($contact['opening_hours_sunday'] ?? 'Geschlossen'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form Settings -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-envelope-open-text mr-2 text-teal-500"></i>Formulareinstellungen
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Formulartitel</label>
                                <input type="text" name="form_title" value="<?php echo htmlspecialchars($contact['form_title'] ?? 'Kostenloses Angebot anfordern'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Button-Text</label>
                                <input type="text" name="form_button" value="<?php echo htmlspecialchars($contact['form_button'] ?? 'Anfrage senden'); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-share-alt mr-2 text-purple-500"></i>Soziale Netzwerke
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook Link</label>
                                <input type="text" name="facebook_link" value="<?php echo htmlspecialchars($contact['facebook_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-instagram text-pink-600 mr-1"></i> Instagram Link</label>
                                <input type="text" name="instagram_link" value="<?php echo htmlspecialchars($contact['instagram_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1"><i class="fab fa-linkedin text-blue-700 mr-1"></i> LinkedIn Link</label>
                                <input type="text" name="linkedin_link" value="<?php echo htmlspecialchars($contact['linkedin_link'] ?? ''); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Map Embed -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <i class="fas fa-map mr-2 text-orange-500"></i>Google Maps Embed
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Embed-Code (Iframe)</label>
                            <textarea name="map_embed_code" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-mono text-xs"><?php echo htmlspecialchars($contact['map_embed_code'] ?? ''); ?></textarea>
                            <p class="text-xs text-gray-500 mt-1">Gehen Sie zu Google Maps -> Teilen -> Karte einbetten -> HTML kopieren.</p>
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
</body>
</html>