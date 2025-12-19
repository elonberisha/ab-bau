// Admin API Integration for Dynamic Content

const API_BASE = 'api/';

// Fetch gallery images
async function fetchGallery(type = 'home') {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=gallery`);
        const data = await response.json();
        return data[type] || [];
    } catch (error) {
        console.error('Error fetching gallery:', error);
        return [];
    }
}

// Fetch services
async function fetchServices() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=services`);
        const data = await response.json();
        // Check if response is an error object
        if (data.error) {
            console.error('API Error:', data.error);
            return [];
        }
        // Ensure data is an array
        return Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Error fetching services:', error);
        return [];
    }
}

// Fetch reviews
async function fetchReviews() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=reviews`);
        const data = await response.json();
        // Check if response is an error object
        if (data.error) {
            console.error('API Error:', data.error);
            return [];
        }
        // Ensure data is an array
        return Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Error fetching reviews:', error);
        return [];
    }
}

// Submit review
async function submitReview(name, message, rating) {
    try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('message', message);
        formData.append('rating', rating);

        const response = await fetch(`${API_BASE}submit-review.php`, {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        return result;
    } catch (error) {
        console.error('Error submitting review:', error);
        return { success: false, message: 'Gabim në dërgim të review!' };
    }
}

// Render gallery images
function renderGallery(images, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (images.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Derzeit sind keine Fotos verfügbar.</p>';
        return;
    }

    container.innerHTML = images.map(img => `
        <div class="portfolio-item">
            <img src="${img.path}" alt="${img.title || ''}" class="w-full h-full object-cover">
            ${img.title ? `<div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2">${img.title}</div>` : ''}
        </div>
    `).join('');
}

// Render services
function renderServices(services, containerId, limit) {
    try {
        const container = document.getElementById(containerId);
        if (!container) return;

        const items = Array.isArray(services) ? services : [];
        const list = typeof limit === 'number' ? items.slice(0, limit) : items;

        if (list.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">Derzeit sind keine Leistungen verfügbar.</p>';
            return;
        }

        container.innerHTML = list.map(service => {
            // Check if image exists and is not a placeholder/default that doesn't exist
            const imagePath = service.image || '';
            const hasValidImage = imagePath && 
                                 !imagePath.includes('kiramika.png') && 
                                 !imagePath.includes('mermeri.png') && 
                                 !imagePath.includes('graniti.png') &&
                                 !imagePath.includes('granit.png');
            
            return `
            <div class="group bg-white p-6 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-500">
                <div class="w-full h-36 rounded-lg overflow-hidden mb-4 bg-gray-100">
                    ${hasValidImage ? 
                        `<img src="${imagePath}" alt="${service.title || ''}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy" width="400" height="300" decoding="async" onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-100 flex items-center justify-center\\'><i class=\\'fas fa-image text-gray-400 text-4xl\\'></i></div>';">` :
                        `<div class="w-full h-full bg-gray-100 flex items-center justify-center"><i class="fas fa-image text-gray-400 text-4xl"></i></div>`
                    }
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">${service.title || ''}</h3>
                <p class="text-gray-600">${service.description || ''}</p>
            </div>
        `;
        }).join('');
    } catch (error) {
        console.error('Error in renderServices:', error);
    }
}

// Render reviews
function renderReviews(reviews, containerId) {
    try {
        const container = document.getElementById(containerId);
        if (!container) return;

        if (!reviews || reviews.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">Derzeit sind keine Bewertungen verfügbar.</p>';
            return;
        }

        container.innerHTML = reviews.map(review => `
            <div class="bg-white p-6 rounded-xl shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-bold text-lg">${review.name || 'Anonym'}</h4>
                    <div class="flex items-center">
                        ${Array.from({ length: 5 }, (_, i) =>
            `<i class="fas fa-star ${i < (review.rating || 0) ? 'text-yellow-400' : 'text-gray-300'}"></i>`
        ).join('')}
                    </div>
                </div>
                <p class="text-gray-700">${review.message || ''}</p>
                <p class="text-sm text-gray-500 mt-2">${review.date || ''}</p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error in renderReviews:', error);
    }
}

// Fetch catalogs
// Fetch catalogs
async function fetchCatalogs() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=catalogs`);
        const data = await response.json();
        // Check if response is an error object
        if (data.error) {
            console.error('API Error:', data.error);
            return [];
        }
        // Ensure data is an array
        return Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Error fetching catalogs:', error);
        return [];
    }
}

// Fetch portfolio (projekte)
async function fetchPortfolio() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=portfolio`);
        const data = await response.json();
        // Check if response is an error object
        if (data.error) {
            console.error('API Error:', data.error);
            return [];
        }
        // Ensure data is an array
        return Array.isArray(data) ? data : [];
    } catch (error) {
        console.error('Error fetching portfolio:', error);
        return [];
    }
}


