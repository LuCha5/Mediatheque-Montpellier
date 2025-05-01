<?php
include('../config.php');

$livres_disponibles = [];

try {
    $query = "SELECT id_document, titre, auteur, genre, date_parution FROM documents WHERE type = 'livre' AND disponible = TRUE ORDER BY titre";
    $stmt = $pdo->query($query);

    if ($stmt) {
        $livres_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        error_log("Erreur PDO (sans exception) dans catalogue.php");
    }
} catch (PDOException $e) {
    error_log("PDOException dans catalogue.php: " . $e->getMessage());
}

include('../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue des Livres Disponibles</title>
    <style>
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-footer {
             background-color: rgba(0,0,0,.03);
             border-top: 1px solid rgba(0,0,0,.125);
             text-align: center;
        }
    </style>
</head>
<body>
<main class="container mt-4">
    <h1>Catalogue des Livres Disponibles</h1>

    <?php if (isset($_GET['reserve']) && $_GET['reserve'] == 'succes'): ?>
        <div class="alert alert-success" role="alert">
            Le livre "<?php echo htmlspecialchars($_GET['titre'] ?? ''); ?>" a été réservé avec succès ! Vous pouvez le voir dans <a href="mes_prets.php" class="alert-link">Mes Prêts</a>.
        </div>
    <?php elseif (isset($_GET['erreur'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php
            $erreur_msg = "Une erreur est survenue.";
            switch ($_GET['erreur']) {
                case 'non_connecte': $erreur_msg = "Vous devez être connecté pour réserver."; break;
                case 'non_client': $erreur_msg = "Seuls les clients peuvent réserver."; break;
                case 'id_manquant': $erreur_msg = "L'identifiant du livre est manquant."; break;
                case 'indisponible': $erreur_msg = "Ce livre n'est plus disponible."; break;
                case 'deja_prete': $erreur_msg = "Vous avez déjà emprunté ce livre."; break;
                case 'limite_atteinte': $erreur_msg = "Vous avez atteint votre limite de prêts."; break;
                case 'db': $erreur_msg = "Erreur lors de la réservation. Veuillez réessayer."; break;
            }
            echo htmlspecialchars($erreur_msg);
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <?php if (count($livres_disponibles) > 0): ?>
            <?php foreach ($livres_disponibles as $livre): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title"><?php echo htmlspecialchars($livre['titre']); ?></h5>
                                <p class="card-text mb-1"><small class="text-muted">Auteur : <?php echo htmlspecialchars($livre['auteur']); ?></small></p>
                                <p class="card-text mb-1"><small>Genre : <?php echo htmlspecialchars($livre['genre']); ?></small></p>
                                <p class="card-text"><small>Parution : <?php echo $livre['date_parution'] ? htmlspecialchars(date('d/m/Y', strtotime($livre['date_parution']))) : 'N/A'; ?></small></p>
                            </div>
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'client'): ?>
                                <div class="mt-auto text-center">
                                    <a href="reserver.php?id=<?php echo $livre['id_document']; ?>"
                                       class="btn btn-success btn-sm reserve-button"
                                       onclick="return confirm('Confirmer la réservation de \'<?php echo addslashes(htmlspecialchars($livre['titre'])); ?>\' ?');">
                                        Réserver
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col">
                <p class="alert alert-info">Aucun livre disponible pour le moment ou erreur lors de la récupération.</p>
            </div>
        <?php endif; ?>
    </div>

</main>
</body>
<?php include('../includes/footer.php'); ?>
</html>
