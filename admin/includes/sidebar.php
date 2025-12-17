<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$currentUser = getCurrentUser(); // Get logged in user info
$username = htmlspecialchars($currentUser['username'] ?? 'Admin');
$role = htmlspecialchars($currentUser['role'] ?? 'Administrator');
$email = htmlspecialchars($currentUser['email'] ?? '');
// Get first letter for avatar
$avatarLetter = strtoupper(substr($username, 0, 1));
?>
<!-- Sidebar -->
<div class="bg-gray-900 text-white w-64 h-screen fixed left-0 top-0 flex flex-col shadow-2xl z-50 transition-all duration-300">
    <!-- Brand -->
    <div class="h-14 flex-shrink-0 flex items-center px-5 bg-gray-800 border-b border-gray-700 shadow-md">
        <i class="fas fa-cube text-lg text-primary mr-3"></i>
        <h2 class="text-lg font-bold tracking-wide">AB Bau Admin</h2>
    </div>
    
    <!-- Navigation (Scrollable) -->
    <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
        <nav class="space-y-1">
            <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Hauptmenü</p>
            
            <a href="dashboard.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'dashboard.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-home w-5 text-center text-sm"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <a href="media-library.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'media-library.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-photo-video w-5 text-center text-sm"></i>
                <span class="font-medium">Medienbibliothek</span>
            </a>

            <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1 mt-4">Inhalte</p>
            
            <a href="hero.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'hero.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-pager w-5 text-center text-sm"></i>
                <span class="font-medium">Startseite (Hero)</span>
            </a>
            
            <a href="about.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'about.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-info-circle w-5 text-center text-sm"></i>
                <span class="font-medium">Über uns</span>
            </a>
            
            <a href="services.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'services.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-tools w-5 text-center text-sm"></i>
                <span class="font-medium">Leistungen</span>
            </a>
            
            <a href="catalogs.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'catalogs.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-book-open w-5 text-center text-sm"></i>
                <span class="font-medium">Kataloge</span>
            </a>
            
            <a href="projekte.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'projekte.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-project-diagram w-5 text-center text-sm"></i>
                <span class="font-medium">Projekte</span>
            </a>

            <a href="contact.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'contact.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-address-card w-5 text-center text-sm"></i>
                <span class="font-medium">Kontakt</span>
            </a>
            
            <a href="reviews.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'reviews.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-star w-5 text-center text-sm"></i>
                <span class="font-medium">Bewertungen</span>
            </a>
            
            <a href="legal.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'legal.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-balance-scale w-5 text-center text-sm"></i>
                <span class="font-medium">Rechtliches (AGB)</span>
            </a>

            <p class="px-3 text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1 mt-4">System</p>
            
            <a href="users.php" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-all duration-200 text-sm <?php echo $currentPage === 'users.php' ? 'bg-primary text-white shadow-md' : 'text-gray-400 hover:bg-gray-800 hover:text-white'; ?>">
                <i class="fas fa-users w-5 text-center text-sm"></i>
                <span class="font-medium">Benutzer</span>
            </a>
        </nav>
    </div>

    <!-- User Profile & Footer (Fixed at bottom) -->
    <div class="border-t border-gray-800 p-3 bg-gray-900/95 flex-shrink-0">
        <div class="flex items-center mb-3 px-1">
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-primary to-blue-400 flex items-center justify-center text-white font-bold text-sm shadow-md shrink-0">
                <?php echo $avatarLetter; ?>
            </div>
            <div class="ml-3 min-w-0">
                <p class="text-sm font-bold text-white truncate leading-tight"><?php echo ucfirst($username); ?></p>
                <p class="text-[10px] text-gray-400 truncate leading-tight"><?php echo $role; ?></p>
            </div>
        </div>
        
        <div class="grid grid-cols-2 gap-2 mb-2">
            <a href="change-password.php" class="flex items-center justify-center space-x-1.5 px-2 py-1.5 rounded bg-gray-800 hover:bg-gray-700 text-gray-300 transition-all text-[11px] font-medium border border-gray-700">
                <i class="fas fa-key text-[10px]"></i>
                <span>Passwort</span>
            </a>
            
            <a href="logout.php" class="flex items-center justify-center space-x-1.5 px-2 py-1.5 rounded bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 transition-all text-[11px] font-medium border border-red-500/20">
                <i class="fas fa-sign-out-alt text-[10px]"></i>
                <span>Abmelden</span>
            </a>
        </div>
        
        <div class="pt-2 border-t border-gray-800 text-center">
            <a href="../index.html" target="_blank" class="text-[10px] text-gray-500 hover:text-primary transition-colors flex items-center justify-center group">
                <span class="group-hover:underline">Website Live</span>
                <i class="fas fa-external-link-alt ml-1.5 text-[9px]"></i>
            </a>
        </div>
    </div>
</div>

<!-- Add padding to main content to account for sidebar -->
<style>
    /* Custom Scrollbar for Sidebar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(75, 85, 99, 0.8);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(107, 114, 128, 1);
    }
</style>
