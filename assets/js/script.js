

document.addEventListener('DOMContentLoaded', function() {
    // Simulation : on récupère le nombre d'articles depuis le localStorage ou une variable PHP
    let cartCount = localStorage.getItem('cartCount') || 0;
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }

    
});