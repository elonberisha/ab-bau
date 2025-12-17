<?php
require_once 'functions.php';
requireLogin();

$stats = getStats();
$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - AB Bau Admin</title>
    <link rel="stylesheet" href="../dist/css/output.css">
    <link rel="stylesheet" href="../assets/fontawesome/all.min.css">
    <link rel="icon" type="image/x-icon" href="../favicon.ico" />
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="../apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="Ab-Bau-Fliesen" />
    <link rel="manifest" href="../site.webmanifest" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Layout Wrapper -->
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar (Fixed Width) -->
        <div class="w-64 flex-shrink-0">
            <?php include 'includes/sidebar.php'; ?>
        </div>
        
        <!-- Main Content (Flexible) -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10 h-16 flex items-center justify-between px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-bold text-gray-800">Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="../index.html" target="_blank" class="text-sm font-medium text-gray-600 hover:text-primary transition-colors flex items-center bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 hover:border-primary/30">
                        <i class="fas fa-external-link-alt mr-2 text-xs"></i> Website Live
                    </a>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6 md:p-8">
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Stat Card: Projects -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-project-diagram text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Projekte</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['projects']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Services -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-tools text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Leistungen</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['services']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Catalogs -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-book-open text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Kataloge</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['catalogs']; ?></p>
                        </div>
                    </div>

                    <!-- Stat Card: Reviews -->
                    <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-6 border border-gray-100 flex items-center">
                        <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-600 flex items-center justify-center mr-4 shadow-sm">
                            <i class="fas fa-star text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Bewertungen</p>
                            <p class="text-2xl font-bold text-gray-800 mt-1"><?php echo $stats['reviews_pending']; ?> <span class="text-xs font-normal text-gray-400">/ <?php echo $stats['reviews_total']; ?></span></p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Title -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-800">Schnellzugriff</h2>
                </div>

                <!-- Quick Actions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Action: Add Project -->
                    <a href="projekte.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-plus text-6xl text-indigo-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-indigo-50 rounded-lg text-indigo-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-briefcase text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-indigo-600 transition-colors">Portfolio verwalten</h3>
                            <p class="text-sm text-gray-500">Projekte hinzufügen oder bearbeiten.</p>
                        </div>
                    </a>

                    <!-- Action: Reviews -->
                    <a href="reviews.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-orange-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-comments text-6xl text-orange-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-orange-50 rounded-lg text-orange-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-circle text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-orange-600 transition-colors">Kundenbewertungen</h3>
                            <p class="text-sm text-gray-500"><?php echo $stats['reviews_pending']; ?> neue Bewertungen genehmigen.</p>
                        </div>
                    </a>

                    <!-- Action: Hero -->
                    <a href="hero.php" class="group bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md hover:border-pink-100 transition-all relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-image text-6xl text-pink-500"></i>
                        </div>
                        <div class="relative z-10">
                            <div class="w-10 h-10 bg-pink-50 rounded-lg text-pink-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-edit text-lg"></i>
                            </div>
                            <h3 class="font-bold text-gray-800 mb-1 group-hover:text-pink-600 transition-colors">Startseite bearbeiten</h3>
                            <p class="text-sm text-gray-500">Texte und Hauptbild aktualisieren.</p>
                        </div>
                    </a>
                </div>
                
                <!-- System Info Box -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-5 flex items-start space-x-4">
                    <i class="fas fa-database text-blue-500 text-xl mt-1"></i>
                    <div>
                        <h3 class="text-sm font-bold text-blue-800">Systemstatus</h3>
                        <p class="text-sm text-blue-600 mt-1 leading-relaxed">
                            Das Panel ist erfolgreich mit der <strong>MySQL</strong>-Datenbank verbunden. Alle Änderungen werden sicher gespeichert und direkt auf der Website angezeigt.
                        </p>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        // Mobile Sidebar Toggle Logic
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.w-64'); // Select sidebar container
        
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                // Toggle logic depending on implementation (usually class based)
                // For this layout, we might want to slide it in
                alert('Mobile menu toggle to be implemented fully');
            });
        }
    </script>
</body>
</html>
