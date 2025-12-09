const trigger = document.getElementById('custom-select-trigger');
const options = document.getElementById('custom-options');
const selectedText = document.getElementById('selected-text');
const input = document.getElementById('category-input');
const form = document.querySelector('.custom-category-form');
const arrow = trigger.querySelector('.arrow');

if (!input.value) {
    input.value = 'All Products';
    selectedText.textContent = 'All Products';
}

trigger.addEventListener('click', function(e) {
    e.stopPropagation();
    options.classList.toggle('open');
    arrow.style.transform = options.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0deg)';
});

options.addEventListener('click', function(e) {
    if (e.target.classList.contains('option')) {
        const value = e.target.getAttribute('data-value');
        selectedText.textContent = value;
        input.value = value;

        options.querySelectorAll('.option').forEach(o => o.removeAttribute('data-selected'));
        e.target.setAttribute('data-selected', 'true');

        options.classList.remove('open');
        arrow.style.transform = 'rotate(0deg)';

        // Submit the form after selection
        window.location.href = `products.php?category=${encodeURIComponent(value)}`;
    }
});

document.addEventListener('click', function() {
    options.classList.remove('open');
    arrow.style.transform = 'rotate(0deg)';
});
