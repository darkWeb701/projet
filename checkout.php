<?php
// checkout.php
include 'includes/config.php';
include 'includes/functions.php';

$cart = getCartItems();

if (empty($cart['items'])) {
    header('Location: cart.php');
    exit();
}

$page_title = "Validation de commande - Hoodix";
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $payment_method = $_POST['payment'] ?? 'cash';
    
    // Validation
    $errors = [];
    if (empty($first_name)) $errors[] = "Prénom requis";
    if (empty($last_name)) $errors[] = "Nom requis";
    if (empty($email)) $errors[] = "Email requis";
    if (empty($phone)) $errors[] = "Téléphone requis";
    if (empty($address)) $errors[] = "Adresse requise";
    if (empty($city)) $errors[] = "Ville requise";
    
    // Vérifier le stock
    if (!checkCartStock()) {
        $errors[] = "Certains produits ne sont plus en stock !";
    }
    
    if (empty($errors)) {
        try {
            // Générer un numéro de commande unique
            $order_number = 'HDX-' . date('Ymd') . '-' . rand(1000, 9999);
            
            // Insérer la commande
            $sql = "INSERT INTO orders (order_number, user_id, first_name, last_name, email, phone, 
                    address, city, postal_code, notes, payment_method, total_amount, status) 
                    VALUES (:order_number, :user_id, :first_name, :last_name, :email, :phone, 
                    :address, :city, :postal_code, :notes, :payment_method, :total_amount, 'pending')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':order_number' => $order_number,
                ':user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':city' => $city,
                ':postal_code' => $postal_code,
                ':notes' => $notes,
                ':payment_method' => $payment_method,
                ':total_amount' => $cart['total']
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            // Insérer les détails de la commande
            foreach ($cart['items'] as $item) {
                $sql = "INSERT INTO order_items (order_id, product_id, product_name, product_price, quantity, size, subtotal) 
                        VALUES (:order_id, :product_id, :product_name, :product_price, :quantity, :size, :subtotal)";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $item['id'],
                    ':product_name' => $item['name'],
                    ':product_price' => $item['price'],
                    ':quantity' => $item['cart_quantity'],
                    ':size' => $item['cart_size'],
                    ':subtotal' => $item['subtotal']
                ]);
                
                // Mettre à jour le stock
                if (!empty($item['cart_size'])) {
                    $sql = "UPDATE product_variants 
                            SET stock = stock - :quantity 
                            WHERE product_id = :product_id AND size = :size";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':quantity' => $item['cart_quantity'],
                        ':product_id' => $item['id'],
                        ':size' => $item['cart_size']
                    ]);
                }
            }
            
            // Vider le panier
            clearCart();
            
            // Stocker l'ID de commande en session
            $_SESSION['last_order_id'] = $order_id;
            $_SESSION['last_order_number'] = $order_number;
            
            // Rediriger vers la page de confirmation
            header("Location: order-confirmation.php");
            exit();
            
        } catch(PDOException $e) {
            $error_message = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>

<?php include 'templates/header.html'; ?>

<section class="checkout-section">
    <div class="container">
        <h1>Validation de commande</h1>
        
        <?php if ($error_message): ?>
            <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="checkout-form">
            <div class="checkout-grid">
                <div class="billing-info">
                    <h3>Informations de livraison</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">Prénom *</label>
                            <input type="text" name="first_name" id="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Nom *</label>
                            <input type="text" name="last_name" id="last_name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" name="email" id="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Téléphone *</label>
                        <input type="tel" name="phone" id="phone" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Adresse *</label>
                        <input type="text" name="address" id="address" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city">Ville *</label>
                            <input type="text" name="city" id="city" required>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Code postal *</label>
                            <input type="text" name="postal_code" id="postal_code" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes de commande</label>
                        <textarea name="notes" id="notes" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="order-summary">
                    <h3>Récapitulatif</h3>
                    
                    <table class="summary-table">
                        <?php foreach ($cart['items'] as $item): ?>
                            <tr>
                                <td>
                                    <?php echo $item['name']; ?><br>
                                    <small>Taille: <?php echo $item['cart_size'] ?: '-'; ?></small><br>
                                    <small>x<?php echo $item['cart_quantity']; ?></small>
                                </td>
                                <td><?php echo number_format($item['subtotal'], 0, ',', ' '); ?> DA</td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="total-row">
                            <td><strong>Total</strong></td>
                            <td><strong><?php echo number_format($cart['total'], 0, ',', ' '); ?> DA</strong></td>
                        </tr>
                    </table>
                    
                    <div class="payment-method">
                        <h4>Mode de paiement</h4>
                        <label>
                            <input type="radio" name="payment" value="cash" checked> 
                            Paiement à la livraison
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-confirm">Confirmer la commande</button>
                </div>
            </div>
        </form>
    </div>
</section>

<style>
.checkout-section {
    padding: 60px 0;
}

.checkout-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
}

.form-row {
    display: flex;
    gap: 20px;
}

.form-group {
    flex: 1;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.summary-table {
    width: 100%;
    border-collapse: collapse;
}

.summary-table td {
    padding: 10px 0;
    border-bottom: 1px solid #ddd;
}

.total-row td {
    border-top: 2px solid #333;
    border-bottom: none;
    padding-top: 15px;
}

.payment-method {
    margin: 20px 0;
}

.btn-confirm {
    width: 100%;
    background: #e63946;
    color: white;
    padding: 15px;
    border: none;
    border-radius: 5px;
    font-size: 1.1rem;
    cursor: pointer;
}

.btn-confirm:hover {
    background: #c82333;
}

@media (max-width: 768px) {
    .checkout-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'templates/footer.html'; ?>