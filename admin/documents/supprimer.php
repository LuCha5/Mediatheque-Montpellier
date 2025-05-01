<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $queryCheckPret = "SELECT COUNT(*) FROM prets WHERE id_document = ? AND date_retour_reelle IS NULL";
    $stmtCheckPret = $pdo->prepare($queryCheckPret);
    $stmtCheckPret->execute([$id]);
    $nbPretsEnCours = $stmtCheckPret->fetchColumn();

    if ($nbPretsEnCours == 0) {
        $query = "DELETE FROM documents WHERE id_document = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        header("Location: index.php?suppression=succes");
        exit;
    } else {
        $erreur = "Impossible de supprimer ce document car il est actuellement prêté.";
    }

} else {
    $erreur = "Aucun identifiant de document fourni.";
}

include('../../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un Document</title>
</head>
<body>
    <h1>Supprimer un Document</h1>
    <?php if (isset($erreur)): ?>
        <p class="message error"><?php echo htmlspecialchars($erreur); ?></p>
        <p><a href="index.php">Retour à la liste des documents</a></p>
    <?php else: ?>
        <p>Une erreur inattendue s'est produite ou l'ID n'a pas été fourni.</p>
         <p><a href="index.php">Retour à la liste des documents</a></p>
    <?php endif; ?>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
