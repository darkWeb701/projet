<?php
// order-confirmation.php
include 'includes/config.php';
include 'includes/functions.php';

if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['last_order_id'];
$order_number = $_SESSION['last_order_number'];

// Récupérer les détails de la commande
$sql = "SELECT * FROM orders WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch();

$sql_items = "SELECT * FROM order_items WHERE order_id = :order_id";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([':order_id' => $order_id]);
$items = $stmt_items->fetchAll();

// Supprimer de la session
unset($_SESSION['last_order_id']);
unset($_SESSION['last_order_number']);

$page_title = "Confirmation - Hoodix";
?>

<?php include 'templates/header.html'; ?>

<section class="confirmation-section">
    <div class="container">
        <div class="confirmation-card">
            <div class="success-icon">✓</div>
            <h1>Merci pour votre commande !</h1>
            <p>Votre commande a été enregistrée avec succès.</p>
            
            <div class="order-info">
                <p><strong>Numéro de commande :</strong> <?php echo $order['order_number']; ?></p>
                <p><strong>Date :</strong> <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?></p>
                <p><strong>Montant total :</strong> <?php echo number_format($order['total_amount'], 0, ',', ' '); ?> DA</p>
                <p><strong>Mode de paiement :</strong> <?php echo $order['payment_method'] == 'cash' ? 'Paiement à la livraison' : 'Carte bancaire'; ?></p>
            </div>
            
            <h2>Récapitulatif de votre commande</h2>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Quantité</th>
                        <th>Prix</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><?php echo $item['product_name']; ?></td>
                        <td><?php echo $item['size'] ?: '-'; ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['product_price'], 0, ',', ' '); ?> DA</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="delivery-info">
                <h3>📦 Informations de livraison</h3>
                <p>
                    <?php echo $order['first_name'] . ' ' . $order['last_name']; ?><br>
                    <?php echo $order['address']; ?><br>
                    <?php echo $order['city'] . ' - ' . $order['postal_code']; ?><br>
                    📞 <?php echo $order['phone']; ?>
                </p>
            </div>
            
            <div class="actions">
                <a href="index.php" class="btn">Retour à l'accueil</a>
                <a href="products.php" class="btn btn-primary">Continuer mes achats</a>
            </div>
        </div>
    </div>
</section>

<style>
.confirmation-section {
    padding: 60px 0;
    min-height: 70vh;
}

.confirmation-card {
    max-width: 800px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    text-align: center;
}

.success-icon {
    width: 80px;
    height: 80px;
    background: #4caf50;
    color: white;
    font-size: 50px;
    line-height: 80px;
    border-radius: 50%;
    margin: 0 auto 20px;
}

.order-info {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
    text-align: left;
}

.order-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.order-table th, .order-table td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
}

.order-table th {
    background: #333;
    color: white;
}

.delivery-info {
    text-align: left;
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin: 20px 0;
}

.actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.btn-primary {
    background: #ff6600;
    color: white;
}
</style>

<?php include 'templates/footer.html'; ?>