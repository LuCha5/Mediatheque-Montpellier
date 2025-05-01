<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

include('../../includes/header.php');

$message = '';
$message_type = '';

if (isset($_GET['suppression']) && $_GET['suppression'] == 'succes') {
    $message = "L'abonné a été supprimé avec succès.";
    $message_type = 'success';
} elseif (isset($_GET['erreur']) && $_GET['erreur'] == 'pret_en_cours') {
    $message = "Impossible de supprimer cet abonné car il a des prêts en cours.";
    $message_type = 'danger';
} elseif (isset($_GET['ajout']) && $_GET['ajout'] == 'succes') {
    $message = "L'abonné a été ajouté avec succès.";
    $message_type = 'success';
} elseif (isset($_GET['modif']) && $_GET['modif'] == 'succes') {
    $message = "L'abonné a été modifié avec succès.";
    $message_type = 'success';
}

try {
    $query = "SELECT id_abonne, nom, prenom, email, date_inscription, actif FROM abonnes ORDER BY nom, prenom";
    $stmt = $pdo->query($query);
    $abonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $abonnes = [];
    $message = "Erreur lors de la récupération des abonnés : " . $e->getMessage();
    $message_type = 'danger';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Abonnés</title>
</head>
<body>
<main class="container mt-4">
    <h1>Gestion des Abonnés</h1>

    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?>" role="alert">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <a href="ajouter.php" class="btn btn-primary mb-3">Ajouter un Abonné</a>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($abonnes) > 0): ?>
                    <?php foreach ($abonnes as $abonne): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($abonne['id_abonne']); ?></td>
                            <td><?php echo htmlspecialchars($abonne['nom']); ?></td>
                            <td><?php echo htmlspecialchars($abonne['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($abonne['email']); ?></td>
                            <td><?php echo $abonne['date_inscription'] ? htmlspecialchars(date('d/m/Y', strtotime($abonne['date_inscription']))) : 'N/A'; ?></td>
                            <td>
                                <?php if ($abonne['actif']): ?>
                                    <span class="badge bg-success">Actif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="modifier.php?id=<?php echo $abonne['id_abonne']; ?>" class="btn btn-sm btn-warning me-1">Modifier</a>
                                <a href="supprimer.php?id=<?php echo $abonne['id_abonne']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet abonné ?\nAttention : Cette action est irréversible.');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Aucun abonné trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
