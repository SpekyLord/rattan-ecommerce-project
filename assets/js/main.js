// Handle missing product images
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img');
    
    images.forEach(img => {
        img.addEventListener('error', function() {
            // Replace broken image with placeholder
            this.src = 'https://via.placeholder.com/300x300/0038A8/FFFFFF?text=Rattan+Product';
            this.alt = 'Product image placeholder';
        });
    });
});

// Confirm delete actions
function confirmDelete(itemName) {
    return confirm(`Are you sure you want to delete "${itemName}"?`);
}