// Render catalogs as buttons
function renderCatalogs(catalogs, containerId, limit) {
    const container = document.getElementById(containerId);
    const emptyContainer = document.getElementById('catalogs-empty');
    if (!container) return;

    const items = Array.isArray(catalogs) ? catalogs : [];
    const list = typeof limit === 'number' ? items.slice(0, limit) : items;

    if (list.length === 0) {
        container.classList.add('hidden');
        if (emptyContainer) emptyContainer.classList.remove('hidden');
        return;
    }

    container.classList.remove('hidden');
    if (emptyContainer) emptyContainer.classList.add('hidden');

    container.innerHTML = list.map((catalog, index) => `
        <a href="catalog-detail.html?id=${catalog.id}" 
           class="group relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden block border-2 border-transparent hover:border-primary"
           onclick="console.log('Navigating to catalog ${catalog.id}')">
            <div class="relative h-48 sm:h-56 overflow-hidden">
                ${catalog.cover_image ? `
                    <img src="${cleanPath(catalog.cover_image)}" 
                         alt="${catalog.title}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                ` : `
                    <div class="w-full h-full bg-gradient-to-br from-primary/20 to-primary-dark/20 flex items-center justify-center">
                        <i class="fas fa-book text-6xl text-primary/50"></i>
                    </div>
                `}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <h3 class="text-2xl sm:text-3xl font-bold mb-2">${catalog.title || 'Katalog'}</h3>
                    ${catalog.description ? `<p class="text-white/90 text-sm sm:text-base line-clamp-2">${catalog.description}</p>` : ''}
                </div>
                ${catalog.category ? `
                    <div class="absolute top-4 right-4">
                        <span class="bg-primary text-white px-3 py-1 rounded-full text-xs font-semibold shadow-lg">
                            ${catalog.category}
                        </span>
                    </div>
                ` : ''}
            </div>
            <div class="p-6 bg-white">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-box mr-2"></i>
                        ${catalog.products ? catalog.products.length : 0} Produkte
                    </span>
                    <span class="text-primary font-semibold group-hover:translate-x-2 transition-transform inline-flex items-center">
                        Mehr anzeigen
                        <i class="fas fa-arrow-right ml-2"></i>
                    </span>
                </div>
            </div>
        </a>
    `).join('');
}

