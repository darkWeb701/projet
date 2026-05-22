<?php
include 'includes/config.php';

$product_id = (int)$_GET['product_id'];
$size = $_GET['size'];

$sql = "SELECT stock 
        FROM product_variants 
        WHERE product_id = $product_id 
        AND size = '$size'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

echo json_encode([
    "stock" => $row['stock'] ?? 10
]);
?>