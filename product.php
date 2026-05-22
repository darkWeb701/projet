<?php
include 'includes/config.php';
include 'includes/functions.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier la connexion PDO
if (!$pdo) {
    die("Erreur: Connexion à la base de données non établie");
}

try {
    /* Récupérer produit */
    $sql = "SELECT * FROM products WHERE id = :product_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':product_id' => $product_id]);
    $product = $stmt->fetch();
    
    if (!$product) {
        header("Location: products.php");
        exit();
    }
    
    /* Récupérer tailles AVEC le stock (mais on ne l'affiche pas) */
    $sql_sizes = "SELECT size, stock 
                  FROM product_variants 
                  WHERE product_id = :product_id 
                  ORDER BY size";
    $stmt_sizes = $pdo->prepare($sql_sizes);
    $stmt_sizes->execute([':product_id' => $product_id]);
    $sizes = $stmt_sizes->fetchAll();
    
} catch(PDOException $e) {
    error_log("Erreur PDO: " . $e->getMessage());
    header("Location: products.php");
    exit();
}

/* Ajouter au panier */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $quantity = (int)$_POST['quantity'];
    $size = $_POST['size'] ?? '';
    
    if ($quantity > 0 && !empty($size)) {
        if (addToCart($product_id, $quantity, $size)) {
            $added_to_cart = true;
        } else {
            $error_message = "Stock insuffisant !";
        }
    }
}
?>

<?php include 'templates/header.html'; ?>

<section class="product-detail-section">
    <div class="container">

        <div class="product-detail-container">

            <!-- IMAGE -->
            <div class="product-image">
                <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg'">
            </div>

            <!-- INFOS -->
            <div class="product-info">

                <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                <p class="product-price">
                    <?php echo number_format($product['price'], 0, ',', ' '); ?> DA
                </p>

                <p class="product-description">
                    <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                </p>

                <?php if (isset($added_to_cart)): ?>
                    <div class="success-message">
                        ✔ Produit ajouté au panier !
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="error-message">
                        ❌ <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULAIRE -->
                <form method="POST" class="add-to-cart-form" id="add-to-cart-form">

                    <!-- TAILLE - Avec attribut data-stock en cache -->
                    <div class="form-group">
                        <label for="size">Taille :</label>
                        <select name="size" id="size" required>
                            <option value="">Choisir une taille</option>
                            <?php foreach($sizes as $size): ?>
                                <option value="<?php echo htmlspecialchars($size['size']); ?>" 
                                        data-stock="<?php echo $size['stock']; ?>">
                                    <?php echo htmlspecialchars($size['size']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- QUANTITÉ -->
                    <div class="form-group">
                        <label for="quantity">Quantité :</label>
                        <input type="number" name="quantity" id="quantity"
                               value="1" min="1" max="10" required>
                    </div>

                    <button type="submit" name="add_to_cart" class="btn-add-to-cart">
                        🛒 Ajouter au panier
                    </button>

                </form>

            </div>

        </div>

    </div>
</section>

<!-- JavaScript pour gérer le vrai stock -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sizeSelect = document.getElementById('size');
    const quantityInput = document.getElementById('quantity');
    const form = document.getElementById('add-to-cart-form');
    
    // Fonction pour mettre à jour la quantité max selon la taille choisie
    function updateMaxQuantity() {
        const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
        
        if (selectedOption && selectedOption.value) {
            // Récupérer le stock réel de la taille choisie
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            
            // Limiter la quantité au stock réel (max 99)
            const maxQty = Math.min(stock, 99);
            
            if (maxQty > 0) {
                quantityInput.max = maxQty;
                // Si la valeur actuelle dépasse le max, la réduire
                if (parseInt(quantityInput.value) > maxQty) {
                    quantityInput.value = maxQty;
                }
            } else {
                quantityInput.max = 0;
                quantityInput.value = 0;
            }
        } else {
            // Aucune taille sélectionnée
            quantityInput.max = 10;
            quantityInput.value = 1;
        }
    }
    
    // Écouter le changement de taille
    if (sizeSelect) {
        sizeSelect.addEventListener('change', updateMaxQuantity);
    }
    
    // Validation avant soumission
    if (form) {
        form.addEventListener('submit', function(e) {
            const size = sizeSelect.value;
            
            if (!size) {
                e.preventDefault();
                alert('Veuillez choisir une taille');
                return false;
            }
            
            const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
            const quantity = parseInt(quantityInput.value);
            
            if (quantity > stock) {
                e.preventDefault();
                alert(`Stock insuffisant ! Maximum disponible: ${stock}`);
                return false;
            }
            
            if (quantity < 1) {
                e.preventDefault();
                alert('La quantité doit être au moins 1');
                return false;
            }
            
            return true;
        });
    }
});
</script>

<?php include 'templates/footer.html'; ?>