// Lightbox function (global)
window.openImageLightbox = function(imageSrc, imageAlt) {
    if (!imageSrc || imageSrc.trim() === '') return;
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    if (lightbox && lightboxImage) {
        lightboxImage.src = imageSrc;
        lightboxImage.alt = imageAlt || 'Produkt Bild';
        lightbox.classList.remove('hidden');
        lightbox.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
};

window.closeImageLightbox = function() {
    const lightbox = document.getElementById('imageLightbox');
    if (lightbox) {
        lightbox.classList.add('hidden');
        lightbox.classList.remove('flex');
        document.body.style.overflow = '';
    }
};

// Render catalog products
function renderCatalogProducts(products, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    if (!products || products.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600 text-lg">Aktuell sind keine Produkte in diesem Katalog verfügbar.</p>
            </div>
        `;
        return;
    }

    // Debug log
    console.log('Rendering catalog products:', products);

    container.innerHTML = products.map((product, index) => `
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group" data-aos="fade-up" data-aos-delay="${index * 50}">
            <div class="relative h-64 overflow-hidden cursor-pointer" onclick="openImageLightbox('${product.image ? cleanPath(product.image) : ''}', '${(product.name || 'Produkt').replace(/'/g, "\\'")}')">
                ${product.image ? `
                    <img src="${cleanPath(product.image)}" 
                         alt="${product.name || 'Produkt'}" 
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                        <i class="fas fa-expand text-white opacity-0 group-hover:opacity-100 transition-opacity text-2xl"></i>
                    </div>
                ` : `
                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <i class="fas fa-image text-5xl text-gray-400"></i>
                    </div>
                `}
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-2">${product.name || 'Produkt'}</h3>
                ${product.price ? `
                    <div class="text-lg font-bold text-primary mb-2">${product.price}</div>
                ` : ''}
                ${product.specifications ? `
                    <div class="text-sm text-gray-500 mb-2 font-medium bg-gray-100 px-2 py-1 rounded inline-block">
                        <i class="fas fa-ruler-combined mr-1"></i> ${product.specifications}
                    </div>
                ` : ''}
                ${product.description ? `
                    <p class="text-gray-600 mt-2 text-sm line-clamp-3">${product.description}</p>
                ` : ''}
            </div>
        </div>
    `).join('');
}

// Render portfolio (projekte)
function renderPortfolio(items, containerId, limit) {
    try {
        const container = document.getElementById(containerId);
        if (!container) return;

        const list = Array.isArray(items) ? items : [];
        const finalList = typeof limit === 'number' ? list.slice(0, limit) : list;

        if (!finalList || finalList.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">Derzeit sind keine Projekte verfügbar.</p>';
            return;
        }

        container.innerHTML = finalList.map((item, idx) => `
        <div class="group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100" data-aos="fade-up" data-aos-delay="${idx * 100}">
            <div class="h-64 overflow-hidden cursor-pointer" onclick="openImageLightbox('${cleanPath(item.path)}', '${(item.title || 'Projekt').replace(/'/g, "\\'")}')">
                <img src="${cleanPath(item.path)}" alt="${item.title || ''}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" loading="lazy"
                     onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-100 flex items-center justify-center\\'><i class=\\'fas fa-image text-gray-400 text-4xl\\'></i></div>';">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors flex items-center justify-center">
                    <i class="fas fa-expand text-white opacity-0 group-hover:opacity-100 transition-opacity text-2xl"></i>
                </div>
            </div>
            <div class="p-5 bg-white">
                <h3 class="text-xl font-bold text-gray-900 mb-2">${item.title || 'Projekt'}</h3>
                ${item.description ? `<p class="text-gray-600 mb-3">${item.description}</p>` : ''}
                <div class="flex items-center text-sm text-gray-500 space-x-3">
                    <span><i class="fas fa-tag mr-1 text-primary"></i>${item.type === 'portfolio' ? 'Projekt' : 'Foto'}</span>
                    ${item.date ? `<span><i class="fas fa-calendar mr-1 text-primary"></i>${item.date}</span>` : ''}
                </div>
            </div>
        </div>
    `).join('');
    } catch (error) {
        console.error('Error in renderPortfolio:', error);
    }
}

// Render blog (using projects as posts)
function renderBlog(items, containerId, limit) {
    try {
        const container = document.getElementById(containerId);
        if (!container) return;
        const list = Array.isArray(items) ? items : [];
        const finalList = typeof limit === 'number' ? list.slice(0, limit) : list;

        if (finalList.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-8">Aktuell keine Beiträge vorhanden.</p>';
            return;
        }

        container.innerHTML = finalList.map((item, idx) => `
        <div class="bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100" data-aos="fade-up" data-aos-delay="${idx * 100}">
            <div class="aspect-video relative overflow-hidden h-40 sm:h-48 lg:h-56">
                <img src="${item.path}" alt="${item.title || ''}" loading="lazy"
                    class="w-full h-full object-cover hover:scale-110 transition-transform duration-500"
                    onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-100 flex items-center justify-center\\'><i class=\\'fas fa-image text-gray-400 text-4xl\\'></i></div>';">
            </div>
            <div class="p-4 sm:p-5 lg:p-6">
                <span class="inline-block px-2 sm:px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold mb-2 sm:mb-3">${item.type || 'Projekt'}</span>
                <h3 class="text-base sm:text-lg lg:text-xl font-bold text-gray-900 mb-2 sm:mb-3 leading-tight">
                    ${item.title || 'Projekt'}
                </h3>
                ${item.description ? `<p class="text-sm sm:text-base text-gray-600 mb-3">${item.description}</p>` : ''}
                <div class="flex items-center text-gray-600 text-xs sm:text-sm">
                    ${item.date ? `<><i class="fas fa-calendar mr-1 sm:mr-2 text-xs"></i><span>${item.date}</span></>` : ''}
                </div>
            </div>
        </div>
    `).join('');
    } catch (error) {
        console.error('Error in renderBlog:', error);
    }
}

// Fetch and render catalogs
async function fetchAndRenderCatalogs() {
    const catalogs = await fetchCatalogs();
    renderCatalogs(catalogs, 'catalogs-container');
}

// View catalog details (modal or expand)
function viewCatalog(catalogId) {
    // This can be expanded to show a modal with catalog details
    console.log('View catalog:', catalogId);
}

// Fetch customization data
async function fetchCustomization() {
    try {
        const response = await fetch(`${API_BASE}get-data.php?type=customization`);
        const data = await response.json();
        return data || {};
    } catch (error) {
        console.error('Error fetching customization:', error);
        return {};
    }
}

// Helper function to clean image paths
function cleanPath(val) {
    if (!val || typeof val !== 'string') return val;
    return val.trim().split(/\s+/)[0]; // use only first path, remove spaces
}

// Helper to set image src and show it
function setImg(id, src) {
    const el = document.getElementById(id);
    if (el && src) {
        el.src = cleanPath(src);
        el.classList.remove('hidden');
        el.style.display = '';
    }
}

// Render customization data
function renderCustomization(data) {
    if (!data || typeof data !== 'object') {
        console.warn('renderCustomization: Invalid data provided');
        return;
    }
    
    try {
        const isAboutPage = !!document.getElementById('about-hero-bg');

    // Hero Section (only on index.html)
    if (data.hero) {
        if (data.hero.mini_text) setText('hero-mini-text', data.hero.mini_text);
        if (data.hero.title) setHTMLSafe('hero-title', data.hero.title);
        if (data.hero.subtitle) setText('hero-subtitle', data.hero.subtitle);
        if (data.hero.button1_text) setText('hero-btn1-text', data.hero.button1_text);
        if (data.hero.button1_link) {
            const btn1 = document.getElementById('hero-btn1-link');
            if (btn1) btn1.href = data.hero.button1_link;
        }
        if (data.hero.button2_text) setText('hero-btn2-text', data.hero.button2_text);
        if (data.hero.button2_link) {
            const btn2 = document.getElementById('hero-btn2-link');
            if (btn2) btn2.href = data.hero.button2_link;
        }
        if (data.hero.image) {
            setImg('hero-bg', data.hero.image);
        }
        // Stats bar
        if (data.hero.stats_bar) {
            const s = data.hero.stats_bar;
            if (s.stat1_number) setText('hero-stat1-number', s.stat1_number);
            if (s.stat1_text) setText('hero-stat1-text', s.stat1_text);
            if (s.stat2_number) setText('hero-stat2-number', s.stat2_number);
            if (s.stat2_text) setText('hero-stat2-text', s.stat2_text);
            if (s.stat3_number) setText('hero-stat3-number', s.stat3_number);
            if (s.stat3_text) setText('hero-stat3-text', s.stat3_text);
        }
        // Partners logos
        if (Array.isArray(data.hero.partners)) {
            const partnersEl = document.getElementById('partners-list');
            if (partnersEl) {
                if (data.hero.partners.length === 0) {
                    partnersEl.innerHTML = '<p class="text-gray-500 text-center py-6">Derzeit sind keine Partner verfügbar.</p>';
                } else {
                    const logos = data.hero.partners.map((logo) => `
                        <div class="flex items-center justify-center">
                            <img src="${cleanPath(logo)}" alt="Partner" class="h-20 sm:h-24 w-auto object-contain"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.style.display='none'; this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gray-100 flex items-center justify-center\\'><i class=\\'fas fa-building text-gray-400 text-2xl\\'></i></div>';">
                        </div>
                    `).join('');
                    partnersEl.className = 'grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 sm:gap-3 items-center';
                    partnersEl.innerHTML = logos;
                }
            }
        }
    }

    // About Section
    if (data.about) {
        const aboutData = data.about;
        const pageContent = aboutData.full_content || {};

        const usePage = isAboutPage;
        const titleVal = usePage ? (pageContent.title || aboutData.title) : aboutData.title;
        const desc1Val = usePage ? (pageContent.description1 || aboutData.description1) : aboutData.description1;
        const desc2Val = usePage ? (pageContent.description2 || aboutData.description2) : aboutData.description2;
        const desc3Val = usePage ? (pageContent.description3 || '') : '';

        if (titleVal) setHTMLSafe('about-title', titleVal);
        if (desc1Val) setText('about-desc1', desc1Val);
        if (desc2Val) setText('about-desc2', desc2Val);
        if (desc3Val) setText('about-desc3', desc3Val);

        if (data.about.shop_title) setText('about-shop-title', data.about.shop_title);
        if (data.about.shop_text) setText('about-shop-text', data.about.shop_text);

        if (data.about.processing_title) setText('about-proc-title', data.about.processing_title);
        if (data.about.processing_text) setText('about-proc-text', data.about.processing_text);

        // Images if IDs exist
        if (data.about.image1) setImg('about-img1', data.about.image1);
        if (data.about.image2) setImg('about-img2', data.about.image2);
        if (data.about.image3) setImg('about-img3', data.about.image3);

        // About cards
        setText('about-card1-title', data.about.card1_title);
        setText('about-card1-text', data.about.card1_text);
        setText('about-card2-title', data.about.card2_title);
        setText('about-card2-text', data.about.card2_text);
        setText('about-card3-title', data.about.card3_title);
        setText('about-card3-text', data.about.card3_text);

        // About stats/cards if present
        if (data.about.stats) {
            setText('about-stat1-number', data.about.stats.stat1_number);
            setText('about-stat1-text', data.about.stats.stat1_text);
            setText('about-stat2-number', data.about.stats.stat2_number);
            setText('about-stat2-text', data.about.stats.stat2_text);
            setText('about-stat3-number', data.about.stats.stat3_number);
            setText('about-stat3-text', data.about.stats.stat3_text);
        }
    }

    // Contact Section
    if (data.contact) {
        setText('contact-title', data.contact.section_title);
        setText('contact-subtitle', data.contact.section_subtitle || data.contact.section_description);

        // address text + link
        const addressEl = document.getElementById('contact-address');
        if (addressEl) {
            const addressLine1 = data.contact.address_line1 || '';
            const addressLine2 = data.contact.address_line2 || '';
            const addressText = addressLine1 + (addressLine1 && addressLine2 ? '<br>' : '') + addressLine2;
            addressEl.innerHTML = addressText || '';
            const link = data.contact.address_map_link || data.contact.address_link;
            if (link) {
                addressEl.setAttribute('href', link);
            } else {
                addressEl.removeAttribute('href');
            }
        }

        // phones - ensure they are links
        const phone1 = document.getElementById('contact-phone1');
        if (phone1 && data.contact.phone1) {
            let phone1Text = data.contact.phone1;
            let phone1Link = phone1Text;
            // If it's a tel: link, extract the number for display
            if (phone1Text.startsWith('tel:')) {
                phone1Link = phone1Text;
                phone1Text = phone1Text.replace('tel:', '').replace(/(\d{2})(\d{3})(\d{4})(\d+)/, '$1 $2 $3$4').trim();
            } else {
                // If it's not a link, create tel: link
                phone1Link = `tel:${phone1Text.replace(/\s+/g, '').replace(/[^\d+]/g, '')}`;
            }
            phone1.textContent = phone1Text;
            phone1.setAttribute('href', phone1Link);
        }
        const phone2 = document.getElementById('contact-phone2');
        if (phone2 && data.contact.phone2) {
            let phone2Text = data.contact.phone2;
            let phone2Link = phone2Text;
            // If it's a tel: link, extract the number for display
            if (phone2Text.startsWith('tel:')) {
                phone2Link = phone2Text;
                phone2Text = phone2Text.replace('tel:', '').replace(/(\d{2})(\d{3})(\d{4})(\d+)/, '$1 $2 $3$4').trim();
            } else {
                // If it's not a link, create tel: link
                phone2Link = `tel:${phone2Text.replace(/\s+/g, '').replace(/[^\d+]/g, '')}`;
            }
            phone2.textContent = phone2Text;
            phone2.setAttribute('href', phone2Link);
        }

        // email
        const emailEl = document.getElementById('contact-email');
        if (emailEl && data.contact.email) {
            emailEl.textContent = data.contact.email;
            emailEl.setAttribute('href', `mailto:${data.contact.email}`);
        }

        // social links - use new field names from admin
        setLinkIfExists('contact-facebook', data.contact.facebook_link);
        setLinkIfExists('contact-instagram', data.contact.instagram_link);
        setLinkIfExists('contact-linkedin', data.contact.linkedin_link);
        setLinkIfExists('contact-whatsapp', data.contact.whatsapp_link || (data.contact.whatsapp_number ? `https://wa.me/${data.contact.whatsapp_number.replace(/\s+/g, '').replace(/[^\d+]/g, '')}` : ''));

        // form
        setText('contact-form-title', data.contact.form_title);
        const formBtn = document.getElementById('contact-form-button');
        if (formBtn && data.contact.form_button) {
            formBtn.textContent = data.contact.form_button;
        }
        
        // Service options dropdown
        const serviceSelect = document.getElementById('service');
        if (serviceSelect) {
            // Debug logging
            console.log('Service select element found:', serviceSelect);
            console.log('Service options data:', data.contact.service_options);
            
            // Check if service_options exists and is valid
            let serviceOptions = [];
            if (data.contact.service_options) {
                // If it's a string, try to parse it
                if (typeof data.contact.service_options === 'string') {
                    try {
                        serviceOptions = JSON.parse(data.contact.service_options);
                    } catch (e) {
                        console.error('Error parsing service_options JSON:', e);
                        serviceOptions = [];
                    }
                } else if (Array.isArray(data.contact.service_options)) {
                    serviceOptions = data.contact.service_options;
                }
            }
            
            console.log('Parsed service options:', serviceOptions);
            
            if (Array.isArray(serviceOptions) && serviceOptions.length > 0) {
                // Clear existing options
                serviceSelect.innerHTML = '';
                
                // Add placeholder option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Bitte wählen Sie eine Leistung';
                serviceSelect.appendChild(defaultOption);
                
                // Add dynamic options
                serviceOptions.forEach(option => {
                    if (option && option.value && option.label) {
                        const optionEl = document.createElement('option');
                        optionEl.value = option.value;
                        optionEl.textContent = option.label;
                        serviceSelect.appendChild(optionEl);
                    }
                });
                
                console.log('Service options rendered successfully');
            } else {
                console.warn('No valid service options found or empty array');
            }
        } else {
            console.warn('Service select element not found');
        }

        // Projektleitung
        if (data.contact) {
            if (data.contact.project_manager_title) setText('contact-project-manager-title', data.contact.project_manager_title);
            if (data.contact.project_manager_name) {
                const nameEl = document.getElementById('contact-project-manager-name');
                if (nameEl) nameEl.textContent = data.contact.project_manager_name;
            }
            if (data.contact.project_manager_description) setText('contact-project-manager-description', data.contact.project_manager_description);
        }

        // Öffnungszeiten
        setText('contact-opening-hours-title', data.contact.opening_hours_title);
        setText('contact-opening-hours-monday-friday', data.contact.opening_hours_monday_friday);
        setText('contact-opening-hours-saturday', data.contact.opening_hours_saturday);
        setText('contact-opening-hours-sunday', data.contact.opening_hours_sunday);

        // Footer bindings (shared footer across pages)
        const footerAddressText = (data.contact.address_line1 || '') + (data.contact.address_line1 && data.contact.address_line2 ? ' ' : '') + (data.contact.address_line2 || '');
        setLinkAndText('footer-address', data.contact.address_map_link || '', footerAddressText);
        let footerPhone1 = data.contact.phone1 || '';
        let footerPhone1Text = footerPhone1;
        if (footerPhone1.startsWith('tel:')) {
            footerPhone1Text = footerPhone1.replace('tel:', '').replace(/(\d{2})(\d{3})(\d{4})(\d+)/, '$1 $2 $3$4').trim();
        } else if (footerPhone1) {
            footerPhone1 = `tel:${footerPhone1.replace(/\s+/g, '').replace(/[^\d+]/g, '')}`;
        }
        setLinkAndText('footer-phone', footerPhone1, footerPhone1Text);
        setLinkAndText('footer-email', data.contact.email ? `mailto:${data.contact.email}` : '', data.contact.email);
        setLinkIfExists('footer-facebook', data.contact.facebook_link);
        setLinkIfExists('footer-instagram', data.contact.instagram_link);
        setLinkIfExists('footer-linkedin', data.contact.linkedin_link);

        // Map Embed
        const mapContainer = document.querySelector('#contact-map-container iframe');
        // If container exists and we have code
        if (data.contact.map_embed_code) {
             const mapSection = document.getElementById('contact-map-container');
             if (mapSection) {
                 mapSection.innerHTML = data.contact.map_embed_code;
                 // Add styles to iframe if needed to ensure it fits
                 const iframe = mapSection.querySelector('iframe');
                 if (iframe) {
                     iframe.style.width = '100%';
                     iframe.style.height = '100%';
                     iframe.style.border = '0';
                 }
             }
        }
    }
    } catch (error) {
        console.error('Error in renderCustomization:', error);
        // Don't break the page if there's an error
    }
}

