<?php
include('../config.php');
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$erreur = '';
if (isset($_GET['erreur'])) {
    if ($_GET['erreur'] == 'identifiants') {
        $erreur = "Email ou mot de passe incorrect.";
    } elseif ($_GET['erreur'] == 'inactif') {
         $erreur = "Votre compte abonné est inactif.";
    } elseif ($_GET['erreur'] == 'acces_refuse') {
         $erreur = "Accès refusé. Vous devez être administrateur.";
    } else {
        $erreur = "Une erreur s'est produite.";
    }
}
if (isset($_GET['logout']) && $_GET['logout'] == 'succes') {
    $message = "Vous avez été déconnecté avec succès.";
}

include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - La Maison du Livre</title>
    <style>
        
    </style>
</head>
<body>
    <main>
        <div class="login-container">
            <h1>Connexion</h1>

            <?php if ($erreur): ?>
                <p class="error-message"><?php echo htmlspecialchars($erreur); ?></p>
            <?php endif; ?>
             <?php if (isset($message)): ?>
                <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <form action="process_login.php" method="POST">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>

                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>

                <input type="submit" value="Se connecter">
            </form>
            <!-- <p style="text-align:center; margin-top:15px;"><a href="inscription.php">Pas encore de compte ?</a></p> -->
        </div>
    </main>
</body>
<?php include('../includes/footer.php'); ?>
</html>
