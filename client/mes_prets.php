<?php
include('../config.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header('Location: ../auth/login.php?erreur=acces_refuse');
    exit;
}
if ($_SESSION['user_role'] !== 'client') {
    header('Location: ../index.php');
    exit;
}

$id_abonne = $_SESSION['user_id'];

$mes_prets = [];
$historique_prets = [];

try {
    $query = "SELECT d.titre, d.auteur, p.date_pret, p.date_retour_prevue
              FROM prets p
              JOIN documents d ON p.id_document = d.id_document
              WHERE p.id_abonne = ? AND p.date_retour_reelle IS NULL
              ORDER BY p.date_retour_prevue ASC";
    $stmt = $pdo->prepare($query);
    if ($stmt && $stmt->execute([$id_abonne])) {
        $mes_prets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        error_log("Erreur PDO (prêts en cours) dans mes_prets.php");
    }

    $queryHistorique = "SELECT d.titre, d.auteur, p.date_pret, p.date_retour_prevue, p.date_retour_reelle
                        FROM prets p
                        JOIN documents d ON p.id_document = d.id_document
                        WHERE p.id_abonne = ? AND p.date_retour_reelle IS NOT NULL
                        ORDER BY p.date_retour_reelle DESC";
    $stmtHistorique = $pdo->prepare($queryHistorique);
    if ($stmtHistorique && $stmtHistorique->execute([$id_abonne])) {
        $historique_prets = $stmtHistorique->fetchAll(PDO::FETCH_ASSOC);
    } else {
         error_log("Erreur PDO (historique prêts) dans mes_prets.php");
    }

} catch (PDOException $e) {
    error_log("PDOException dans mes_prets.php: " . $e->getMessage());
}

include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Prêts - La Maison du Livre</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h1>Mes Prêts en cours</h1>

    <?php if (count($mes_prets) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Date du Prêt</th>
                    <th>Date Retour Prévue</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $aujourdhui = new DateTime();
                foreach ($mes_prets as $pret):
                    $dateRetourPrevue = new DateTime($pret['date_retour_prevue']);
                    $diff = $aujourdhui->diff($dateRetourPrevue);
                    $joursRestants = (int)$diff->format('%r%a');

                    $classeDate = '';
                    $messageDate = '';

                    if ($joursRestants < 0) {
                        $classeDate = 'date-alerte-rouge';
                        $messageDate = ' (Dépassé)';
                    } elseif ($joursRestants <= 7) {
                        $classeDate = 'date-alerte-orange';
                        $messageDate = ' (Bientôt)';
                    } else { 
                        $classeDate = 'date-ok-vert';
                        $messageDate = ' (Vous avez encore le temps)';
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pret['titre']); ?></td>
                        <td><?php echo htmlspecialchars($pret['auteur']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_pret']))); ?></td>
                        <td class="<?php echo $classeDate; ?>">
                            <?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_retour_prevue']))); ?>
                            <?php echo $messageDate; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez aucun document emprunté actuellement.</p>
        <p><a href="catalogue.php">Consulter le catalogue</a> pour faire une réservation.</p>
    <?php endif; ?>

    <h2>Historique de mes prêts</h2>
     <?php if (count($historique_prets) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Auteur</th>
                    <th>Date du Prêt</th>
                    <th>Date Retour Prévue</th>
                    <th>Date Retour Réelle</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique_prets as $pret): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($pret['titre']); ?></td>
                        <td><?php echo htmlspecialchars($pret['auteur']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_pret']))); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_retour_prevue']))); ?></td>
                         <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($pret['date_retour_reelle']))); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Vous n'avez pas encore d'historique de prêts.</p>
    <?php endif; ?>


</body>
<?php include('../includes/footer.php'); ?>
</html>
