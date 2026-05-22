<?php
include 'includes/config.php';
include 'includes/functions.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Veuillez remplir tous les champs obligatoires';
    } else {
        // Ici vous ajouterez la logique pour envoyer l'email
        $success = 'Votre message a été envoyé. Nous vous répondrons dans les plus brefs délais.';
    }
}

$page_title = "Contact - Hoodix";
?>
<?php include 'templates/header.html'; ?>

<section class="contact-section">
    <div class="container">
        
        <h1>Contactez-nous</h1>
        
        <div class="contact-grid">
            <div class="contact-info">
                <div class="info-card">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Adresse</h3>
                    <p>Boulevard Krim Belkacem<br>Bejaia, Algérie</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-phone"></i>
                    <h3>Téléphone</h3>
                    <p>+213 559 74 87 96</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@hoodix.com</p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-clock"></i>
                    <h3>Horaires</h3>
                    <p>Lun - Ven: 9h - 18h<br>Sam: 10h - 14h</p>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Envoyez-nous un message</h2>
                
                <?php if ($error): ?>
                <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="success-message"><?php echo $success; ?></div>
                <?php else: ?>
                
                <form method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Nom *</label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="subject">Sujet</label>
                        <input type="text" name="subject" id="subject">
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Message *</label>
                        <textarea name="message" id="message" rows="5" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-paper-plane"></i> Envoyer le message
                    </button>
                </form>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'templates/footer.html'; ?>