<?php
// includes/config.php

// SESSION SAFE
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB CONFIG
define('DB_HOST', $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? 'localhost');
define('DB_NAME', $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? 'railway');
define('DB_USER', $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? 'root');
define('DB_PASS', $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? '');
define('DB_PORT', (int)($_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? 3306));

// CONNEXION PDO
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Erreur connexion base de données: " . $e->getMessage());
}

// INIT PANIER
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
