<?php
include('../../config.php');

// --- Vérification Accès Admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}
// --- Fin Vérification ---

$erreur = null;
$pretInfo = null;
$id_employe_session = $_SESSION['user_id'] ?? null; // Récupérer l'ID de l'employé connecté

if (isset($_GET['id_pret'])) {
    $id_pret = $_GET['id_pret'];
    $date_retour = date('Y-m-d'); // Date actuelle pour le retour

    try {
        // Récupérer l'ID du document ET la date de retour prévue
        $queryDocId = "SELECT id_document, date_retour_prevue FROM prets WHERE id_pret = ? AND date_retour_reelle IS NULL";
        $stmtDocId = $pdo->prepare($queryDocId);

        if ($stmtDocId && $stmtDocId->execute([$id_pret])) {
            $pretInfo = $stmtDocId->fetch(PDO::FETCH_ASSOC);

            if ($pretInfo) {
                $id_document = $pretInfo['id_document'];
                $date_retour_prevue = $pretInfo['date_retour_prevue'];

                $pdo->beginTransaction();

                // 1. Mettre à jour la date de retour réelle dans la table prets
                $queryRetour = "UPDATE prets SET date_retour_reelle = ? WHERE id_pret = ?";
                $stmtRetour = $pdo->prepare($queryRetour);
                $retourOk = $stmtRetour->execute([$date_retour, $id_pret]);

                // 2. Rendre le document à nouveau disponible
                $queryDispo = "UPDATE documents SET disponible = TRUE WHERE id_document = ?";
                $stmtDispo = $pdo->prepare($queryDispo);
                $dispoOk = $stmtDispo->execute([$id_document]);

                $contentieuxOk = true; // Supposer que tout va bien par défaut pour le contentieux

                // 3. Vérifier si le retour est en retard
                if ($retourOk && $dispoOk && $date_retour > $date_retour_prevue) {
                    // Le retour est en retard, créer une entrée dans contentieux
                    $motif_retard = "Retour en retard.";
                    $penalite_retard = 5.00; // Montant fixe ou à calculer

                    $queryContentieux = "INSERT INTO contentieux (id_pret, id_employe, motif, montant_penalite, date_creation, resolu)
                                         VALUES (?, ?, ?, ?, ?, ?)";
                    $stmtContentieux = $pdo->prepare($queryContentieux);
                    $contentieuxOk = $stmtContentieux->execute([
                        $id_pret,
                        $id_employe_session, // Utiliser l'ID de l'employé connecté
                        $motif_retard,
                        $penalite_retard,
                        $date_retour, // Date de création du contentieux = date de retour
                        0 // Non résolu par défaut
                    ]);
                    if (!$contentieuxOk) {
                         error_log("Erreur lors de la création de l'entrée contentieux pour prêt ID: $id_pret");
                    }
                }

                // Valider la transaction si tout s'est bien passé (retour, dispo, et contentieux si nécessaire)
                if ($retourOk && $dispoOk && $contentieuxOk) {
                    $pdo->commit();
                    $message_succes = "Le document a été marqué comme retourné avec succès.";
                    if ($date_retour > $date_retour_prevue) {
                        $message_succes .= " Un contentieux pour retard a été créé.";
                    }
                    header("Location: index.php?retour=succes&message=" . urlencode($message_succes));
                    exit;
                } else {
                    $pdo->rollBack();
                    $erreur = "Erreur lors de la mise à jour de la base de données (retour, disponibilité ou contentieux).";
                    error_log("Erreur MAJ retour prêt ID: $id_pret. RetourOK: $retourOk, DispoOK: $dispoOk, ContentieuxOK: $contentieuxOk");
                }

            } else {
                $erreur = "Prêt non trouvé ou déjà retourné (ID: " . htmlspecialchars($id_pret) . ").";
            }
        } else {
            $erreur = "Erreur lors de la récupération des informations du prêt.";
            $errorInfo = $stmtDocId ? $stmtDocId->errorInfo() : $pdo->errorInfo();
            error_log("Erreur PDO select prêt: " . implode(":", $errorInfo));
        }
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $erreur = "Erreur base de données. Veuillez contacter l'administrateur.";
        error_log("PDOException in retourner.php: " . $e->getMessage());
    }
} else {
    $erreur = "Aucun identifiant de prêt fourni.";
}

// Afficher la page d'erreur
include('../../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Retour de Prêt - Erreur</title>
</head>
<body>
    <main class="container mt-4">
        <h1>Erreur lors du Retour de Prêt</h1>
        <?php if ($erreur !== null): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php else: ?>
             <div class="alert alert-warning" role="alert">
                Une condition inattendue s'est produite.
            </div>
         <?php endif; ?>
        <p><a href="index.php" class="btn btn-secondary">Retour à la liste des prêts</a></p>
    </main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
