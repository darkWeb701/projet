<?php
// Inclure les fichiers de configuration et de fonctions si nécessaire
// include '../includes/config.php';
// include '../includes/functions.php';

// Définir le titre de la page pour le header (optionnel)
$page_title = "Accueil - Hoodix";
?>
<?php include 'templates/header.html'; ?>

<!-- Section Héro (image d'accueil) -->
<section class="hero">
    <div class="hero-content">
        <h1>Nouvelle Collection Hiver 2025</h1>
        <p>Découvrez nos hoodies uniques alliant confort et style urbain.</p>
        <a href="products.php" class="btn">Shop Now</a>
    </div>
</section>

<!-- Section New Collection -->
<section class="new-collection">
    <h2>New Collection</h2>
    <div class="products-grid">
        <!-- Exemple de produits (à remplacer par une boucle PHP plus tard) -->
        <div class="product-card">
            <img src="/assets/images/hoodie1.png" alt="Hoodie blanc">
            <h3>Hoodie blanc</h3>
            <p class="price">2500DA</p>
            <a href="product.php?id=1" class="btn-small">Voir le produit</a>
        </div>
        <div class="product-card">
            <img src="/assets/images/hoodie2.png" alt="Hoodie gris">
            <h3>Hoodie gris</h3>
            <p class="price">2000DA</p>
            <a href="product.php?id=2" class="btn-small">Voir le produit</a>
        </div>
        <div class="product-card">
            <img src="/assets/images/hoodie3.png" alt="Hoodie noir">
            <h3>Hoodie noir</h3>
            <p class="price">3000DA</p>
            <a href="product.php?id=3" class="btn-small">Voir le produit</a>
        </div>
    </div>
</section>

<?php include 'templates/footer.html'; ?>