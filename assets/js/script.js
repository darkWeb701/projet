// Exemple simple pour mettre à jour le compteur du panier
// À remplacer par une vraie logique AJAX ou PHP

document.addEventListener('DOMContentLoaded', function() {
    // Simulation : on récupère le nombre d'articles depuis le localStorage ou une variable PHP
    let cartCount = localStorage.getItem('cartCount') || 0;
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = cartCount;
    }

    // Si vous avez un bouton "Ajouter au panier", vous pouvez incrémenter ce compteur
    // Exemple (à adapter) :
    // const addToCartButtons = document.querySelectorAll('.add-to-cart');
    // addToCartButtons.forEach(button => {
    //     button.addEventListener('click', () => {
    //         cartCount++;
    //         localStorage.setItem('cartCount', cartCount);
    //         cartCountElement.textContent = cartCount;
    //     });
    // });
});