<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'Rechtliches (Impressum, Datenschutz & AGB)';

// Load legal data
$legal = readJson('legal.json');

// Get active tab from URL or default to impressum
$activeTab = $_GET['tab'] ?? 'impressum';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tab = $_POST['tab'] ?? 'impressum';
    
    if ($tab === 'impressum') {
        // Impressum data - preserve text exactly as entered
        $legal['impressum']['company_name'] = trim($_POST['company_name'] ?? '');
        $legal['impressum']['legal_form'] = trim($_POST['legal_form'] ?? '');
        $legal['impressum']['address']['street'] = trim($_POST['address_street'] ?? '');
        $legal['impressum']['address']['city'] = trim($_POST['address_city'] ?? '');
        $legal['impressum']['address']['country'] = trim($_POST['address_country'] ?? '');
        $legal['impressum']['contact']['phone'] = trim($_POST['contact_phone'] ?? '');
        $legal['impressum']['contact']['email'] = trim($_POST['contact_email'] ?? '');
        $legal['impressum']['contact']['website'] = trim($_POST['contact_website'] ?? '');
        $legal['impressum']['register']['registered'] = isset($_POST['register_registered']);
        $legal['impressum']['register']['register_court'] = trim($_POST['register_court'] ?? '');
        $legal['impressum']['register']['register_number'] = trim($_POST['register_number'] ?? '');
        $legal['impressum']['tax']['ust_id'] = trim($_POST['ust_id'] ?? '');
        $legal['impressum']['tax']['tax_number'] = trim($_POST['tax_number'] ?? '');
        $legal['impressum']['management']['name'] = trim($_POST['management_name'] ?? '');
        $legal['impressum']['responsible_person']['name'] = trim($_POST['responsible_person_name'] ?? '');
        $legal['impressum']['responsible_person']['person'] = trim($_POST['responsible_person_person'] ?? '');
        $legal['impressum']['responsible_person']['address']['street'] = trim($_POST['responsible_address_street'] ?? '');
        $legal['impressum']['responsible_person']['address']['city'] = trim($_POST['responsible_address_city'] ?? '');
        $legal['impressum']['responsible_person']['address']['country'] = trim($_POST['responsible_address_country'] ?? '');
        
        // Disclaimer sections - preserve newlines and text exactly as entered
        $legal['impressum']['disclaimer']['haftung_fuer_inhalte'] = trim($_POST['disclaimer_haftung_inhalte'] ?? '');
        $legal['impressum']['disclaimer']['haftung_fuer_links'] = trim($_POST['disclaimer_haftung_links'] ?? '');
        $legal['impressum']['disclaimer']['urheberrecht'] = trim($_POST['disclaimer_urheberrecht'] ?? '');
        $legal['impressum']['disclaimer']['streitschlichtung'] = trim($_POST['disclaimer_streitschlichtung'] ?? '');
        
    } elseif ($tab === 'datenschutz') {
        // Datenschutz responsible person
        $legal['datenschutz']['responsible_person']['name'] = trim($_POST['ds_responsible_name'] ?? '');
        $legal['datenschutz']['responsible_person']['person'] = trim($_POST['ds_responsible_person'] ?? '');
        $legal['datenschutz']['responsible_person']['address']['street'] = trim($_POST['ds_address_street'] ?? '');
        $legal['datenschutz']['responsible_person']['address']['city'] = trim($_POST['ds_address_city'] ?? '');
        $legal['datenschutz']['responsible_person']['address']['country'] = trim($_POST['ds_address_country'] ?? '');
        $legal['datenschutz']['responsible_person']['phone'] = trim($_POST['ds_phone'] ?? '');
        $legal['datenschutz']['responsible_person']['email'] = trim($_POST['ds_email'] ?? '');
        
        // Datenschutz sections
        if (isset($legal['datenschutz']['sections'])) {
            $legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['allgemeine_hinweise'] = trim($_POST['ds_allgemeine_hinweise'] ?? '');
            $legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['datenerfassung'] = trim($_POST['ds_datenerfassung'] ?? '');
            $legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['datenverwendung'] = trim($_POST['ds_datenverwendung'] ?? '');
            $legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['rechte'] = trim($_POST['ds_rechte'] ?? '');
            $legal['datenschutz']['sections']['hosting'] = trim($_POST['ds_hosting'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['datenschutz'] = trim($_POST['ds_allgemeine_hinweise_pflicht'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['speicherdauer'] = trim($_POST['ds_speicherdauer'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['rechtsgrundlagen'] = trim($_POST['ds_rechtsgrundlagen'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['widerruf'] = trim($_POST['ds_widerruf'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['widerspruchsrecht'] = trim($_POST['ds_widerspruchsrecht'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['beschwerderecht'] = trim($_POST['ds_beschwerderecht'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['datenuebertragbarkeit'] = trim($_POST['ds_datenuebertragbarkeit'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['ssl_tls'] = trim($_POST['ds_ssl_tls'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['auskunft_loeschung'] = trim($_POST['ds_auskunft_loeschung'] ?? '');
            $legal['datenschutz']['sections']['allgemeine_hinweise']['einschraenkung'] = trim($_POST['ds_einschraenkung'] ?? '');
            $legal['datenschutz']['sections']['datenerfassung']['kontaktformular'] = trim($_POST['ds_kontaktformular'] ?? '');
            $legal['datenschutz']['sections']['datenerfassung']['server_log'] = trim($_POST['ds_server_log'] ?? '');
            $legal['datenschutz']['sections']['cookies']['allgemeine_informationen'] = trim($_POST['ds_cookies_allgemein'] ?? '');
            $legal['datenschutz']['sections']['cookies']['zukunft'] = trim($_POST['ds_cookies_zukunft'] ?? '');
            $legal['datenschutz']['sections']['cookies']['technisch_notwendig'] = trim($_POST['ds_cookies_technisch'] ?? '');
            $legal['datenschutz']['sections']['plugins']['google_fonts'] = trim($_POST['ds_google_fonts'] ?? '');
            $legal['datenschutz']['sections']['plugins']['font_awesome'] = trim($_POST['ds_font_awesome'] ?? '');
            $legal['datenschutz']['sections']['aenderung'] = trim($_POST['ds_aenderung'] ?? '');
        }
        
    } elseif ($tab === 'agb') {
        // AGB sections
        if (isset($legal['agb']['sections'])) {
            foreach ($legal['agb']['sections'] as $sectionKey => $sectionData) {
                if (is_array($sectionData)) {
                    foreach ($sectionData as $key => $value) {
                        if ($key === 'list_items' && is_array($value)) {
                            // Handle list items
                            $listItems = [];
                            if (isset($_POST['agb_' . $sectionKey . '_list_items'])) {
                                $items = explode("\n", $_POST['agb_' . $sectionKey . '_list_items']);
                                foreach ($items as $item) {
                                    $item = trim($item);
                                    if (!empty($item)) {
                                        $listItems[] = $item;
                                    }
                                }
                            }
                            $legal['agb']['sections'][$sectionKey]['list_items'] = $listItems;
                        } else {
                            $postKey = 'agb_' . $sectionKey . '_' . $key;
                            if (isset($_POST[$postKey])) {
                                $legal['agb']['sections'][$sectionKey][$key] = trim($_POST[$postKey]);
                            }
                        }
                    }
                }
            }
        }
        
        // AGB footer
        $legal['agb']['footer']['stand'] = trim($_POST['agb_footer_stand'] ?? '');
        $legal['agb']['footer']['company_name'] = trim($_POST['agb_footer_company_name'] ?? '');
        $legal['agb']['footer']['address'] = trim($_POST['agb_footer_address'] ?? '');
        $legal['agb']['footer']['phone'] = trim($_POST['agb_footer_phone'] ?? '');
        $legal['agb']['footer']['email'] = trim($_POST['agb_footer_email'] ?? '');
    }
    
    // Save to JSON
    if (writeJson('legal.json', $legal)) {
        $message = 'Daten wurden erfolgreich gespeichert!';
        $messageType = 'success';
    } else {
        $message = 'Fehler beim Speichern der Daten!';
        $messageType = 'error';
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
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #004499;
        }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/sidebar.php'; ?>
    <?php include 'includes/header.php'; ?>
    
    <div class="ml-64 pt-16 p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo $pageTitle; ?></h1>
        <p class="text-gray-600">Bearbeiten Sie die rechtlichen Informationen für Impressum, Datenschutz und AGB</p>
    </div>

    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8">
            <a href="?tab=impressum" class="py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'impressum' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                Impressum
            </a>
            <a href="?tab=datenschutz" class="py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'datenschutz' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                Datenschutz
            </a>
            <a href="?tab=agb" class="py-4 px-1 border-b-2 font-medium text-sm <?php echo $activeTab === 'agb' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                AGB
            </a>
        </nav>
    </div>

    <?php if ($activeTab === 'impressum'): ?>
    <form method="POST" action="?tab=impressum" class="space-y-6">
        <input type="hidden" name="tab" value="impressum">
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Angaben gemäß § 5 TMG</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Firmenname *</label>
                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($legal['impressum']['company_name'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechtsform *</label>
                    <select name="legal_form" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                        <option value="GmbH" <?php echo ($legal['impressum']['legal_form'] ?? '') === 'GmbH' ? 'selected' : ''; ?>>GmbH</option>
                        <option value="UG (haftungsbeschränkt)" <?php echo ($legal['impressum']['legal_form'] ?? '') === 'UG (haftungsbeschränkt)' ? 'selected' : ''; ?>>UG (haftungsbeschränkt)</option>
                        <option value="Einzelunternehmen" <?php echo ($legal['impressum']['legal_form'] ?? '') === 'Einzelunternehmen' ? 'selected' : ''; ?>>Einzelunternehmen</option>
                        <option value="GbR" <?php echo ($legal['impressum']['legal_form'] ?? '') === 'GbR' ? 'selected' : ''; ?>>GbR</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Straße *</label>
                    <input type="text" name="address_street" value="<?php echo htmlspecialchars($legal['impressum']['address']['street'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stadt und Postleitzahl *</label>
                    <input type="text" name="address_city" value="<?php echo htmlspecialchars($legal['impressum']['address']['city'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Land *</label>
                    <input type="text" name="address_country" value="<?php echo htmlspecialchars($legal['impressum']['address']['country'] ?? 'Deutschland'); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Kontakt</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon *</label>
                    <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($legal['impressum']['contact']['phone'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-Mail *</label>
                    <input type="email" name="contact_email" value="<?php echo htmlspecialchars($legal['impressum']['contact']['email'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                    <input type="text" name="contact_website" value="<?php echo htmlspecialchars($legal['impressum']['contact']['website'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Registereintrag</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="register_registered" <?php echo ($legal['impressum']['register']['registered'] ?? false) ? 'checked' : ''; ?> 
                               class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="text-sm font-medium text-gray-700">Im Handelsregister eingetragen?</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registergericht</label>
                    <input type="text" name="register_court" value="<?php echo htmlspecialchars($legal['impressum']['register']['register_court'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Registernummer (HRB)</label>
                    <input type="text" name="register_number" value="<?php echo htmlspecialchars($legal['impressum']['register']['register_number'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Umsatzsteuer-ID (USt-IdNr.)</label>
                    <input type="text" name="ust_id" value="<?php echo htmlspecialchars($legal['impressum']['tax']['ust_id'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Steuernummer</label>
                    <input type="text" name="tax_number" value="<?php echo htmlspecialchars($legal['impressum']['tax']['tax_number'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Geschäftsführer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vollständiger Name *</label>
                    <input type="text" name="management_name" value="<?php echo htmlspecialchars($legal['impressum']['management']['name'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name der Firma</label>
                    <input type="text" name="responsible_person_name" value="<?php echo htmlspecialchars($legal['impressum']['responsible_person']['name'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vollständiger Name</label>
                    <input type="text" name="responsible_person_person" value="<?php echo htmlspecialchars($legal['impressum']['responsible_person']['person'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Straße</label>
                    <input type="text" name="responsible_address_street" value="<?php echo htmlspecialchars($legal['impressum']['responsible_person']['address']['street'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stadt und Postleitzahl</label>
                    <input type="text" name="responsible_address_city" value="<?php echo htmlspecialchars($legal['impressum']['responsible_person']['address']['city'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Land</label>
                    <input type="text" name="responsible_address_country" value="<?php echo htmlspecialchars($legal['impressum']['responsible_person']['address']['country'] ?? 'Deutschland'); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Haftungsausschluss (Disclaimer)</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Haftung für Inhalte</label>
                    <textarea name="disclaimer_haftung_inhalte" rows="6" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['impressum']['disclaimer']['haftung_fuer_inhalte'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Haftung für Links</label>
                    <textarea name="disclaimer_haftung_links" rows="6" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['impressum']['disclaimer']['haftung_fuer_links'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urheberrecht</label>
                    <textarea name="disclaimer_urheberrecht" rows="6" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['impressum']['disclaimer']['urheberrecht'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Streitschlichtung</label>
                    <textarea name="disclaimer_streitschlichtung" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['impressum']['disclaimer']['streitschlichtung'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Impressum speichern
            </button>
        </div>
    </form>
    <?php endif; ?>

    <?php if ($activeTab === 'datenschutz'): ?>
    <form method="POST" action="?tab=datenschutz" class="space-y-6">
        <input type="hidden" name="tab" value="datenschutz">
        
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Verantwortliche Stelle</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name der Firma</label>
                    <input type="text" name="ds_responsible_name" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['name'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Vollständiger Name</label>
                    <input type="text" name="ds_responsible_person" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['person'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Straße</label>
                    <input type="text" name="ds_address_street" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['address']['street'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stadt und Postleitzahl</label>
                    <input type="text" name="ds_address_city" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['address']['city'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Land</label>
                    <input type="text" name="ds_address_country" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['address']['country'] ?? 'Deutschland'); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="ds_phone" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['phone'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-Mail</label>
                    <input type="email" name="ds_email" value="<?php echo htmlspecialchars($legal['datenschutz']['responsible_person']['email'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">1. Datenschutz auf einen Blick</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Allgemeine Hinweise</label>
                    <textarea name="ds_allgemeine_hinweise" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['allgemeine_hinweise'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datenerfassung auf dieser Website</label>
                    <textarea name="ds_datenerfassung" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['datenerfassung'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Wie werden Ihre Daten verwendet?</label>
                    <textarea name="ds_datenverwendung" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['datenverwendung'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Welche Rechte haben Sie?</label>
                    <textarea name="ds_rechte" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenschutz_auf_einen_blick']['rechte'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">2. Hosting</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hosting</label>
                <textarea name="ds_hosting" rows="6" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['hosting'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">3. Allgemeine Hinweise und Pflichtinformationen</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datenschutz</label>
                    <textarea name="ds_allgemeine_hinweise_pflicht" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['datenschutz'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Speicherdauer</label>
                    <textarea name="ds_speicherdauer" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['speicherdauer'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rechtsgrundlagen</label>
                    <textarea name="ds_rechtsgrundlagen" rows="5" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['rechtsgrundlagen'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Widerruf Ihrer Einwilligung zur Datenverarbeitung</label>
                    <textarea name="ds_widerruf" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['widerruf'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Widerspruchsrecht</label>
                    <textarea name="ds_widerspruchsrecht" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['widerspruchsrecht'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Beschwerderecht</label>
                    <textarea name="ds_beschwerderecht" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['beschwerderecht'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Datenübertragbarkeit</label>
                    <textarea name="ds_datenuebertragbarkeit" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['datenuebertragbarkeit'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">SSL- bzw. TLS-Verschlüsselung</label>
                    <textarea name="ds_ssl_tls" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['ssl_tls'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auskunft, Löschung und Berichtigung</label>
                    <textarea name="ds_auskunft_loeschung" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['auskunft_loeschung'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recht auf Einschränkung der Verarbeitung</label>
                    <textarea name="ds_einschraenkung" rows="5" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['allgemeine_hinweise']['einschraenkung'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">4. Datenerfassung auf dieser Website</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kontaktformular</label>
                    <textarea name="ds_kontaktformular" rows="5" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenerfassung']['kontaktformular'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Server-Log-Dateien</label>
                    <textarea name="ds_server_log" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['datenerfassung']['server_log'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">5. Cookies</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Allgemeine Informationen zu Cookies</label>
                    <textarea name="ds_cookies_allgemein" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['cookies']['allgemeine_informationen'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zukunft</label>
                    <textarea name="ds_cookies_zukunft" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['cookies']['zukunft'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Technisch notwendige Cookies</label>
                    <textarea name="ds_cookies_technisch" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['cookies']['technisch_notwendig'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">6. Plugins und Tools</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Google Fonts (lokales Hosting)</label>
                    <textarea name="ds_google_fonts" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['plugins']['google_fonts'] ?? ''); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Font Awesome</label>
                    <textarea name="ds_font_awesome" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['plugins']['font_awesome'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">7. Änderung dieser Datenschutzerklärung</h2>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Änderung</label>
                <textarea name="ds_aenderung" rows="3" 
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($legal['datenschutz']['sections']['aenderung'] ?? ''); ?></textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-lg font-medium transition-colors">
                Datenschutz speichern
            </button>
        </div>
    </form>
    <?php endif; ?>

    <?php if ($activeTab === 'agb'): ?>
    <form method="POST" action="?tab=agb" class="space-y-6">
        <input type="hidden" name="tab" value="agb">
        
        <?php
        $agbSections = [
            '1_geltungsbereich' => '1. Geltungsbereich',
            '2_vertragsgegenstand' => '2. Vertragsgegenstand',
            '3_angebote' => '3. Angebote',
            '4_preise' => '4. Preise',
            '5_ausfuehrung' => '5. Ausführung',
            '6_lieferzeiten' => '6. Lieferzeiten',
            '7_materialien' => '7. Materialien',
            '8_gewaehrleistung' => '8. Gewährleistung',
            '9_abnahme' => '9. Abnahme',
            '10_haftung' => '10. Haftung',
            '11_aufrechnung' => '11. Aufrechnung',
            '12_datenschutz' => '12. Datenschutz',
            '13_schlussbestimmungen' => '13. Schlussbestimmungen',
            '14_widerrufsrecht' => '14. Widerrufsrecht'
        ];
        
        foreach ($agbSections as $sectionKey => $sectionTitle):
            $sectionData = $legal['agb']['sections'][$sectionKey] ?? [];
        ?>
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6"><?php echo htmlspecialchars($sectionTitle); ?></h2>
            <div class="space-y-4">
                <?php
                if (is_array($sectionData)):
                    foreach ($sectionData as $key => $value):
                        if ($key === 'list_items' && is_array($value)):
                ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Listeneinträge (eine Zeile pro Eintrag)</label>
                    <textarea name="agb_<?php echo htmlspecialchars($sectionKey); ?>_list_items" rows="5" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars(implode("\n", $value)); ?></textarea>
                </div>
                <?php
                        elseif ($key !== 'list_items'):
                ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?></label>
                    <textarea name="agb_<?php echo htmlspecialchars($sectionKey); ?>_<?php echo htmlspecialchars($key); ?>" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"><?php echo htmlspecialchars($value); ?></textarea>
                </div>
                <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Footer</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Stand</label>
                    <input type="text" name="agb_footer_stand" value="<?php echo htmlspecialchars($legal['agb']['footer']['stand'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Firmenname</label>
                    <input type="text" name="agb_footer_company_name" value="<?php echo htmlspecialchars($legal['agb']['footer']['company_name'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <input type="text" name="agb_footer_address" value="<?php echo htmlspecialchars($legal['agb']['footer']['address'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Telefon</label>
                    <input type="text" name="agb_footer_phone" value="<?php echo htmlspecialchars($legal['agb']['footer']['phone'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">E-Mail</label>
                    <input type="email" name="agb_footer_email" value="<?php echo htmlspecialchars($legal['agb']['footer']['email'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-lg font-medium transition-colors">
                AGB speichern
            </button>
        </div>
    </form>
    <?php endif; ?>
    </div>
</body>
</html>
