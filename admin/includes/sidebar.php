<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="bg-gray-800 text-white w-64 min-h-screen fixed left-0 top-0 pt-16 overflow-y-auto">
    <div class="p-3">
        <div class="flex items-center space-x-2 mb-4 pb-3 border-b border-gray-700">
            <i class="fas fa-tachometer-alt text-lg text-primary"></i>
            <h2 class="text-lg font-bold">Admin Panel</h2>
        </div>
        
        <nav class="space-y-1">
            <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'dashboard.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-home w-4"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="media-library.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'media-library.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-folder-open w-4"></i>
                <span>Media Library</span>
            </a>
            
            <a href="hero.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'hero.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-image w-4"></i>
                <span>Hero Section</span>
            </a>
            
            <a href="about.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'about.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-info-circle w-4"></i>
                <span>About Us</span>
            </a>
            
            <a href="services.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'services.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-tools w-4"></i>
                <span>Shërbimet</span>
            </a>
            
            <a href="catalogs.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'catalogs.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-book w-4"></i>
                <span>Katalogje</span>
            </a>
            
            <a href="projekte.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'projekte.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-briefcase w-4"></i>
                <span>Projekte</span>
            </a>

            <a href="contact.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'contact.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-address-book w-4"></i>
                <span>Kontakt</span>
            </a>
            
            <a href="reviews.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'reviews.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-star w-4"></i>
                <span>Reviews</span>
            </a>
            
            <a href="legal.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'legal.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-gavel w-4"></i>
                <span>Rechtliches</span>
            </a>
            
            <a href="change-password.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg transition-all text-sm <?php echo $currentPage === 'change-password.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-700'; ?>">
                <i class="fas fa-key w-4"></i>
                <span>Ndrysho Fjalëkalimin</span>
            </a>
        </nav>
        
        <div class="mt-4 pt-3 border-t border-gray-700">
            <a href="../index.html" target="_blank" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-gray-300 hover:bg-gray-700 transition-all text-sm">
                <i class="fas fa-external-link-alt w-4"></i>
                <span>Shiko Faqen</span>
            </a>
            
            <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-400 hover:bg-red-900 hover:bg-opacity-20 transition-all mt-1 text-sm">
                <i class="fas fa-sign-out-alt w-4"></i>
                <span>Dil</span>
            </a>
        </div>
    </div>
</div>

