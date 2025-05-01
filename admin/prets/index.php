<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

include('../../includes/header.php');

$message_succes = '';
if (isset($_GET['retour']) && $_GET['retour'] == 'succes' && isset($_GET['message'])) {
    $message_succes = htmlspecialchars(urldecode($_GET['message']));
} elseif (isset($_GET['ajout']) && $_GET['ajout'] == 'succes') {
    $message_succes = "Le prêt a été ajouté avec succès.";
}

$query = "SELECT p.id_pret, a.nom AS abonne_nom, a.prenom AS abonne_prenom, d.titre AS document_titre, p.date_pret, p.date_retour_prevue
          FROM prets p
          JOIN abonnes a ON p.id_abonne = a.id_abonne
          JOIN documents d ON p.id_document = d.id_document
          WHERE p.date_retour_reelle IS NULL
          ORDER BY p.date_retour_prevue ASC";
$stmt = $pdo->query($query);
$prets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Prêts</title>
    <style>
        
    </style>
</head>
<body>
<main class="container mt-4">
    <h1>Gestion des Prêts en cours</h1>

    <?php if ($message_succes): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $message_succes; ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>Abonné</th>
                    <th>Document</th>
                    <th>Date du Prêt</th>
                    <th>Date Retour Prévue</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($prets) > 0): ?>
                    <?php foreach ($prets as $pret): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($pret['abonne_prenom'] . ' ' . $pret['abonne_nom']); ?></td>
                            <td><?php echo htmlspecialchars($pret['document_titre']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_pret']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_retour_prevue']))); ?></td>
                            <td>
                                <a href="retourner.php?id_pret=<?php echo $pret['id_pret']; ?>" class="btn btn-sm btn-info" onclick="return confirm('Confirmer le retour de ce document ?');">Marquer comme Retourné</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Aucun prêt en cours.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="ajouter.php" class="btn btn-primary mt-3">Ajouter un Prêt</a>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
