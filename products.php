<?php
include 'includes/config.php';
include 'includes/functions.php';

$page_title = "Catalogue - Hoodix";

// Vérifier la connexion PDO
if (!$pdo) {
    die("Erreur: Connexion à la base de données non établie");
}

try {
    /* Récupérer les produits depuis MySQL avec PDO */
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>

<?php include 'templates/header.html'; ?>

<section class="catalogue-section">
    <div class="container">
        
        <h1>Notre Catalogue</h1>

        <!-- Filtres -->
        <div class="filters">
            <div class="filter-group">
                <label for="sort">Trier par :</label>
                <select id="sort" onchange="filterProducts()">
                    <option value="default">Par défaut</option>
                    <option value="price_asc">Prix croissant</option>
                    <option value="price_desc">Prix décroissant</option>
                    <option value="name_asc">Nom A-Z</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="search-filter">Rechercher :</label>
                <input 
                    type="text"
                    id="search-filter"
                    placeholder="Nom du produit..."
                    onkeyup="filterProducts()"
                >
            </div>
        </div>

        <!-- Produits -->
        <div class="products-grid" id="products-grid">

            <?php foreach($products as $product): ?>
            <div 
                class="product-card"
                data-name="<?php echo strtolower(htmlspecialchars($product['name'])); ?>"
                data-price="<?php echo $product['price']; ?>"
            >
                <img 
                    src="assets/images/<?php echo htmlspecialchars($product['image']); ?>"
                    alt="<?php echo htmlspecialchars($product['name']); ?>"
                >
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="price">
                    <?php echo number_format($product['price'], 0, ',', ' '); ?> DA
                </p>
                <p class="description">
                    <?php echo htmlspecialchars(substr($product['description'], 0, 80)); ?>...
                </p>
                <a 
                    href="product.php?id=<?php echo $product['id']; ?>"
                    class="btn-small"
                >
                    Voir le produit
                </a>
            </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>

<script>
function filterProducts() {
    const sortBy = document.getElementById('sort').value;
    const searchTerm = document.getElementById('search-filter').value.toLowerCase();
    const productsGrid = document.getElementById('products-grid');
    const products = Array.from(productsGrid.getElementsByClassName('product-card'));

    // Filtrer
    let filteredProducts = products.filter(product => {
        const name = product.getAttribute('data-name');
        return name.includes(searchTerm);
    });

    // Trier
    filteredProducts.sort((a, b) => {
        if (sortBy === 'price_asc') {
            return parseInt(a.getAttribute('data-price')) - parseInt(b.getAttribute('data-price'));
        } else if (sortBy === 'price_desc') {
            return parseInt(b.getAttribute('data-price')) - parseInt(a.getAttribute('data-price'));
        } else if (sortBy === 'name_asc') {
            return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
        }
        return 0;
    });

    // Réaffichage
    productsGrid.innerHTML = '';
    filteredProducts.forEach(product => {
        productsGrid.appendChild(product);
    });
}
</script>

<?php include 'templates/footer.html'; ?>