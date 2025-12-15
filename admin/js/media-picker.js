// Media Picker Helper
function openMediaLibrary(inputId) {
    // Open media library in a new window
    const width = 1200;
    const height = 800;
    const left = (screen.width - width) / 2;
    const top = (screen.height - height) / 2;
    
    const mediaWindow = window.open(
        'media-library.php?picker=true&target=' + inputId,
        'MediaLibrary',
        `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes`
    );
    
    // Store the target input ID for the callback
    window.mediaPickerTarget = inputId;
}

// Function to receive selected media path from media library
function selectMediaPath(path) {
    if (window.mediaPickerTarget) {
        const input = document.getElementById(window.mediaPickerTarget);
        if (input) {
            input.value = path;
            // Trigger change event
            input.dispatchEvent(new Event('change'));
        }
    }
}

// Add media picker button ONLY to inputs që janë shënuar me data-media-picker="image"
document.addEventListener('DOMContentLoaded', function() {
    const imageInputs = document.querySelectorAll('input[type="text"][data-media-picker="image"]');
    
    imageInputs.forEach(input => {
        // Skip if already has a picker button
        if (input.parentElement.querySelector('.media-picker-btn')) {
            return;
        }
        
        // Create wrapper if input is not already in a flex container
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center space-x-2';
        
        // Clone the input
        const newInput = input.cloneNode(true);
        // Ruaj klasat ekzistuese dhe shto flex styling
        newInput.className = (input.className || '') + ' flex-1';
        
        // Create picker button
        const pickerBtn = document.createElement('button');
        pickerBtn.type = 'button';
        pickerBtn.className = 'media-picker-btn bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors';
        pickerBtn.innerHTML = '<i class="fas fa-folder-open mr-2"></i>Browse';
        pickerBtn.onclick = function() {
            openMediaLibrary(newInput.id || 'input-' + Math.random().toString(36).substr(2, 9));
        };
        
        // Ensure input has an ID
        if (!newInput.id) {
            newInput.id = 'input-' + Math.random().toString(36).substr(2, 9);
        }
        
        // Replace input with wrapper
        input.parentNode.replaceChild(wrapper, input);
        wrapper.appendChild(newInput);
        wrapper.appendChild(pickerBtn);
    });
});

