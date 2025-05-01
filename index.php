<?php
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

include('includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Maison du Livre - Accueil</title>
    <style>
        .dashboard-links ul { list-style: none; padding: 0; }
        .dashboard-links li { margin: 10px 0; }
        .dashboard-links a { text-decoration: none; font-size: 1.1em; color: #0066cc; }
        .dashboard-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main>
        <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> !</h1>

        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <h2>Tableau de bord Administrateur</h2>
            <p>Que souhaitez-vous faire aujourd'hui ?</p>
            <nav class="dashboard-links">
                <ul>
                    <li><a href="admin/documents/index.php">📚 Gérer les Documents</a></li>
                    <li><a href="admin/abonnes/index.php">👤 Gérer les Abonnés</a></li>
                    <li><a href="admin/prets/index.php">🔄 Gérer les Prêts</a></li>
                    <li><a href="admin/contentieux/index.php">⚠️ Gérer les Contentieux</a></li>
                </ul>
            </nav>

        <?php elseif ($_SESSION['user_role'] === 'client'): ?>
            <h2>Espace Abonné</h2>
            <p>Consultez le catalogue ou gérez vos prêts.</p>
             <nav class="dashboard-links">
                <ul>
                    <li><a href="client/catalogue.php">📖 Consulter le Catalogue</a></li>
                    <li><a href="client/mes_prets.php"> 📃 Mes Prêts</a></li>
                </ul>
            </nav>

        <?php else: ?>
            <p>Votre rôle n'est pas défini. Veuillez contacter l'administrateur.</p>
        <?php endif; ?>

    </main>
</body>
<?php include('includes/footer.php'); ?>
</html>
