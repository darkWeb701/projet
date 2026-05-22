<?php
// register.php
include 'includes/config.php';
include 'includes/functions.php';

$page_title = "Inscription - Hoodix";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Nom d'utilisateur requis";
    } elseif (strlen($username) < 3) {
        $errors[] = "Nom d'utilisateur trop court (min 3 caractères)";
    }
    
    if (empty($email)) {
        $errors[] = "Email requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }
    
    if (empty($password)) {
        $errors[] = "Mot de passe requis";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mot de passe trop court (min 6 caractères)";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    // Vérifier si l'utilisateur existe déjà
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors[] = "Cet utilisateur ou email existe déjà";
        }
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if (empty($errors)) {
        try {
            // Hasher le mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insérer l'utilisateur (sans prénom, nom, téléphone)
            $sql = "INSERT INTO users (username, email, password, role) 
                    VALUES (:username, :email, :password, 'user')";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password
            ]);
            
            // Connexion automatique après inscription
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            
            // Rediriger vers la page d'accueil
            header("Location: index.php?register=success");
            exit();
            
        } catch(PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<?php include 'templates/header.html'; ?>

<section class="register-section">
    <div class="container">
        <div class="register-container">
            <h1>Créer un compte</h1>
            <p>Inscrivez-vous pour passer vos commandes plus facilement</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" class="register-form">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur *</label>
                    <input type="text" name="username" id="username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le mot de passe *</label>
                        <input type="password" name="confirm_password" id="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn-register">S'inscrire</button>
                
                <p class="login-link">
                    Déjà un compte ? <a href="login.php">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
</section>

<style>
.register-section {
    padding: 60px 0;
    min-height: 70vh;
    background: #f5f5f5;
}

.register-container {
    max-width: 500px;
    margin: 0 auto;
    background: white;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
}

.register-container h1 {
    text-align: center;
    margin-bottom: 10px;
}

.register-container p {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
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

.form-group input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.btn-register {
    width: 100%;
    background: #e63946;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    margin-top: 10px;
}

.btn-register:hover {
    background: #c82333;
}

.login-link {
    text-align: center;
    margin-top: 20px;
}

.login-link a {
    color: #e63946;
    text-decoration: none;
}

.alert {
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>

<?php include 'templates/footer.html'; ?>