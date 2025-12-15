<?php
require_once 'functions.php';
requireLogin();

$pageTitle = 'Menaxho Kontaktin';
$message = '';
$messageType = '';

$customization = readJson('customization.json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customization['contact']['hero_image'] = sanitize($_POST['hero_image'] ?? '');
    $customization['contact']['section_title'] = sanitize($_POST['section_title'] ?? '');
    $customization['contact']['section_subtitle'] = sanitize($_POST['section_subtitle'] ?? '');
    $customization['contact']['address_label'] = sanitize($_POST['address_label'] ?? '');
    $customization['contact']['address_line1'] = sanitize($_POST['address_line1'] ?? '');
    $customization['contact']['address_line2'] = sanitize($_POST['address_line2'] ?? '');
    $customization['contact']['address_map_link'] = sanitize($_POST['address_map_link'] ?? '');
    $customization['contact']['phone_label'] = sanitize($_POST['phone_label'] ?? '');
    // Convert phone numbers to tel: links if they're not already links
    $phone1 = sanitize($_POST['phone1'] ?? '');
    if ($phone1 && !preg_match('/^tel:/', $phone1)) {
        // Remove all non-digit characters except + and keep only digits and +
        $phone1Clean = preg_replace('/[^\d+]/', '', $phone1);
        $phone1 = 'tel:' . $phone1Clean;
    }
    $customization['contact']['phone1'] = $phone1;
    
    $phone2 = sanitize($_POST['phone2'] ?? '');
    if ($phone2 && !preg_match('/^tel:/', $phone2)) {
        // Remove all non-digit characters except + and keep only digits and +
        $phone2Clean = preg_replace('/[^\d+]/', '', $phone2);
        $phone2 = 'tel:' . $phone2Clean;
    }
    $customization['contact']['phone2'] = $phone2;
    $customization['contact']['email_label'] = sanitize($_POST['email_label'] ?? '');
    $customization['contact']['email'] = sanitize($_POST['email'] ?? '');
    $customization['contact']['whatsapp_number'] = sanitize($_POST['whatsapp_number'] ?? '');
    $customization['contact']['project_manager_title'] = sanitize($_POST['project_manager_title'] ?? '');
    $customization['contact']['project_manager_name'] = sanitize($_POST['project_manager_name'] ?? '');
    $customization['contact']['project_manager_description'] = sanitize($_POST['project_manager_description'] ?? '');
    $customization['contact']['opening_hours_title'] = sanitize($_POST['opening_hours_title'] ?? '');
    $customization['contact']['opening_hours_monday_friday'] = sanitize($_POST['opening_hours_monday_friday'] ?? '');
    $customization['contact']['opening_hours_saturday'] = sanitize($_POST['opening_hours_saturday'] ?? '');
    $customization['contact']['opening_hours_sunday'] = sanitize($_POST['opening_hours_sunday'] ?? '');
    $customization['contact']['form_title'] = sanitize($_POST['form_title'] ?? '');
    $customization['contact']['form_button'] = sanitize($_POST['form_button'] ?? '');
    // Social media links - convert username to full URL if needed
    $facebook = sanitize($_POST['facebook_link'] ?? '');
    if ($facebook && !preg_match('/^https?:\/\//', $facebook)) {
        $facebook = 'https://facebook.com/' . ltrim($facebook, '/');
    }
    $customization['contact']['facebook_link'] = $facebook;
    
    $instagram = sanitize($_POST['instagram_link'] ?? '');
    if ($instagram && !preg_match('/^https?:\/\//', $instagram)) {
        $instagram = 'https://instagram.com/' . ltrim($instagram, '/');
    }
    $customization['contact']['instagram_link'] = $instagram;
    
    $linkedin = sanitize($_POST['linkedin_link'] ?? '');
    if ($linkedin && !preg_match('/^https?:\/\//', $linkedin)) {
        $linkedin = 'https://linkedin.com/in/' . ltrim($linkedin, '/');
    }
    $customization['contact']['linkedin_link'] = $linkedin;
    
    $customization['contact']['whatsapp_link'] = sanitize($_POST['whatsapp_link'] ?? '');

    if (writeJson('customization.json', $customization)) {
            $message = 'Kontakt Einstellungen erfolgreich gespeichert!';
            $messageType = 'success';
        } else {
            $message = 'Fehler beim Speichern!';
            $messageType = 'error';
    }
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

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-address-book text-primary mr-2"></i>
                Kontakt Einstellungen
            </h2>
            <p class="text-xs text-gray-500 mb-4">
                <span class="font-bold">*</span> Felder mit Stern werden im Kontaktbereich auf index.html angezeigt.
            </p>

            <form method="POST" class="space-y-6">
                <!-- Hero Section -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Hero Section (contact.html)</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Bild URL</label>
                            <input type="text" name="hero_image" data-media-picker="image" placeholder="Hero Image URL"
                                   value="<?php echo htmlspecialchars($customization['contact']['hero_image'] ?? ''); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Titel</label>
                            <input type="text" name="section_title" placeholder="z.B. Lassen Sie uns sprechen"
                                   value="<?php echo htmlspecialchars($customization['contact']['section_title'] ?? 'Lassen Sie uns sprechen'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hero Untertitel</label>
                            <textarea name="section_subtitle" rows="2" placeholder="Hero Beschreibung"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['contact']['section_subtitle'] ?? 'Wir freuen uns auf Ihre Anfrage'); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Kontaktinformationen -->
                <div class="bg-blue-50 border-l-4 border-primary p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-home text-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-primary">Kontaktinformationen</h3>
                        <span class="ml-2 text-xs bg-primary text-white px-2 py-1 rounded">*</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Adresse Zeile 1
                            </label>
                            <input type="text" name="address_line1" placeholder="z.B. Talstraße 3d"
                                   value="<?php echo htmlspecialchars($customization['contact']['address_line1'] ?? 'Talstraße 3d'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Adresse Zeile 2
                            </label>
                            <input type="text" name="address_line2" placeholder="z.B. 85238 Petershausen"
                                   value="<?php echo htmlspecialchars($customization['contact']['address_line2'] ?? '85238 Petershausen'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Google Maps Link</label>
                            <input type="text" name="address_map_link" placeholder="Google Maps Embed URL"
                                   value="<?php echo htmlspecialchars($customization['contact']['address_map_link'] ?? ''); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Telefon 1
                            </label>
                            <input type="text" name="phone1" placeholder="z.B. 08137 9957477"
                                   value="<?php 
                                       $p1 = $customization['contact']['phone1'] ?? '08137 9957477';
                                       // Extract number from tel: link if exists
                                       if (preg_match('/^tel:(.+)$/', $p1, $matches)) {
                                           echo htmlspecialchars($matches[1]);
                                       } else {
                                           echo htmlspecialchars($p1);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nummer eingeben (z.B. <strong>08137 9957477</strong>). Wird automatisch zu tel: Link konvertiert.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefon 2</label>
                            <input type="text" name="phone2" placeholder="z.B. 017655537071"
                                   value="<?php 
                                       $p2 = $customization['contact']['phone2'] ?? '017655537071';
                                       // Extract number from tel: link if exists
                                       if (preg_match('/^tel:(.+)$/', $p2, $matches)) {
                                           echo htmlspecialchars($matches[1]);
                                       } else {
                                           echo htmlspecialchars($p2);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nummer eingeben (z.B. <strong>017655537071</strong>). Wird automatisch zu tel: Link konvertiert.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>E-Mail
                            </label>
                            <input type="email" name="email" placeholder="z.B. office@ab-bau.de"
                                   value="<?php echo htmlspecialchars($customization['contact']['email'] ?? 'office@ab-bau.de'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Projektleitung -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Projektleitung</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                            <input type="text" name="project_manager_title" placeholder="z.B. Projektleitung"
                                   value="<?php echo htmlspecialchars($customization['contact']['project_manager_title'] ?? 'Projektleitung'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" name="project_manager_name" placeholder="z.B. Anduena Blakaj"
                                   value="<?php echo htmlspecialchars($customization['contact']['project_manager_name'] ?? 'Anduena Blakaj'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Beschreibung</label>
                            <textarea name="project_manager_description" rows="2" placeholder="z.B. Mit über 15 Jahren Erfahrung in der Branche"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['contact']['project_manager_description'] ?? 'Mit über 15 Jahren Erfahrung in der Branche'); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Öffnungszeiten -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Öffnungszeiten</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Titel</label>
                            <input type="text" name="opening_hours_title" placeholder="z.B. Öffnungszeiten"
                                   value="<?php echo htmlspecialchars($customization['contact']['opening_hours_title'] ?? 'Öffnungszeiten'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Montag - Freitag</label>
                            <input type="text" name="opening_hours_monday_friday" placeholder="z.B. 8:00 - 18:00 Uhr"
                                   value="<?php echo htmlspecialchars($customization['contact']['opening_hours_monday_friday'] ?? '8:00 - 18:00 Uhr'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Samstag</label>
                            <input type="text" name="opening_hours_saturday" placeholder="z.B. 9:00 - 14:00 Uhr"
                                   value="<?php echo htmlspecialchars($customization['contact']['opening_hours_saturday'] ?? '9:00 - 14:00 Uhr'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sonntag</label>
                            <input type="text" name="opening_hours_sunday" placeholder="z.B. Geschlossen"
                                   value="<?php echo htmlspecialchars($customization['contact']['opening_hours_sunday'] ?? 'Geschlossen'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-blue-50 border-l-4 border-primary p-4 rounded-lg">
                    <div class="flex items-center mb-3">
                        <i class="fas fa-share-alt text-primary mr-2"></i>
                        <h3 class="text-lg font-bold text-primary">Social Media Links</h3>
                        <span class="ml-2 text-xs bg-primary text-white px-2 py-1 rounded">*</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Facebook Username
                            </label>
                            <input type="text" name="facebook_link" 
                                   pattern="[a-zA-Z0-9._-]+"
                                   placeholder="z.B. abbau"
                                   value="<?php 
                                       $fb = $customization['contact']['facebook_link'] ?? '';
                                       // Extract username from full URL if exists
                                       if ($fb && preg_match('/facebook\.com\/([^\/\?]+)/', $fb, $matches)) {
                                           echo htmlspecialchars($matches[1]);
                                       } else if ($fb && !preg_match('/^https?:\/\//', $fb)) {
                                           echo htmlspecialchars($fb);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nur Username eingeben (z.B. <strong>abbau</strong>). https://facebook.com/ wird automatisch hinzugefügt.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <span class="text-primary font-bold mr-1">*</span>Instagram Username
                            </label>
                            <input type="text" name="instagram_link" 
                                   pattern="[a-zA-Z0-9._-]+"
                                   placeholder="z.B. abbau"
                                   value="<?php 
                                       $ig = $customization['contact']['instagram_link'] ?? '';
                                       // Extract username from full URL if exists
                                       if ($ig && preg_match('/instagram\.com\/([^\/\?]+)/', $ig, $matches)) {
                                           echo htmlspecialchars($matches[1]);
                                       } else if ($ig && !preg_match('/^https?:\/\//', $ig)) {
                                           echo htmlspecialchars($ig);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nur Username eingeben (z.B. <strong>abbau</strong>). https://instagram.com/ wird automatisch hinzugefügt.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn Username</label>
                            <input type="text" name="linkedin_link" 
                                   pattern="[a-zA-Z0-9._-]+"
                                   placeholder="z.B. berat-blakaj"
                                   value="<?php 
                                       $li = $customization['contact']['linkedin_link'] ?? '';
                                       // Extract username from full URL if exists
                                       if ($li && preg_match('/linkedin\.com\/(in|company)\/([^\/\?]+)/', $li, $matches)) {
                                           echo htmlspecialchars($matches[2]);
                                       } else if ($li && !preg_match('/^https?:\/\//', $li)) {
                                           echo htmlspecialchars($li);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nur Username eingeben (z.B. <strong>berat-blakaj</strong>). https://linkedin.com/in/ wird automatisch hinzugefügt.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp Nummer oder Link</label>
                            <input type="text" name="whatsapp_number" placeholder="z.B. 017655537071 oder https://wa.me/..."
                                   value="<?php 
                                       $wa = $customization['contact']['whatsapp_link'] ?? $customization['contact']['whatsapp_number'] ?? '';
                                       // Extract number from wa.me link if exists
                                       if (preg_match('/wa\.me\/([^\?\/]+)/', $wa, $matches)) {
                                           echo htmlspecialchars($matches[1]);
                                       } else if ($wa && !preg_match('/^https?:\/\//', $wa)) {
                                           echo htmlspecialchars($wa);
                                       } else if ($wa) {
                                           echo htmlspecialchars($wa);
                                       }
                                   ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Nummer eingeben (z.B. <strong>017655537071</strong>) oder vollständigen Link. Wird automatisch zu https://wa.me/... konvertiert.</p>
                        </div>
                    </div>
                </div>

                <!-- Formular -->
                <div class="bg-gray-50 border-l-4 border-gray-600 p-4 rounded-lg">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Kontaktformular</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Formular Titel</label>
                            <input type="text" name="form_title" placeholder="z.B. Kostenloses Angebot anfordern"
                                   value="<?php echo htmlspecialchars($customization['contact']['form_title'] ?? 'Kostenloses Angebot anfordern'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Formular Button Text</label>
                            <input type="text" name="form_button" placeholder="z.B. Anfrage senden"
                                   value="<?php echo htmlspecialchars($customization['contact']['form_button'] ?? 'Anfrage senden'); ?>" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i>Einstellungen speichern
                </button>
            </form>
        </div>
    </div>
</body>
</html>

