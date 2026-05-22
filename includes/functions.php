<?php
// includes/functions.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ajouter un produit au panier
 */
function addToCart($product_id, $quantity = 1, $size = '') {
    global $pdo;
    
    // Vérifier si la connexion existe
    if (!$pdo) {
        error_log("PDO n'est pas initialisé dans addToCart");
        return false;
    }
    
    // Vérifier si le produit existe
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    
    if (!$stmt->fetch()) {
        return false;
    }
    
    // Si une taille est spécifiée, vérifier le stock
    if (!empty($size)) {
        $stmt = $pdo->prepare("SELECT stock FROM product_variants WHERE product_id = ? AND size = ?");
        $stmt->execute([$product_id, $size]);
        $variant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$variant || $variant['stock'] < $quantity) {
            return false;
        }
    }
    
    // Ajouter au panier
    if (!isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = [
            'quantity' => 0,
            'size' => $size
        ];
    }
    
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    
    if (!empty($size)) {
        $_SESSION['cart'][$product_id]['size'] = $size;
    }
    
    return true;
}

/**
 * Supprimer un produit du panier
 */
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

/**
 * Mettre à jour la quantité d'un produit
 */
function updateCartQuantity($product_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
}

/**
 * Obtenir le contenu du panier
 */
function getCartItems() {
    global $pdo;
    
    // Vérifier si la connexion existe
    if (!$pdo) {
        error_log("PDO n'est pas initialisé dans getCartItems");
        return ['items' => [], 'total' => 0];
    }
    
    $cart_items = [];
    $total = 0;
    
    if (empty($_SESSION['cart'])) {
        return ['items' => [], 'total' => 0];
    }
    
    foreach ($_SESSION['cart'] as $id => $item) {
        // Récupérer les infos du produit
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $product['cart_quantity'] = $item['quantity'];
            $product['cart_size'] = $item['size'] ?? '';
            $product['subtotal'] = $product['price'] * $item['quantity'];
            
            // Récupérer le stock disponible pour cette taille
            if (!empty($item['size'])) {
                $stmt = $pdo->prepare("SELECT stock FROM product_variants WHERE product_id = ? AND size = ?");
                $stmt->execute([$id, $item['size']]);
                $variant = $stmt->fetch(PDO::FETCH_ASSOC);
                $product['available_stock'] = $variant ? $variant['stock'] : 0;
            } else {
                $product['available_stock'] = PHP_INT_MAX;
            }
            
            $cart_items[] = $product;
            $total += $product['subtotal'];
        }
    }
    
    return ['items' => $cart_items, 'total' => $total];
}

/**
 * Vider le panier
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}



/**
* Vérifier si l'utilisateur est admin
 */
function isAdmin() {
    global $pdo;
    
    // Vérifier d'abord si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    // Récupérer le rôle de l'utilisateur
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Retourner true si c'est un admin
    return $user && $user['role'] === 'admin';
}

/**
 * Rediriger vers une page
 */
function redirect($page) {
    header("Location: $page");
    exit();
}






/**
 * Vérifier le stock avant validation de commande
 */
function checkCartStock() {
    global $pdo;
    
    if (!$pdo) {
        return false;
    }
    
    foreach ($_SESSION['cart'] as $id => $item) {
        if (!empty($item['size'])) {
            $stmt = $pdo->prepare("SELECT stock FROM product_variants WHERE product_id = ? AND size = ?");
            $stmt->execute([$id, $item['size']]);
            $variant = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$variant || $variant['stock'] < $item['quantity']) {
                return false;
            }
        }
    }
    
    return true;
}







/**
 * Obtenir les infos de l'utilisateur connecté
 */
function getCurrentUser() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
?>