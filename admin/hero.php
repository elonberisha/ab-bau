<?php
require_once 'functions.php';
requireLogin();

$message = '';
$messageType = '';
$pageTitle = 'Hero Section';

// Load customization data
$customization = readJson('customization.json');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customization['hero']['image'] = sanitize($_POST['hero_image'] ?? '');
    $customization['hero']['title'] = sanitize($_POST['hero_title'] ?? '');
    $customization['hero']['subtitle'] = sanitize($_POST['hero_subtitle'] ?? '');
    $customization['hero']['button1_text'] = sanitize($_POST['hero_button1_text'] ?? '');
    $customization['hero']['button1_link'] = sanitize($_POST['hero_button1_link'] ?? '');
    $customization['hero']['button2_text'] = sanitize($_POST['hero_button2_text'] ?? '');
    $customization['hero']['button2_link'] = sanitize($_POST['hero_button2_link'] ?? '');
    
    // Stats Bar
    $customization['hero']['stats_bar']['stat1_number'] = sanitize($_POST['stat1_number'] ?? '15+');
    $customization['hero']['stats_bar']['stat1_text'] = sanitize($_POST['stat1_text'] ?? 'Jahre Erfahrung');
    $customization['hero']['stats_bar']['stat2_number'] = sanitize($_POST['stat2_number'] ?? '200+');
    $customization['hero']['stats_bar']['stat2_text'] = sanitize($_POST['stat2_text'] ?? 'Erfolgreiche Projekte');
    $customization['hero']['stats_bar']['stat3_number'] = sanitize($_POST['stat3_number'] ?? '100%');
    $customization['hero']['stats_bar']['stat3_text'] = sanitize($_POST['stat3_text'] ?? 'Zufriedene Kunden');
    
    // Partners (logos) - array inputs
    $partnersInput = $_POST['hero_partners'] ?? [];
    $partners = [];
    if (is_array($partnersInput)) {
        foreach ($partnersInput as $path) {
            $clean = trim($path);
            if ($clean !== '') {
                $partners[] = sanitize($clean);
            }
        }
    }
    // Always set partners array, even if empty (to remove deleted partners)
    $customization['hero']['partners'] = array_values($partners); // array_values to reindex
    
    if (writeJson('customization.json', $customization)) {
        $message = 'Hero section u ruajt me sukses! Ndryshimet reflektohen në index.html';
        $messageType = 'success';
    } else {
        $message = 'Gabim në ruajtje!';
        $messageType = 'error';
    }
    
    // Reload customization after save
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

        <!-- Hero Section Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-bold mb-4 flex items-center">
                <i class="fas fa-image text-primary mr-2"></i>
                Menaxho Hero Section
            </h2>
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="text-primary font-bold mr-1">*</span>Hero Image URL
                    </label>
                    <input type="text" name="hero_image" data-media-picker="image"
                           value="<?php echo htmlspecialchars($customization['hero']['image'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">URL e fotos kryesore të hero section (shfaqet në index.html)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="hero_title" value="<?php echo htmlspecialchars($customization['hero']['title'] ?? ''); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subtitle</label>
                    <textarea name="hero_subtitle" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo htmlspecialchars($customization['hero']['subtitle'] ?? ''); ?></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button 1 Text</label>
                        <input type="text" name="hero_button1_text" value="<?php echo htmlspecialchars($customization['hero']['button1_text'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button 1 Link</label>
                        <input type="text" name="hero_button1_link" value="<?php echo htmlspecialchars($customization['hero']['button1_link'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button 2 Text</label>
                        <input type="text" name="hero_button2_text" value="<?php echo htmlspecialchars($customization['hero']['button2_text'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Button 2 Link</label>
                        <input type="text" name="hero_button2_link" value="<?php echo htmlspecialchars($customization['hero']['button2_link'] ?? ''); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                
                <hr class="my-6">
                <h3 class="text-lg font-bold mb-4">Stats Bar (Pas Hero)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stat 1 Number</label>
                        <input type="text" name="stat1_number" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat1_number'] ?? '15+'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2 mt-2">Stat 1 Text</label>
                        <input type="text" name="stat1_text" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat1_text'] ?? 'Jahre Erfahrung'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stat 2 Number</label>
                        <input type="text" name="stat2_number" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat2_number'] ?? '200+'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2 mt-2">Stat 2 Text</label>
                        <input type="text" name="stat2_text" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat2_text'] ?? 'Erfolgreiche Projekte'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Stat 3 Number</label>
                        <input type="text" name="stat3_number" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat3_number'] ?? '100%'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2 mt-2">Stat 3 Text</label>
                        <input type="text" name="stat3_text" value="<?php echo htmlspecialchars($customization['hero']['stats_bar']['stat3_text'] ?? 'Zufriedene Kunden'); ?>" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <hr class="my-6">
                <h3 class="text-lg font-bold mb-4">Partner Logos</h3>
                <div class="space-y-2" id="partners-list">
                    <?php
                        $partnersExisting = $customization['hero']['partners'] ?? [];
                        if (empty($partnersExisting)) {
                            $partnersExisting = [''];
                        }
                        foreach ($partnersExisting as $idx => $path): ?>
                        <div class="flex items-center space-x-2 partner-row">
                            <input type="text" name="hero_partners[]" data-media-picker="image"
                                   value="<?php echo htmlspecialchars($path); ?>"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg"
                                   placeholder="uploads/partner-logo.png">
                            <button type="button" class="remove-partner bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm" aria-label="Remove">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3">
                    <div class="flex space-x-2">
                        <button type="button" id="add-partner" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 text-sm">
                            <i class="fas fa-plus mr-1"></i>Shto partner
                        </button>
                        <button type="button" id="clear-all-partners" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 text-sm">
                            <i class="fas fa-trash-alt mr-1"></i>Fshi të gjithë partnerët
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        Përdor <a href="media-library.php" target="_blank" class="text-primary underline">Media Library</a> për të kopjuar path-in e logos. Çdo rresht është një partner.
                    </p>
                </div>
                
                <button type="submit" id="save-hero-form" class="bg-gray-800 text-white px-6 py-3 rounded-lg hover:bg-gray-900 font-semibold text-lg shadow-lg hover:shadow-xl transition-all mt-6">
                    <i class="fas fa-save mr-2"></i>Ruaj Hero Section
                </button>
            </form>
        </div>
    </div>

    <script>
        (function() {
            const list = document.getElementById('partners-list');
            const addBtn = document.getElementById('add-partner');
            const clearAllBtn = document.getElementById('clear-all-partners');

            // Function to initialize media picker for an input
            function initMediaPicker(input) {
                // Skip if already has a picker button
                if (input.parentElement && input.parentElement.querySelector('.media-picker-btn')) {
                    return;
                }
                
                // Check if media-picker.js is loaded and has the initialization function
                if (typeof openMediaLibrary === 'function') {
                    // Create wrapper if input is not already in a flex container
                    const wrapper = document.createElement('div');
                    wrapper.className = 'flex items-center space-x-2';
                    
                    // Clone the input
                    const newInput = input.cloneNode(true);
                    newInput.className = (input.className || '') + ' flex-1';
                    
                    // Ensure input has an ID
                    if (!newInput.id) {
                        newInput.id = 'input-' + Math.random().toString(36).substr(2, 9);
                    }
                    
                    // Create picker button
                    const pickerBtn = document.createElement('button');
                    pickerBtn.type = 'button';
                    pickerBtn.className = 'media-picker-btn bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors';
                    pickerBtn.innerHTML = '<i class="fas fa-folder-open mr-2"></i>Browse';
                    pickerBtn.onclick = function() {
                        openMediaLibrary(newInput.id);
                    };
                    
                    // Replace input with wrapper
                    if (input.parentNode) {
                        input.parentNode.replaceChild(wrapper, input);
                        wrapper.appendChild(newInput);
                        wrapper.appendChild(pickerBtn);
                    }
                }
            }

            function addRow(value = '') {
                const row = document.createElement('div');
                row.className = 'flex items-center space-x-2 partner-row mt-2';
                
                // Create input
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'hero_partners[]';
                input.setAttribute('data-media-picker', 'image');
                input.value = value.replace(/"/g, '&quot;');
                input.className = 'flex-1 px-4 py-2 border border-gray-300 rounded-lg';
                input.placeholder = 'uploads/partner-logo.png';
                
                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'remove-partner bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg text-sm';
                removeBtn.setAttribute('aria-label', 'Remove');
                removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
                
                row.appendChild(input);
                row.appendChild(removeBtn);
                list.appendChild(row);
                
                // Initialize media picker for the new input
                setTimeout(() => {
                    initMediaPicker(input);
                }, 100);
            }

            addBtn?.addEventListener('click', () => addRow(''));
            
            // Clear all partners
            clearAllBtn?.addEventListener('click', () => {
                if (confirm('A jeni të sigurt që dëshironi të fshini të gjithë partnerët?')) {
                    // Remove all inputs first
                    const allInputs = list.querySelectorAll('input[name="hero_partners[]"]');
                    allInputs.forEach(input => {
                        input.removeAttribute('name');
                        input.disabled = true;
                        input.remove();
                    });
                    
                    // Remove all rows
                    const allRows = list.querySelectorAll('.partner-row');
                    allRows.forEach(row => row.remove());
                    
                    // Add one empty row
                    addRow('');
                }
            });

            // Function to remove a partner row completely
            function removePartnerRow(removeBtn) {
                // Find the partner-row that contains this button
                let row = removeBtn.closest('.partner-row');
                
                // If not found, try to find it by traversing up the DOM
                if (!row) {
                    let current = removeBtn.parentElement;
                    while (current && current !== list && current !== document.body) {
                        if (current.classList && current.classList.contains('partner-row')) {
                            row = current;
                            break;
                        }
                        current = current.parentElement;
                    }
                }
                
                if (row && list.contains(row)) {
                    // Find and completely remove ALL inputs in this row
                    const inputs = row.querySelectorAll('input[name="hero_partners[]"]');
                    inputs.forEach(input => {
                        input.removeAttribute('name');
                        input.disabled = true;
                        input.remove();
                    });
                    
                    // Remove the entire row
                    row.remove();
                    return true;
                }
                return false;
            }
            
            list?.addEventListener('click', (e) => {
                const removeBtn = e.target.closest('.remove-partner');
                if (removeBtn) {
                    e.preventDefault();
                    e.stopPropagation();
                    removePartnerRow(removeBtn);
                }
            });
            
            // Before form submission, ensure only visible inputs are submitted
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Remove ALL inputs with name="hero_partners[]" from the entire document first
                    const allInputs = Array.from(document.querySelectorAll('input[name="hero_partners[]"]'));
                    allInputs.forEach(input => {
                        input.removeAttribute('name');
                        input.disabled = true;
                        input.remove(); // Completely remove from DOM
                    });
                    
                    // Get all visible partner rows in the list
                    const visibleRows = Array.from(list.querySelectorAll('.partner-row')).filter(row => {
                        return list.contains(row) && row.offsetParent !== null;
                    });
                    
                    // Create new hidden inputs for each visible partner
                    visibleRows.forEach((row, index) => {
                        // Find the input value in the row
                        let inputValue = '';
                        let inputElement = row.querySelector('input[type="text"]');
                        
                        if (!inputElement) {
                            // Try to find input in any wrapper
                            const wrapper = row.querySelector('.flex.items-center.space-x-2:not(.partner-row)');
                            if (wrapper) {
                                inputElement = wrapper.querySelector('input[type="text"]');
                            }
                        }
                        
                        if (inputElement) {
                            inputValue = inputElement.value || '';
                        }
                        
                        // Create a new hidden input with the value
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'hero_partners[]';
                        hiddenInput.value = inputValue.trim();
                        form.appendChild(hiddenInput);
                    });
                    
                    console.log('Submitting with', visibleRows.length, 'partners');
                }, true); // Use capture phase to run before other handlers
            }
        })();
    </script>
</body>
</html>

