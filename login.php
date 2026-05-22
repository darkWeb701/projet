<?php
// login.php
include 'includes/config.php';
include 'includes/functions.php';

$page_title = "Connexion - Hoodix";
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Chercher l'utilisateur
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                header("Location: admin_orders.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Identifiants incorrects";
        }
    }
}
?>

<?php include 'templates/header.html'; ?>

<section class="login-section">
    <div class="container">
        <div class="login-container">
            <h1>Connexion</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur ou Email</label>
                    <input type="text" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <button type="submit" class="btn-login">Se connecter</button>
                
                <p class="register-link">
                    Pas encore de compte ? <a href="register.php">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>
</section>

<style>
.login-section {
    padding: 60px 0;
    min-height: 70vh;
    background: #f5f5f5;
}

.login-container {
    max-width: 450px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
}

.login-container h1 {
    text-align: center;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.btn-login {
    width: 100%;
    background: #e63946;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
}

.btn-login:hover {
    background: #e63946;
}

.register-link {
    text-align: center;
    margin-top: 20px;
}

.register-link a {
    color: #e63946;
    text-decoration: none;
}
</style>

<?php include 'templates/footer.html'; ?>