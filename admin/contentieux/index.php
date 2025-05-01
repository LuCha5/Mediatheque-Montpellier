<?php
include('../../config.php');

// --- Vérification Accès Admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}
// --- Fin Vérification ---

include('../../includes/header.php');

$message_succes = '';
if (isset($_GET['resolu']) && $_GET['resolu'] == 'succes') {
    $message_succes = "Le contentieux a été marqué comme résolu.";
}
$message_erreur = '';
if (isset($_GET['erreur'])) {
    $message_erreur = "Une erreur est survenue.";
    if ($_GET['erreur'] == 'db') {
        $message_erreur = "Erreur lors de la mise à jour du contentieux.";
    } elseif ($_GET['erreur'] == 'not_found') {
         $message_erreur = "Contentieux non trouvé.";
    }
}

$contentieux_list = [];
try {
    // Jointures pour récupérer les informations nécessaires
    $query = "SELECT c.id_contentieux, c.motif, c.montant_penalite, c.date_creation, c.resolu,
                     p.id_pret,
                     a.nom AS abonne_nom, a.prenom AS abonne_prenom,
                     d.titre AS document_titre,
                     e.nom AS employe_nom, e.prenom AS employe_prenom
              FROM contentieux c
              LEFT JOIN prets p ON c.id_pret = p.id_pret
              LEFT JOIN abonnes a ON p.id_abonne = a.id_abonne
              LEFT JOIN documents d ON p.id_document = d.id_document
              LEFT JOIN employes e ON c.id_employe = e.id_employe
              ORDER BY c.date_creation DESC, c.resolu ASC"; // Afficher les non résolus en premier
    $stmt = $pdo->query($query);
    $contentieux_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message_erreur = "Erreur lors de la récupération des contentieux : " . $e->getMessage();
    error_log("PDOException in admin/contentieux/index.php: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Contentieux</title>
</head>
<body>
<main class="container mt-4">
    <h1>Gestion des Contentieux</h1>

    <?php if ($message_succes): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($message_succes); ?>
        </div>
    <?php endif; ?>
    <?php if ($message_erreur): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($message_erreur); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Abonné</th>
                    <th>Document</th>
                    <th>Motif</th>
                    <th>Pénalité (€)</th>
                    <th>Date Création</th>
                    <th>Employé</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($contentieux_list) > 0): ?>
                    <?php foreach ($contentieux_list as $contentieux): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($contentieux['id_contentieux']); ?></td>
                            <td><?php echo htmlspecialchars($contentieux['abonne_prenom'] . ' ' . $contentieux['abonne_nom']); ?></td>
                            <td><?php echo htmlspecialchars($contentieux['document_titre'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($contentieux['motif']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($contentieux['montant_penalite'], 2, ',', ' ')); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($contentieux['date_creation']))); ?></td>
                            <td><?php echo htmlspecialchars($contentieux['employe_prenom'] . ' ' . $contentieux['employe_nom']); ?></td>
                            <td>
                                <?php if ($contentieux['resolu']): ?>
                                    <span class="badge bg-success">Résolu</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">En cours</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!$contentieux['resolu']): ?>
                                    <a href="resoudre.php?id=<?php echo $contentieux['id_contentieux']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Confirmer la résolution de ce contentieux ?');">Marquer Résolu</a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Aucun contentieux trouvé.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
     <a href="/maison_du_livre/index.php" class="btn btn-secondary mt-3">Retour au tableau de bord</a>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
