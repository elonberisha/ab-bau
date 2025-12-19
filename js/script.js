// Initialize AOS (Animate On Scroll) - Only if AOS is loaded
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        offset: 100,
        delay: 0
    });
}

// Portfolio grid animations (no Swiper needed)

// Mobile Menu Toggle
const hamburger = document.getElementById('hamburger');
const mobileMenu = document.getElementById('mobileMenu');

if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        if (mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.remove('hidden');
            mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
        } else {
            mobileMenu.style.maxHeight = '0';
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);
        }
    });

    // Close mobile menu when clicking on a link
    const mobileLinks = mobileMenu.querySelectorAll('a');
    mobileLinks.forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('active');
            mobileMenu.style.maxHeight = '0';
            setTimeout(() => {
                mobileMenu.classList.add('hidden');
            }, 300);
        });
    });
}

// Active Navigation Link Highlighting (only for index.html with anchor links)
window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Only run if we have sections with IDs (index.html)
    if (sections.length > 0) {
        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 150;
            const sectionId = section.getAttribute('id');
            const navLink = document.querySelector(`.nav-link[href="#${sectionId}"]`);
            
            if (currentScroll > sectionTop && currentScroll <= sectionTop + sectionHeight) {
                navLinks.forEach(link => {
                    link.classList.remove('text-primary', 'font-bold', 'bg-gray-50');
                    link.classList.add('text-gray-800');
                });
                if (navLink) {
                    navLink.classList.remove('text-gray-800');
                    navLink.classList.add('text-primary', 'font-bold');
                }
            }
        });
    }
});

// Highlight active page in navigation (for separate pages)
document.addEventListener('DOMContentLoaded', () => {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-link');
    
    navLinks.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref === currentPage || (currentPage === '' && linkHref === 'index.html')) {
            link.classList.add('text-primary', 'font-bold', 'bg-gray-50');
            link.classList.remove('text-gray-800');
        }
    });
});

// Smooth Scrolling for Anchor Links (only on index.html)
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        // Only prevent default if it's an anchor link on the same page
        if (href !== '#' && href.startsWith('#')) {
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                const offsetTop = target.offsetTop - 80;
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        }
    });
});

// Contact Form Handling
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        // Get form values
        const nameField = document.getElementById('name');
        const emailField = document.getElementById('email');
        const phoneField = document.getElementById('phone');
        const messageField = document.getElementById('message');
        const serviceField = document.getElementById('service');
        
        const formData = {
            name: nameField ? nameField.value : '',
            email: emailField ? emailField.value : '',
            phone: phoneField ? phoneField.value : '',
            message: messageField ? messageField.value : '',
            service: serviceField ? serviceField.value : ''
        };
        
        // Remove existing messages
        const existingMessage = contactForm.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Validate form
        if (!formData.name || !formData.email || !formData.message) {
            showFormMessage('Bitte füllen Sie alle Pflichtfelder aus.', 'error');
            return;
        }
        
        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(formData.email)) {
            showFormMessage('Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'error');
            return;
        }
        
        // Show loading state
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
        submitButton.disabled = true;
        
        // Send form data to API
        fetch('api/submit-contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFormMessage(data.message || 'Vielen Dank! Ihre Nachricht wurde erfolgreich gesendet. Wir werden uns bald bei Ihnen melden.', 'success');
                contactForm.reset();
            } else {
                showFormMessage(data.error || 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.', 'error');
            }
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            showFormMessage('Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut oder kontaktieren Sie uns direkt unter anduena@ab-bau-fliesen.de', 'error');
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
}

// Show Form Message
function showFormMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `form-message ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-4 rounded-xl mt-4 shadow-lg`;
    messageDiv.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    contactForm.appendChild(messageDiv);
    
    // Remove message after 5 seconds
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(-10px)';
        messageDiv.style.transition = 'all 0.3s ease-out';
        setTimeout(() => {
            messageDiv.remove();
        }, 300);
    }, 5000);
}

// Counter Animation for Stats
function animateCounter(element, target, duration = 2000) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target + (element.textContent.includes('+') ? '+' : '') + (element.textContent.includes('%') ? '%' : '');
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current) + (element.textContent.includes('+') ? '+' : '') + (element.textContent.includes('%') ? '%' : '');
        }
    }, 16);
}

// Intersection Observer for Counter Animation
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumber = entry.target.querySelector('.text-4xl, .text-5xl');
            if (statNumber && !statNumber.dataset.animated) {
                const text = statNumber.textContent;
                const number = parseInt(text.replace(/\D/g, ''));
                if (number) {
                    statNumber.dataset.animated = 'true';
                    animateCounter(statNumber, number);
                }
            }
        }
    });
}, { threshold: 0.5 });

