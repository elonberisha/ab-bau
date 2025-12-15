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
        // Log error for debugging
        error_log("JSON decode error in impressum.php: " . json_last_error_msg());
        return [];
    }
    return $decoded ?: [];
}

$legal = loadLegalData();
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
?>
<!DOCTYPE html>
<html lang="de" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="de">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Impressum - <?php echo getValue($impressum['company_name']); ?>">
    <meta name="language" content="de">
    <meta name="geo.region" content="DE">
    <title>Impressum - <?php echo getValue($impressum['company_name']); ?></title>
    
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
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6">Impressum</h1>
                <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto">Angaben gemäß § 5 TMG</p>
            </div>
        </div>
    </section>

    <!-- Impressum Content -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <div class="prose prose-lg max-w-none">
                
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Angaben gemäß § 5 TMG</h2>
                <div class="mb-8 text-gray-700">
                    <p class="mb-2"><strong><?php echo getValue($impressum['company_name'] ?? ''); ?><?php echo !empty($impressum['legal_form']) ? ' (' . getValue($impressum['legal_form']) . ')' : ''; ?></strong></p>
                    <p class="mb-2"><?php echo getValue($impressum['address']['street'] ?? ''); ?></p>
                    <p class="mb-2"><?php echo getValue($impressum['address']['city'] ?? ''); ?></p>
                    <p class="mb-2"><?php echo getValue($impressum['address']['country'] ?? 'Deutschland'); ?></p>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6 mt-12">Kontakt</h2>
                <div class="mb-8 text-gray-700">
                    <?php if (!empty($impressum['contact']['phone'])): ?>
                    <p class="mb-2"><strong>Telefon:</strong> <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $impressum['contact']['phone']); ?>" class="text-primary hover:underline"><?php echo getValue($impressum['contact']['phone']); ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['contact']['email'])): ?>
                    <p class="mb-2"><strong>E-Mail:</strong> <a href="mailto:<?php echo getValue($impressum['contact']['email']); ?>" class="text-primary hover:underline"><?php echo getValue($impressum['contact']['email']); ?></a></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['contact']['website'])): ?>
                    <p class="mb-2"><strong>Website:</strong> <a href="https://<?php echo getValue($impressum['contact']['website']); ?>" class="text-primary hover:underline" target="_blank" rel="noopener noreferrer"><?php echo getValue($impressum['contact']['website']); ?></a></p>
                    <?php endif; ?>
                </div>

                <?php if (!empty($impressum['register']['registered']) && $impressum['register']['registered']): ?>
                <h2 class="text-2xl font-bold text-gray-900 mb-6 mt-12">Registereintrag</h2>
                <div class="mb-8 text-gray-700">
                    <?php if (!empty($impressum['register']['register_court'])): ?>
                    <p class="mb-2"><strong>Registergericht:</strong> <?php echo getValue($impressum['register']['register_court']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['register']['register_number'])): ?>
                    <p class="mb-2"><strong>Registernummer:</strong> <?php echo getValue($impressum['register']['register_number']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['tax']['ust_id'])): ?>
                    <p class="mb-2"><strong>Umsatzsteuer-ID:</strong><br>
                    Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
                    <?php echo getValue($impressum['tax']['ust_id']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['tax']['tax_number'])): ?>
                    <p class="mb-2"><strong>Steuernummer:</strong> <?php echo getValue($impressum['tax']['tax_number']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($impressum['management']['name'])): ?>
                <h2 class="text-2xl font-bold text-gray-900 mb-6 mt-12">Geschäftsführer</h2>
                <div class="mb-8 text-gray-700">
                    <p><?php echo getValue($impressum['management']['name']); ?></p>
                </div>
                <?php endif; ?>

                <h2 class="text-2xl font-bold text-gray-900 mb-6 mt-12">Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h2>
                <div class="mb-8 text-gray-700">
                    <?php if (!empty($impressum['responsible_person']['name'])): ?>
                    <p class="mb-2"><?php echo getValue($impressum['responsible_person']['name']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['responsible_person']['person'])): ?>
                    <p class="mb-2"><?php echo getValue($impressum['responsible_person']['person']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['responsible_person']['address']['street'])): ?>
                    <p class="mb-2"><?php echo getValue($impressum['responsible_person']['address']['street']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['responsible_person']['address']['city'])): ?>
                    <p class="mb-2"><?php echo getValue($impressum['responsible_person']['address']['city']); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($impressum['responsible_person']['address']['country'])): ?>
                    <p class="mb-2"><?php echo getValue($impressum['responsible_person']['address']['country']); ?></p>
                    <?php endif; ?>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-6 mt-12">Haftungsausschluss (Disclaimer)</h2>
                
                <?php if (!empty($impressum['disclaimer']['haftung_fuer_inhalte'])): ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Haftung für Inhalte</h3>
                <p class="mb-6 text-gray-700">
                    <?php echo nl2br(getValue($impressum['disclaimer']['haftung_fuer_inhalte'])); ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($impressum['disclaimer']['haftung_fuer_links'])): ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Haftung für Links</h3>
                <p class="mb-6 text-gray-700">
                    <?php echo nl2br(getValue($impressum['disclaimer']['haftung_fuer_links'])); ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($impressum['disclaimer']['urheberrecht'])): ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Urheberrecht</h3>
                <p class="mb-6 text-gray-700">
                    <?php echo nl2br(getValue($impressum['disclaimer']['urheberrecht'])); ?>
                </p>
                <?php endif; ?>

                <?php if (!empty($impressum['disclaimer']['streitschlichtung'])): ?>
                <h3 class="text-xl font-bold text-gray-900 mb-4 mt-8">Streitschlichtung</h3>
                <p class="mb-6 text-gray-700">
                    <?php echo nl2br(getValue($impressum['disclaimer']['streitschlichtung'])); ?>
                </p>
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
                <p>&copy; 2025 <?php echo getValue($impressum['company_name']); ?><?php echo !empty($impressum['legal_form']) ? ' (' . getValue($impressum['legal_form']) . ')' : ''; ?>. Alle Rechte vorbehalten. | 
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

