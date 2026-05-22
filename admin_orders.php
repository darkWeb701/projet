<?php
// admin/orders.php
include 'includes/config.php';
include 'includes/functions.php';

// Vérifier si l'admin est connecté (à créer)
// if (!isAdmin()) {
//     header('Location: ../login.php');
//     exit();
// }


if (!isAdmin()) {
    header('Location: /login.php');
    exit();
}


$page_title = "Gestion des commandes - Admin";

// Récupérer toutes les commandes
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll();

// Mettre à jour le statut d'une commande
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    
    $sql = "UPDATE orders SET status = :status WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':status' => $status, ':id' => $order_id]);
    
    header('Location: admin_orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
        }
        
        .admin-header {
            background: #333;
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #ff6600;
        }
        
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #333;
            color: white;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            font-weight: bold;
        }
        
        .status-pending { background: #ffc107; color: #333; }
        .status-processing { background: #17a2b8; color: white; }
        .status-shipped { background: #007bff; color: white; }
        .status-delivered { background: #28a745; color: white; }
        .status-cancelled { background: #dc3545; color: white; }
        
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        
        .btn-view {
            background: #007bff;
            color: white;
        }
        
        select, button {
            padding: 5px 10px;
            margin: 0 2px;
        }
        
        .order-details {
            display: none;
            background: #f9f9f9;
            padding: 20px;
            margin-top: 10px;
        }
        
        .order-details.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>📦 Gestion des commandes</h1>
            <p>Bienvenue dans l'espace administrateur</p>
        </div>
        
        <?php
        // Statistiques
        $total_orders = count($orders);
        $pending = count(array_filter($orders, function($o) { return $o['status'] == 'pending'; }));
        $delivered = count(array_filter($orders, function($o) { return $o['status'] == 'delivered'; }));
        $total_revenue = array_sum(array_column($orders, 'total_amount'));
        ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div>Commandes totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending; ?></div>
                <div>En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $delivered; ?></div>
                <div>Livrées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo number_format($total_revenue, 0, ',', ' '); ?> DA</div>
                <div>Chiffre d'affaires</div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Commande</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td><strong><?php echo $order['order_number']; ?></strong></td>
                    <td>
                        <?php echo $order['first_name'] . ' ' . $order['last_name']; ?><br>
                        <small><?php echo $order['email']; ?></small>
                    </td>
                    <td><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> DA</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <span class="status status-<?php echo $order['status']; ?>">
                            <?php 
                                $status_text = [
                                    'pending' => 'En attente',
                                    'processing' => 'En traitement',
                                    'shipped' => 'Expédiée',
                                    'delivered' => 'Livrée',
                                    'cancelled' => 'Annulée'
                                ];
                                echo $status_text[$order['status']];
                            ?>
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-view" onclick="toggleDetails(<?php echo $order['id']; ?>)">
                            Voir détails
                        </button>
                        
                        <form method="POST" style="display: inline-block;" onchange="this.submit()">
                           <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                           <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>En traitement</option>
                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Expédiée</option>
                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                          </select>
                          <input type="hidden" name="update_status" value="1">
                       </form>
                    </td>
                </tr>
                <tr id="details-<?php echo $order['id']; ?>" class="order-details">
                    <td colspan="6">
                        <?php
                        // Récupérer les détails de la commande
                        $sql_items = "SELECT * FROM order_items WHERE order_id = :order_id";
                        $stmt_items = $pdo->prepare($sql_items);
                        $stmt_items->execute([':order_id' => $order['id']]);
                        $items = $stmt_items->fetchAll();
                        ?>
                        <h4>📋 Détails de la commande</h4>
                        <table style="background: white; margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Taille</th>
                                    <th>Quantité</th>
                                    <th>Prix unitaire</th>
                                    <th>Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($items as $item): ?>
                                <tr>
                                    <td><?php echo $item['product_name']; ?></td>
                                    <td><?php echo $item['size'] ?: '-'; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['product_price'], 0, ',', ' '); ?> DA</td>
                                    <td><?php echo number_format($item['subtotal'], 0, ',', ' '); ?> DA</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <p style="margin-top: 10px;">
                            <strong>📦 Adresse de livraison :</strong><br>
                            <?php echo $order['address'] . ', ' . $order['city'] . ' - ' . $order['postal_code']; ?><br>
                            <strong>📞 Téléphone :</strong> <?php echo $order['phone']; ?>
                        </p>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function toggleDetails(orderId) {
            const detailsRow = document.getElementById('details-' + orderId);
            detailsRow.classList.toggle('show');
        }
    </script>
</body>
</html>