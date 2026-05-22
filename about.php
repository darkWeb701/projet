<?php
include 'includes/config.php';
include 'includes/functions.php';

$page_title = "À propos - Hoodix";
?>
<?php include 'templates/header.html'; ?>

<section class="about-section">
    <div class="container">
        <h1>À propos de Hoodix</h1>
        
        <div class="about-content">
            <div class="about-image">
                <img src="../assets/images/about-bg.jpg" alt="Hoodix - À propos">
            </div>
            
            <div class="about-text">
                <h2>Notre histoire</h2>
                <p>Hoodix est née d'une passion pour le streetwear et le confort. Fondée en 2020, notre marque s'est donné pour mission de créer des hoodies de qualité qui allient style, confort et durabilité.</p>
                
                <h2>Notre engagement</h2>
                <p>Nous utilisons des matériaux biologiques et durables pour minimiser notre impact environnemental. Chaque hoodie est conçu avec soin pour durer dans le temps.</p>
                
                <h2>Pourquoi nous choisir ?</h2>
                <ul>
                    <li><i class="fas fa-check-circle"></i> Qualité premium</li>
                    <li><i class="fas fa-check-circle"></i> Design unique</li>
                    <li><i class="fas fa-check-circle"></i> Livraison rapide</li>
                    <li><i class="fas fa-check-circle"></i> Service client réactif</li>
                </ul>
            </div>
        </div>
        
        <div class="values-section">
            <h2>Nos valeurs</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-leaf"></i>
                    <h3>Éco-responsable</h3>
                    <p>Des matériaux durables pour préserver la planète.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-heart"></i>
                    <h3>Qualité</h3>
                    <p>Des produits conçus pour durer dans le temps.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-star"></i>
                    <h3>Style</h3>
                    <p>Des designs uniques et tendance.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.html'; ?>