// Helpers
function setText(id, value) {
    if (!value) return;
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function setHTMLSafe(id, value) {
    if (!value) return;
    const el = document.getElementById(id);
    if (el) el.innerHTML = value;
}

function setLinkIfExists(id, href) {
    if (!href) return;
    const el = document.getElementById(id);
    if (el) el.setAttribute('href', href);
}

function setLinkAndText(id, href, text) {
    const el = document.getElementById(id);
    if (!el) return;
    if (text) el.textContent = text;
    if (href) el.setAttribute('href', href);
}

// Initialize dynamic content
async function initDynamicContent() {
    try {
        const [customization, services, catalogs, portfolio] = await Promise.all([
            fetchCustomization().catch(err => {
                console.error('Error fetching customization:', err);
                return {};
            }),
            fetchServices().catch(err => {
                console.error('Error fetching services:', err);
                return [];
            }),
            fetchCatalogs().catch(err => {
                console.error('Error fetching catalogs:', err);
                return [];
            }),
            fetchPortfolio().catch(err => {
                console.error('Error fetching portfolio:', err);
                return [];
            })
        ]);

        if (customization && Object.keys(customization).length > 0) {
            renderCustomization(customization);
        }

    // Services section (index only - services.html doesn't have titles anymore)
    if (customization?.services) {
        const isServicesPage = !!document.getElementById('services-hero-bg');
        
        if (!isServicesPage) {
            // For index.html - use index values
            setText('services-subtitle', customization.services.section_subtitle);
            setText('services-title-line1', customization.services.section_title_line1);
            setText('services-title-line2', customization.services.section_title_line2);
            setText('services-description', customization.services.section_description);
        }
    }
    const indexServices = (customization?.services?.show_in_index === false) ? [] : services;
    const indexLimit = customization?.services?.max_cards_index;
    renderServices(indexServices, 'services-list', indexLimit);       // index
    renderServices(services, 'services-page-list');                   // services page

    // Catalogs section
    if (customization?.catalogs) {
        setText('catalogs-label', customization.catalogs.index_title);
        setText('catalogs-title', customization.catalogs.index_title);
        setText('catalogs-description', customization.catalogs.index_description);
        setText('catalogs-hero-title', customization.catalogs.full_title);
        setText('catalogs-hero-description', customization.catalogs.full_description);
    }
    const catalogsSection = document.getElementById('catalogs-preview');
    const showCatalogs = customization?.catalogs?.show_in_index !== false;
    if (catalogsSection) {
        catalogsSection.style.display = showCatalogs ? '' : 'none';
    }
    // full catalogs page
    renderCatalogs(catalogs, 'catalogs-container');
    // index preview with limit
    const catalogsLimit = customization?.catalogs?.max_catalogs_index || 3;
    renderCatalogs(showCatalogs ? catalogs : [], 'catalogs-preview-grid', catalogsLimit);

    // Projekte / Portfolio section
    if (customization?.portfolio) {
        setText('projects-title', customization.portfolio.index_title);
        setText('projects-description', customization.portfolio.index_description);
        setText('portfolio-hero-title', customization.portfolio.full_title);
        setText('portfolio-hero-description', customization.portfolio.full_description);
    }
    const projectsSection = document.getElementById('projects');
    const showProjects = customization?.portfolio?.show_in_index !== false;
    if (projectsSection) {
        if (showProjects) {
            projectsSection.classList.remove('hidden');
            projectsSection.style.display = '';
        } else {
            projectsSection.classList.add('hidden');
            projectsSection.style.display = 'none';
        }
    }
    const portfolioLimit = customization?.portfolio?.max_items_index || portfolio?.length;
    renderPortfolio(showProjects ? portfolio : [], 'projects-container', portfolioLimit);       // (removed section in index, safe no-op)
    renderPortfolio(portfolio, 'portfolio-container');      // portfolio page

    // About hero
    if (customization?.about) {
        const aboutData = customization.about;
        const pageContent = aboutData.full_content || {};
        const aboutHeroEl = document.getElementById('about-hero-bg');
        const isAboutPage = !!aboutHeroEl;
        if (aboutHeroEl) {
            const heroSrc = aboutData.page_hero_image || aboutData.hero_image;
            if (heroSrc) setImg('about-hero-bg', heroSrc);
        }
        if (isAboutPage) {
            // Hero section
            setText('about-hero-title', aboutData.page_hero_title || pageContent.title || aboutData.title || 'Über uns');
            setText('about-hero-subtitle', aboutData.page_hero_subtitle || pageContent.description1 || aboutData.description1 || '');
            // Main content
            setText('about-title', pageContent.title || aboutData.title || '');
            setText('about-desc1', pageContent.description1 || aboutData.description1 || '');
            setText('about-desc2', pageContent.description2 || aboutData.description2 || '');
            setText('about-desc3', pageContent.description3 || '');
            // Images
            if (aboutData.image1) {
                const img1 = document.getElementById('about-img1');
                if (img1) img1.src = cleanPath(aboutData.image1);
            }
            if (aboutData.image2) {
                const img2 = document.getElementById('about-img2');
                if (img2) img2.src = cleanPath(aboutData.image2);
            }
            if (aboutData.image3) {
                const img3 = document.getElementById('about-img3');
                if (img3) img3.src = cleanPath(aboutData.image3);
            }
            // Cards
            setText('about-card1-title', aboutData.card1_title || '');
            setText('about-card1-text', aboutData.card1_text || '');
            setText('about-card2-title', aboutData.card2_title || '');
            setText('about-card2-text', aboutData.card2_text || '');
            setText('about-card3-title', aboutData.card3_title || '');
            setText('about-card3-text', aboutData.card3_text || '');
            // Stats
            if (aboutData.stats) {
                setText('about-stat1-number', aboutData.stats.stat1_number || '');
                setText('about-stat1-text', aboutData.stats.stat1_text || '');
                setText('about-stat2-number', aboutData.stats.stat2_number || '');
                setText('about-stat2-text', aboutData.stats.stat2_text || '');
                setText('about-stat3-number', aboutData.stats.stat3_number || '');
                setText('about-stat3-text', aboutData.stats.stat3_text || '');
            }
            // Story section
            setText('about-story-title', aboutData.story_title || '');
            setText('about-story-p1', aboutData.story_paragraph1 || '');
            setText('about-story-p2', aboutData.story_paragraph2 || '');
            setText('about-story-p3', aboutData.story_paragraph3 || '');
        }
    }

    // Services hero (full page)
    if (customization?.services?.hero_image) {
        setImg('services-hero-bg', customization.services.hero_image);
    }
    setText('services-hero-title', customization?.services?.full_title);
    setText('services-hero-subtitle', customization?.services?.full_description);
    
    // Additional Services section (services.html only)
    if (document.getElementById('services-additional-title')) {
        if (customization?.services?.additional_title) {
            setText('services-additional-title', customization.services.additional_title);
            document.getElementById('additional-services-section').style.display = 'block';
        } else {
            document.getElementById('additional-services-section').style.display = 'none';
        }
    }
    if (customization?.services?.additional_cards && document.getElementById('services-additional-cards')) {
        const cardsContainer = document.getElementById('services-additional-cards');
        cardsContainer.innerHTML = '';
        customization.services.additional_cards.forEach((card, index) => {
            if (card.title && card.text) {
                const cardDiv = document.createElement('div');
                cardDiv.className = 'bg-white p-6 rounded-2xl shadow-md border border-gray-100';
                cardDiv.setAttribute('data-aos', 'fade-up');
                cardDiv.setAttribute('data-aos-delay', (index + 1) * 100);
                cardDiv.innerHTML = `
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas ${card.icon || 'fa-check'} text-primary text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">${card.title}</h3>
                    <p class="text-gray-600 text-sm">${card.text}</p>
                `;
                cardsContainer.appendChild(cardDiv);
            }
        });
    }
    
    // Why Choose Us section (services.html only)
    if (document.getElementById('services-why-title')) {
        if (customization?.services?.why_title) {
            setText('services-why-title', customization.services.why_title);
            setText('services-why-description', customization.services.why_description || '');
            document.getElementById('services-why-choose-section').style.display = 'block';
        } else {
            document.getElementById('services-why-choose-section').style.display = 'none';
        }
    }
    if (customization?.services?.why_cards && document.getElementById('services-why-cards')) {
        const whyContainer = document.getElementById('services-why-cards');
        whyContainer.innerHTML = '';
        customization.services.why_cards.forEach((card, index) => {
            if (card.title && card.text) {
                const cardDiv = document.createElement('div');
                cardDiv.className = 'bg-white p-8 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow';
                cardDiv.setAttribute('data-aos', 'fade-up');
                cardDiv.setAttribute('data-aos-delay', (index + 1) * 100);
                cardDiv.innerHTML = `
                    <div class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-4">
                        <i class="fas ${card.icon || 'fa-check-circle'} text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">${card.title}</h3>
                    <p class="text-gray-600">${card.text}</p>
                `;
                whyContainer.appendChild(cardDiv);
            }
        });
    }
    
    // Process section (services.html only)
    if (document.getElementById('services-process-title')) {
        if (customization?.services?.process_title) {
            setText('services-process-title', customization.services.process_title);
            setText('services-process-description', customization.services.process_description || '');
            document.getElementById('services-process-section').style.display = 'block';
        } else {
            document.getElementById('services-process-section').style.display = 'none';
        }
    }
    if (customization?.services?.process_steps && document.getElementById('services-process-steps')) {
        const processContainer = document.getElementById('services-process-steps');
        processContainer.innerHTML = '';
        customization.services.process_steps.forEach((step, index) => {
            if (step.title && step.text) {
                const stepDiv = document.createElement('div');
                stepDiv.className = 'bg-white p-6 rounded-2xl shadow-md border border-gray-100 text-center';
                stepDiv.setAttribute('data-aos', 'fade-up');
                stepDiv.setAttribute('data-aos-delay', (index + 1) * 100);
                stepDiv.innerHTML = `
                    <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">
                        ${step.number || String(index + 1).padStart(2, '0')}
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-3">${step.title}</h3>
                    <p class="text-gray-600 text-sm">${step.text}</p>
                `;
                processContainer.appendChild(stepDiv);
            }
        });
    }

    // Catalogs hero image
    if (customization?.catalogs?.hero_image) {
        setImg('catalogs-hero-bg', customization.catalogs.hero_image);
    }

    // Portfolio hero image
    if (customization?.portfolio?.hero_image) {
        setImg('portfolio-hero-bg', customization.portfolio.hero_image);
    }

    // Contact hero + info
    if (customization?.contact) {
        setText('contact-hero-title', customization.contact.section_title);
        setText('contact-hero-subtitle', customization.contact.section_subtitle || customization.contact.section_description);
        if (customization.contact.hero_image) {
            setImg('contact-hero-bg', customization.contact.hero_image);
        }
    }

    // Blog (use projects as posts) limited to 3
    if (customization?.portfolio) {
        setText('blog-title', customization.portfolio.index_title || 'Unser Baujournal');
        setText('blog-description', customization.portfolio.index_description || '');
    }
    renderBlog(portfolio, 'blog-list', 3);
    } catch (error) {
        console.error('Error initializing dynamic content:', error);
        // Don't break the page if there's an error
    }
}

// Export functions for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        fetchGallery,
        fetchServices,
        fetchReviews,
        fetchCatalogs,
        fetchPortfolio,
        submitReview,
        renderGallery,
        renderServices,
        renderReviews,
        renderCatalogs,
        renderPortfolio,
        fetchAndRenderCatalogs,
        fetchCustomization,
        renderCustomization,
        initDynamicContent
    };
}

