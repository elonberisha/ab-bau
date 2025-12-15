<?php
// Load legal data
function loadLegalData() {
    $path = __DIR__ . '/data/legal.json';
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    // Remove UTF-8 BOM if present
    if (substr($content, 0, 3) == "\xef\xbb\xbf") {
        $content = substr($content, 3);
    }
    // Ensure UTF-8 encoding
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'auto');
    }
    $decoded = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log("JSON decode error in agb.php: " . json_last_error_msg());
        return [];
    }
    return $decoded ?: [];
}

$legal = loadLegalData();
$agb = $legal['agb'] ?? [];
$impressum = $legal['impressum'] ?? [];

// Helper function to safely get values with proper UTF-8 encoding
function getValue($data, $default = '') {
    $value = $data ?? $default;
    // Ensure UTF-8 encoding and escape HTML special characters
    if (is_string($value)) {
        // Convert to UTF-8 if not already
        if (!mb_check_encoding($value, 'UTF-8')) {
            $value = mb_convert_encoding($value, 'UTF-8', 'auto');
        }
    }
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Helper function to get list items
function getListItems($items) {
    if (is_array($items)) {
        return $items;
    }
    return [];
}
?>
<!DOCTYPE html>
<html lang="de" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="de">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Allgemeine Geschäftsbedingungen - <?php echo getValue($impressum['company_name'] ?? ''); ?>">
    <meta name="language" content="de">
    <meta name="geo.region" content="DE">
    <title>AGB - <?php echo getValue($impressum['company_name'] ?? ''); ?></title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="dist/css/output.css">
    
    <!-- Google Fonts (Local) -->
    <link rel="stylesheet" href="assets/css/google-fonts.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="font-sans antialiased">
    
    <!-- Navigation -->
    <nav id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-white shadow-md transition-all duration-500">
        <div class="container mx-auto px-3 sm:px-4 lg:px-6 max-w-7xl">
            <div class="flex items-center justify-between h-16 sm:h-18 lg:h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.html" class="flex items-center group">
                        <img src="logo.svg" alt="AB Bau Logo" class="h-10 sm:h-12 lg:h-14 w-auto transition-transform duration-300 group-hover:scale-105 brightness-0 contrast-100">
                    </a>
                </div>
                
                <!-- Desktop Menu -->
                <ul class="hidden lg:flex items-center space-x-1 xl:space-x-2">
                    <li><a href="about.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">
                        Über uns
                    </a></li>
                    <li><a href="services.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">
                        Leistungen
                    </a></li>
                    <li><a href="portfolio.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">
                        Projekte
                    </a></li>
                    <li><a href="catalogs.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">
                        Kataloge
                    </a></li>
                    <li><a href="contact.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">
                        Kontakt
                    </a></li>
                </ul>
                
                <!-- Mobile Menu Button -->
                <button id="hamburger" class="lg:hidden flex flex-col space-y-1.5 p-2 z-50 group">
                    <span class="w-6 h-0.5 bg-gray-800 transition-all duration-300 group-hover:bg-primary"></span>
                    <span class="w-6 h-0.5 bg-gray-800 transition-all duration-300 group-hover:bg-primary"></span>
                    <span class="w-6 h-0.5 bg-gray-800 transition-all duration-300 group-hover:bg-primary"></span>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t shadow-2xl fixed top-16 sm:top-18 left-0 right-0 z-40 max-h-0 overflow-hidden transition-all duration-300">
            <ul class="flex flex-col space-y-1 p-3 sm:p-4">
                <li><a href="index.html" class="block text-gray-800 hover:text-primary font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-all">Home</a></li>
                <li><a href="about.html" class="block text-gray-800 hover:text-primary font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-all">Über uns</a></li>
                <li><a href="services.html" class="block text-gray-800 hover:text-primary font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-all">Leistungen</a></li>
                <li><a href="portfolio.html" class="block text-gray-800 hover:text-primary font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-all">Portfolio</a></li>
                <li><a href="catalogs.html" class="block text-gray-800 hover:text-primary font-medium py-3 px-4 rounded-xl hover:bg-gray-50 transition-all">Kataloge</a></li>
                <li><a href="contact.html" class="block bg-gradient-to-r from-primary to-primary-dark text-white px-6 py-3 rounded-xl text-center font-semibold mt-2">Kontakt</a></li>
            </ul>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="relative pt-32 sm:pt-36 lg:pt-40 pb-16 sm:pb-20 bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-900/80 via-gray-800/70 to-gray-900/80"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl relative z-10">
            <div class="text-center text-white">
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6">Allgemeine Geschäftsbedingungen</h1>
                <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto">AGB - <?php echo getValue($impressum['company_name'] ?? ''); ?></p>
            </div>
        </div>
    </section>

    <!-- AGB Content -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <div class="prose prose-lg max-w-none">
                
                <?php if (isset($agb['sections'])): ?>
                    <?php 
                    $sectionTitles = [
                        '1_geltungsbereich' => '1. Geltungsbereich',
                        '2_vertragsgegenstand' => '2. Vertragsgegenstand',
                        '3_angebote' => '3. Angebote und Auftragsbestätigung',
                        '4_preise' => '4. Preise und Zahlungsbedingungen',
                        '5_ausfuehrung' => '5. Ausführung der Arbeiten',
                        '6_lieferzeiten' => '6. Lieferzeiten und Fristen',
                        '7_materialien' => '7. Materialien',
                        '8_gewaehrleistung' => '8. Gewährleistung',
                        '9_abnahme' => '9. Abnahme',
                        '10_haftung' => '10. Haftung',
                        '11_aufrechnung' => '11. Aufrechnung und Zurückbehaltung',
                        '12_datenschutz' => '12. Datenschutz',
                        '13_schlussbestimmungen' => '13. Schlussbestimmungen',
                        '14_widerrufsrecht' => '14. Widerrufsrecht'
                    ];
                    
                    foreach ($agb['sections'] as $sectionKey => $sectionData): 
                        $sectionTitle = $agb['titles'][$sectionKey] ?? ($sectionTitles[$sectionKey] ?? $sectionKey);
                    ?>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 <?php echo $sectionKey === '1_geltungsbereich' ? '' : 'mt-12'; ?>"><?php echo getValue($sectionTitle); ?></h2>
                    
                    <?php foreach ($sectionData as $key => $value): ?>
                        <?php if ($key === 'list_items' && is_array($value) && !empty($value)): ?>
                            <ul class="list-disc pl-6 mb-6 text-gray-700 space-y-2">
                                <?php foreach ($value as $item): ?>
                                    <li><?php echo getValue($item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif ($key !== 'list_items' && !empty($value)): ?>
                            <p class="mb-6 text-gray-700">
                                <?php echo nl2br(getValue($value)); ?>
                            </p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($agb['footer'])): ?>
                <div class="mt-12 p-6 bg-gray-50 rounded-lg">
                    <?php if (!empty($agb['footer']['stand'])): ?>
                    <p class="text-sm text-gray-600 mb-2"><strong><?php echo getValue($agb['labels']['stand'] ?? 'Stand:'); ?></strong> <?php echo getValue($agb['footer']['stand']); ?></p>
                    <?php endif; ?>
                    <p class="text-sm text-gray-600">
                        <?php if (!empty($agb['footer']['company_name'])): ?>
                        <?php echo getValue($agb['footer']['company_name']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($agb['footer']['address'])): ?>
                        <?php echo getValue($agb['footer']['address']); ?><br>
                        <?php endif; ?>
                        <?php if (!empty($agb['footer']['phone'])): ?>
                        <?php echo getValue($agb['labels']['telefon'] ?? 'Telefon:'); ?> <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $agb['footer']['phone']); ?>" class="text-primary hover:underline"><?php echo getValue($agb['footer']['phone']); ?></a><br>
                        <?php endif; ?>
                        <?php if (!empty($agb['footer']['email'])): ?>
                        <?php echo getValue($agb['labels']['email'] ?? 'E-Mail:'); ?> <a href="mailto:<?php echo getValue($agb['footer']['email']); ?>" class="text-primary hover:underline"><?php echo getValue($agb['footer']['email']); ?></a>
                        <?php endif; ?>
                    </p>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12 sm:py-16">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-3 mb-4">
                        <a href="index.html" class="flex items-center space-x-3 group">
                            <img src="logo.svg" alt="AB Bau Logo"
                                class="h-10 sm:h-12 w-auto brightness-0 invert transition-transform duration-300 group-hover:scale-105">
                            <span class="text-xl font-bold">AB BAU</span>
                        </a>
                    </div>
                    <p class="text-gray-400 text-sm">Professionelle Bau- und Fliesenarbeiten</p>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Navigation</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="index.html" class="hover:text-white transition-colors">Home</a></li>
                        <li><a href="about.html" class="hover:text-white transition-colors">Über uns</a></li>
                        <li><a href="services.html" class="hover:text-white transition-colors">Leistungen</a></li>
                        <li><a href="portfolio.html" class="hover:text-white transition-colors">Projekte</a></li>
                        <li><a href="contact.html" class="hover:text-white transition-colors">Kontakt</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Kontakt</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="contact.html" class="hover:text-white transition-colors"><?php echo getValue($impressum['address']['street'] ?? ''); ?>, <?php echo getValue($impressum['address']['city'] ?? ''); ?></a></li>
                        <?php if (!empty($impressum['contact']['phone'])): ?>
                        <li><a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $impressum['contact']['phone']); ?>" class="hover:text-white transition-colors break-all"><?php echo getValue($impressum['contact']['phone']); ?></a></li>
                        <?php endif; ?>
                        <?php if (!empty($impressum['contact']['email'])): ?>
                        <li><a href="mailto:<?php echo getValue($impressum['contact']['email']); ?>" class="hover:text-white transition-colors break-all"><?php echo getValue($impressum['contact']['email']); ?></a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Rechtliches</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="impressum.php" class="hover:text-white transition-colors">Impressum</a></li>
                        <li><a href="datenschutz.php" class="hover:text-white transition-colors">Datenschutz</a></li>
                        <li><a href="agb.php" class="hover:text-white transition-colors">AGB</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400 text-sm">
                <p>&copy; 2025 <?php echo getValue($impressum['company_name'] ?? ''); ?><?php echo !empty($impressum['legal_form']) ? ' (' . getValue($impressum['legal_form']) . ')' : ''; ?>. Alle Rechte vorbehalten. | 
                <a href="impressum.php" class="hover:text-white transition-colors">Impressum</a> | 
                <a href="datenschutz.php" class="hover:text-white transition-colors">Datenschutz</a> | 
                <a href="agb.php" class="hover:text-white transition-colors">AGB</a></p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/script.js"></script>
</body>
</html>

