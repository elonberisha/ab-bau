<?php
require_once 'admin/includes/db_connect.php';

// Fetch Legal Data
try {
    $stmt = $pdo->query("SELECT * FROM legal_section LIMIT 1");
    $legal = $stmt->fetch();
} catch (PDOException $e) {
    $legal = [];
}

$privacyContent = $legal['privacy_content'] ?? '<p class="text-center py-10">Përmbajtja nuk është disponueshme.</p>';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta name="description" content="Datenschutz - AB Bau">
    <title>Datenschutz - AB Bau | Bau und Fliesen GmbH</title>
    
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="dist/css/output.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">

    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="site.webmanifest" />
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
                    <li><a href="about.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">Über uns</a></li>
                    <li><a href="services.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">Leistungen</a></li>
                    <li><a href="portfolio.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">Projekte</a></li>
                    <li><a href="catalogs.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">Kataloge</a></li>
                    <li><a href="contact.html" class="nav-link px-4 py-2 text-gray-800 hover:text-primary font-medium text-sm xl:text-base transition-all duration-300 rounded-lg hover:bg-gray-50">Kontakt</a></li>
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
                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black mb-6">Datenschutzerklärung</h1>
                <p class="text-xl sm:text-2xl text-white/90 max-w-3xl mx-auto">Schutz Ihrer persönlichen Daten</p>
            </div>
        </div>
    </section>

    <!-- Content -->
    <section class="py-16 sm:py-20 lg:py-24 bg-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <div class="prose prose-lg max-w-none text-gray-700">
                <?php echo $privacyContent; ?>
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
                            <img src="logo.svg" alt="AB Bau Logo" class="h-10 sm:h-12 w-auto brightness-0 invert transition-transform duration-300 group-hover:scale-105">
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
                        <li><a href="contact.html" class="hover:text-white transition-colors">Talstraße 3d, 85238 Petershausen</a></li>
                        <li><a href="tel:081379957477" class="hover:text-white transition-colors break-all">08137 9957477</a></li>
                        <li><a href="mailto:office@ab-bau.de" class="hover:text-white transition-colors break-all">office@ab-bau.de</a></li>
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
                <p>&copy; 2025 AB Bau - Bau und Fliesen GmbH. Alle Rechte vorbehalten. | 
                <a href="impressum.php" class="hover:text-white transition-colors">Impressum</a> | 
                <a href="datenschutz.php" class="hover:text-white transition-colors">Datenschutz</a> | 
                <a href="agb.php" class="hover:text-white transition-colors">AGB</a></p>
                <p class="mt-2 text-xs text-gray-500">
                    Powered by <a href="https://devycore.com/" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors">Devycore</a>
                </p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/admin-api.js?v=2"></script>
</body>
</html>