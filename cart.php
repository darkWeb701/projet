<?php
include 'includes/config.php';
include 'includes/functions.php';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Mettre à jour le panier
    if (isset($_POST['update_cart']) && isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $id => $qty) {
            updateCartQuantity($id, (int)$qty);
        }
        redirect('cart.php');
    }
    
    // Supprimer un article spécifique
    elseif (isset($_POST['remove_item'])) {
        removeFromCart($_POST['remove_item']);
        redirect('cart.php');
    }
    
    // Vider tout le panier
    elseif (isset($_POST['clear_cart'])) {
        clearCart();
        redirect('cart.php');
    }
}

$cart = getCartItems();
$page_title = "Mon Panier - Hoodix";
?>

<?php include 'templates/header.html'; ?>

<section class="cart-section">
    <div class="container">
        <h1>Mon Panier</h1>
        
        <?php if (empty($cart['items'])): ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Votre panier est vide.</p>
            <a href="products.php" class="btn">Continuer mes achats</a>
        </div>
        <?php else: ?>
        
        <form method="POST" action="">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart['items'] as $item): ?>
                    <tr>
                        <td class="cart-product">
                            <img src="assets/images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px;">
                            <span><?php echo htmlspecialchars($item['name']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($item['cart_size'] ?: '-'); ?></td>
                        <td><?php echo number_format($item['price'], 0, ',', ' '); ?> DA</td>
                        <td>
                            <input type="number" 
                                   name="quantities[<?php echo $item['id']; ?>]" 
                                   value="<?php echo $item['cart_quantity']; ?>" 
                                   min="0" 
                                   max="99" 
                                   style="width: 60px;">
                        </td>
                        <td><?php echo number_format($item['subtotal'], 0, ',', ' '); ?> DA</td>
                        <td>
                            <button type="submit" name="remove_item" value="<?php echo $item['id']; ?>" class="remove-btn" onclick="return confirm('Supprimer ce produit ?')">
                                🗑️ Supprimer
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4"><strong>Total général</strong></td>
                        <td colspan="2"><strong><?php echo number_format($cart['total'], 0, ',', ' '); ?> DA</strong></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="cart-actions" style="margin-top: 20px;">
                <button type="submit" name="update_cart" class="btn-update">🔄 Mettre à jour</button>
                <button type="submit" name="clear_cart" class="btn-clear" onclick="return confirm('Vider complètement le panier ?')">🗑️ Vider le panier</button>
                <a href="checkout.php" class="btn-checkout">✅ Passer la commande</a>
            </div>
        </form>
        
        <?php endif; ?>
    </div>
</section>

<?php include 'templates/footer.html'; ?>