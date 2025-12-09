// ==================== PARALLAX SCROLL EFFECT ====================
document.addEventListener('DOMContentLoaded', function() {
    const heroBg = document.getElementById('heroBg');
    
    if (heroBg) {
        // Only scroll parallax - no mouse movement
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxSpeed = 0.15; // Slow and smooth
            heroBg.style.transform = `translateY(${scrolled * parallaxSpeed}px)`;
        });
    }
});


// ==================== CUSTOM CATEGORY DROPDOWN ====================
const trigger = document.getElementById('custom-select-trigger');
const options = document.getElementById('custom-options');
const selectedText = document.getElementById('selected-text');
const input = document.getElementById('category-input');
const arrow = trigger ? trigger.querySelector('.arrow') : null;

// Initialize default value if empty
if (trigger && input && !input.value) {
    input.value = 'All Categories';
    selectedText.textContent = 'All Categories';
}

// Toggle dropdown on trigger click
if (trigger && options && arrow) {
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        options.classList.toggle('open');
        arrow.style.transform = options.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
    });

    // Handle option selection
    options.addEventListener('click', function(e) {
        if (e.target.classList.contains('option')) {
            const value = e.target.getAttribute('data-value');
            selectedText.textContent = value;
            input.value = value;

            // Update selected state
            options.querySelectorAll('.option').forEach(o => o.removeAttribute('data-selected'));
            e.target.setAttribute('data-selected', 'true');

            // Close dropdown
            options.classList.remove('open');
            arrow.style.transform = 'rotate(0deg)';

            // Redirect with selected category
            window.location.href = `products.php?category=${encodeURIComponent(value)}`;
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function() {
        options.classList.remove('open');
        arrow.style.transform = 'rotate(0deg)';
    });
}

// ==================== PRODUCT DETAIL IMAGE GALLERY ====================
function changeImage(thumbnail) {
    const mainImage = document.getElementById('mainImage');
    if (mainImage && thumbnail) {
        // Update main image source
        mainImage.src = thumbnail.src;
        
        // Update active thumbnail
        const thumbnails = document.querySelectorAll('.thumbnail');
        thumbnails.forEach(t => t.classList.remove('active'));
        thumbnail.classList.add('active');
    }
}

// ==================== QUANTITY CONTROLS ====================
function incrementQty(maxStock) {
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        let currentQty = parseInt(qtyInput.value);
        if (currentQty < maxStock) {
            qtyInput.value = currentQty + 1;
        }
    }
}

function decrementQty() {
    const qtyInput = document.getElementById('quantity');
    if (qtyInput) {
        let currentQty = parseInt(qtyInput.value);
        if (currentQty > 1) {
            qtyInput.value = currentQty - 1;
        }
    }
}

// ==================== ADD TO CART (PLACEHOLDER) ====================
function addToCart() {
    const qtyInput = document.getElementById('quantity');
    const productTitle = document.querySelector('.product-title');
    
    if (qtyInput && productTitle) {
        const quantity = qtyInput.value;
        const productName = productTitle.textContent;
        
        // Show success message (you can replace this with actual cart logic)
        alert(`Added ${quantity}x "${productName}" to cart!\n\nNote: This is a demo. Implement actual cart functionality here.`);
        
        // TODO: Implement actual cart functionality
        // Example:
        // - Save to localStorage
        // - Update cart count in navbar
        // - Show cart modal
        // - Send AJAX request to server
    }
}

// ==================== SMOOTH SCROLL FOR ANCHOR LINKS ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        
        // Only apply smooth scroll if it's a valid anchor (not just "#")
        if (href && href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ==================== NAVBAR SCROLL EFFECT (OPTIONAL) ====================
let lastScrollTop = 0;
const navbar = document.querySelector('.navbar');

if (navbar) {
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Add shadow when scrolled
        if (scrollTop > 50) {
            navbar.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        } else {
            navbar.style.boxShadow = 'none';
        }
        
        lastScrollTop = scrollTop;
    });
}

// ==================== LAZY LOAD IMAGES (OPTIONAL) ====================
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

    // Observe all images with data-src attribute
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ==================== FORM VALIDATION HELPERS ====================
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

// ==================== CONSOLE GREETING (FUN) ====================
console.log('%c🇵🇭 Sophee\'s Home Decors and Native Products', 
    'color: #754A39; font-size: 20px; font-weight: bold;');
console.log('%cHandcrafted with tradition, made with pride!', 
    'color: #967162; font-size: 14px;');

// ==================== DEVELOPMENT HELPERS ====================
// Remove in production
if (window.location.hostname === 'localhost') {
    console.log('%c🔧 Development Mode', 
        'background: #ff9800; color: white; padding: 4px 8px; border-radius: 4px;');
}