// Observe stats elements
document.addEventListener('DOMContentLoaded', () => {
    const statsElements = document.querySelectorAll('#home + section .text-center');
    statsElements.forEach(element => {
        statsObserver.observe(element);
    });
});

// Parallax Effect for Hero Section - REMOVED to fix glitching issue

// Lazy Loading for Images (if you add real images later)
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Add loading animation to page
window.addEventListener('load', () => {
    document.body.classList.add('page-transition');
});

// Portfolio Item Hover Effects
const portfolioItems = document.querySelectorAll('.portfolio-item, .swiper-slide');
portfolioItems.forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.05)';
    });
    
    item.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});

// Service Card Animation on Scroll
const serviceCards = document.querySelectorAll('[data-aos]');
const cardObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, index * 100);
        }
    });
}, { threshold: 0.1 });

serviceCards.forEach(card => {
    cardObserver.observe(card);
});

// Console Welcome Message
// Star Rating System
const starRatings = document.querySelectorAll('.star-rating');
let selectedRating = 0;

if (starRatings.length > 0) {
    starRatings.forEach((star, index) => {
        star.addEventListener('click', () => {
            selectedRating = index + 1;
            updateStarDisplay();
        });
        
        star.addEventListener('mouseenter', () => {
            highlightStars(index + 1);
        });
    });

    const ratingContainer = document.getElementById('ratingStars');
    if (ratingContainer) {
        ratingContainer.addEventListener('mouseleave', () => {
            updateStarDisplay();
        });
    }
}

function highlightStars(rating) {
    starRatings.forEach((star, index) => {
        const icon = star.querySelector('i');
        if (icon) {
            if (index < rating) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                icon.style.color = '#fbbf24';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                icon.style.color = '#d1d5db';
            }
        }
    });
}

function updateStarDisplay() {
    highlightStars(selectedRating);
}

// Review Form Handling
const reviewForm = document.getElementById('reviewForm');
const reviewSuccess = document.getElementById('reviewSuccess');

if (reviewForm) {
    reviewForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (selectedRating === 0) {
            alert('Bitte wählen Sie eine Bewertung aus.');
            return;
        }
        
        const reviewData = {
            name: document.getElementById('reviewName').value,
            message: document.getElementById('reviewMessage').value,
            rating: selectedRating
        };
        
        // Show loading state
        const submitButton = reviewForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
        submitButton.disabled = true;
        
        // Submit review to API
        const formData = new FormData();
        formData.append('name', reviewData.name);
        formData.append('message', reviewData.message);
        formData.append('rating', reviewData.rating);
        
        fetch('api/submit-review.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log('Review submitted:', reviewData);
                
                // Show success message
                if (reviewSuccess) {
                    reviewSuccess.classList.remove('hidden');
                }
                reviewForm.reset();
                selectedRating = 0;
                updateStarDisplay();
                
                // Reload reviews to show the new one (if approved)
                if (typeof loadReviews === 'function') {
                    setTimeout(() => {
                        loadReviews();
                    }, 1000);
                }
            } else {
                alert('Fehler beim Senden der Bewertung. Bitte versuchen Sie es erneut.');
            }
            
            // Reset button
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            
            // Hide success message after 5 seconds
            if (reviewSuccess) {
                setTimeout(() => {
                    reviewSuccess.classList.add('hidden');
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Error submitting review:', error);
            alert('Fehler beim Senden der Bewertung. Bitte versuchen Sie es erneut.');
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        });
    });
}

// Portfolio Filter Functionality
function initPortfolioFilter() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    if (filterButtons.length > 0 && portfolioItems.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                const filter = button.getAttribute('data-filter');
                
                // Update active button
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('bg-white', 'text-gray-700', 'border', 'border-gray-200');
                });
                button.classList.add('active', 'bg-primary', 'text-white');
                button.classList.remove('bg-white', 'text-gray-700', 'border', 'border-gray-200');
                
                // Filter items with animation
                portfolioItems.forEach((item, index) => {
                    const category = item.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        item.style.display = 'block';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            item.style.transition = 'all 0.3s ease-out';
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, index * 50);
                    } else {
                        item.style.transition = 'all 0.3s ease-out';
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(-20px)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }
}

// Initialize portfolio filter on page load
document.addEventListener('DOMContentLoaded', () => {
    initPortfolioFilter();
});

// Scroll to top button functionality
function initScrollToTop() {
    // Create scroll to top button if it doesn't exist
    if (!document.getElementById('scrollToTop')) {
        const scrollButton = document.createElement('button');
        scrollButton.id = 'scrollToTop';
        scrollButton.className = 'fixed bottom-8 right-8 bg-primary text-white p-4 rounded-full shadow-lg hover:bg-primary-dark transition-all duration-300 opacity-0 pointer-events-none z-50';
        scrollButton.innerHTML = '<i class="fas fa-arrow-up text-xl"></i>';
        scrollButton.setAttribute('aria-label', 'Nach oben scrollen');
        document.body.appendChild(scrollButton);

        // Show/hide button on scroll
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollButton.style.opacity = '1';
                scrollButton.style.pointerEvents = 'auto';
            } else {
                scrollButton.style.opacity = '0';
                scrollButton.style.pointerEvents = 'none';
            }
        });

        // Scroll to top on click
        scrollButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Initialize scroll to top button
document.addEventListener('DOMContentLoaded', () => {
    initScrollToTop();
});

// Form input focus effects
document.addEventListener('DOMContentLoaded', () => {
    const inputs = document.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});

// Smooth page transitions
document.addEventListener('DOMContentLoaded', () => {
    document.body.style.opacity = '0';
    setTimeout(() => {
        document.body.style.transition = 'opacity 0.3s ease-in';
        document.body.style.opacity = '1';
    }, 100);
});

// Lazy load images with fade-in effect
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    img.style.opacity = '0';
                    img.style.transition = 'opacity 0.5s ease-in';
                    setTimeout(() => {
                        img.style.opacity = '1';
                    }, 100);
                    observer.unobserve(img);
                }
            }
        });
    }, { rootMargin: '50px' });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Cookie Banner
function initCookieBanner() {
    // Check if user has already accepted/rejected cookies
    const cookieConsent = localStorage.getItem('cookieConsent');
    
    if (!cookieConsent) {
        // Create cookie banner
        const cookieBanner = document.createElement('div');
        cookieBanner.id = 'cookieBanner';
        cookieBanner.className = 'fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 sm:p-6 z-50 shadow-2xl border-t border-gray-800 transform translate-y-full transition-transform duration-500 ease-out';
        cookieBanner.innerHTML = `
            <div class="container mx-auto max-w-7xl">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold mb-2">Wir respektieren Ihre Privatsphäre</h3>
                        <p class="text-sm text-gray-300 mb-2">
                            Wir verwenden Cookies und ähnliche Technologien, um das Nutzererlebnis auf unserer Website zu verbessern. 
                            Einige sind technisch notwendig, andere helfen uns, Inhalte zu personalisieren und Zugriffe zu analysieren.
                        </p>
                        <p class="text-sm">
                            <a href="datenschutz.php" class="text-primary hover:underline underline-offset-2">Datenschutzerklärung</a>
                            <span class="mx-2 text-gray-500">|</span>
                            <a href="impressum.php" class="text-gray-400 hover:text-white underline-offset-2">Impressum</a>
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        <button id="rejectCookies" class="px-6 py-2.5 rounded-lg font-medium border border-gray-600 hover:bg-gray-800 hover:border-gray-500 transition-all text-sm sm:text-base whitespace-nowrap">
                            Nur notwendige
                        </button>
                        <button id="acceptCookies" class="bg-primary hover:bg-primary-dark text-white px-6 py-2.5 rounded-lg font-medium shadow-lg hover:shadow-primary/25 transition-all text-sm sm:text-base whitespace-nowrap">
                            Alle akzeptieren
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(cookieBanner);
        
        // Slide in animation
        setTimeout(() => {
            cookieBanner.classList.remove('translate-y-full');
        }, 100);
        
        const closeBanner = () => {
            cookieBanner.classList.add('translate-y-full');
            setTimeout(() => {
                cookieBanner.remove();
            }, 500);
        };

        // Accept All
        document.getElementById('acceptCookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent', 'accepted');
            // Here you would enable tracking scripts if you had any
            closeBanner();
        });

        // Reject (Only Necessary)
        document.getElementById('rejectCookies').addEventListener('click', () => {
            localStorage.setItem('cookieConsent', 'rejected');
            // Here you would ensure tracking scripts remain disabled
            closeBanner();
        });
    }
}

// Initialize cookie banner when DOM is loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCookieBanner);
} else {
    initCookieBanner();
}

console.log('%cAB Bau - Bau und Fliesen GmbH', 'color: #0066cc; font-size: 20px; font-weight: bold;');
console.log('%cProfessionelle Bau- und Fliesenarbeiten', 'color: #666; font-size: 14px;');
console.log('%cKontakt: office@ab-bau.de | Tel: 08137 9957477', 'color: #999; font-size: 12px;');

