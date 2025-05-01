<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

$erreur = '';
$succes = '';
$id_abonne = null;
$id_document = null;
$date_pret = date('Y-m-d');
$abonnes = [];
$documents_disponibles = [];

try {
    $stmtAbonnes = $pdo->query("SELECT id_abonne, nom, prenom FROM abonnes WHERE actif = TRUE ORDER BY nom, prenom");
    $abonnes = $stmtAbonnes->fetchAll(PDO::FETCH_ASSOC);

    $stmtDocs = $pdo->query("SELECT id_document, titre, auteur FROM documents WHERE disponible = TRUE ORDER BY titre");
    $documents_disponibles = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération des données : " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_abonne = $_POST['id_abonne'] ?? null;
    $id_document = $_POST['id_document'] ?? null;
    $date_pret_post = $_POST['date_pret'] ?? $date_pret;

    if (empty($id_abonne) || empty($id_document) || empty($date_pret_post)) {
        $erreur = "Veuillez sélectionner un abonné, un document et une date de prêt.";
    } else {
        $date_retour_prevue = date('Y-m-d', strtotime($date_pret_post . ' + 3 weeks'));

        try {
            $pdo->beginTransaction();

            $stmtCheckDoc = $pdo->prepare("SELECT disponible FROM documents WHERE id_document = ? FOR UPDATE");
            $stmtCheckDoc->execute([$id_document]);
            $document = $stmtCheckDoc->fetch(PDO::FETCH_ASSOC);

            if ($document && $document['disponible']) {
                $queryInsertPret = "INSERT INTO prets (id_abonne, id_document, date_pret, date_retour_prevue) VALUES (?, ?, ?, ?)";
                $stmtInsertPret = $pdo->prepare($queryInsertPret);
                $insertOk = $stmtInsertPret->execute([$id_abonne, $id_document, $date_pret_post, $date_retour_prevue]);

                $queryUpdateDoc = "UPDATE documents SET disponible = FALSE WHERE id_document = ?";
                $stmtUpdateDoc = $pdo->prepare($queryUpdateDoc);
                $updateOk = $stmtUpdateDoc->execute([$id_document]);

                if ($insertOk && $updateOk) {
                    $pdo->commit();
                    header("Location: index.php?ajout=succes");
                    exit;
                } else {
                    $pdo->rollBack();
                    $erreur = "Erreur lors de l'enregistrement du prêt.";
                    error_log("Erreur ajout prêt manuel. InsertOK: $insertOk, UpdateOK: $updateOk");
                }
            } else {
                $pdo->rollBack();
                $erreur = "Le document sélectionné n'est plus disponible ou n'existe pas.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erreur = "Erreur base de données : " . $e->getMessage();
            error_log("PDOException ajout prêt manuel: " . $e->getMessage());
        }
    }
}

include('../../includes/header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Prêt</title>
     <style>
        form { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9; }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        select, input[type="date"], input[type="submit"] { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { background-color: #28a745; color: white; cursor: pointer; }
        input[type="submit"]:hover { background-color: #218838; }
        .error-message { color: #dc3545; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
    </style>
</head>
<body>
<main class="container mt-4">
    <h1>Ajouter un Nouveau Prêt</h1>

    <?php if ($erreur): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erreur); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="ajouter.php">
        <div class="mb-3">
            <label for="id_abonne" class="form-label">Abonné <span class="text-danger">*</span></label>
            <select class="form-select" id="id_abonne" name="id_abonne" required>
                <option value="">-- Sélectionner un abonné --</option>
                <?php foreach ($abonnes as $abo): ?>
                    <option value="<?php echo $abo['id_abonne']; ?>" <?php echo ($id_abonne == $abo['id_abonne'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($abo['prenom'] . ' ' . $abo['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_document" class="form-label">Document Disponible <span class="text-danger">*</span></label>
            <select class="form-select" id="id_document" name="id_document" required>
                <option value="">-- Sélectionner un document --</option>
                <?php foreach ($documents_disponibles as $doc): ?>
                    <option value="<?php echo $doc['id_document']; ?>" <?php echo ($id_document == $doc['id_document'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($doc['titre'] . ' - ' . $doc['auteur']); ?>
                    </option>
                <?php endforeach; ?>
                 <?php if (empty($documents_disponibles) && empty($erreur)): ?>
                    <option value="" disabled>Aucun document disponible</option>
                <?php endif; ?>
            </select>
        </div>

         <div class="mb-3">
            <label for="date_pret" class="form-label">Date du Prêt <span class="text-danger">*</span></label>
            <input type="date" class="form-control" id="date_pret" name="date_pret" value="<?php echo htmlspecialchars($date_pret); ?>" required>
        </div>

        <button type="submit" class="btn btn-success">Ajouter le Prêt</button>
    </form>

    <p style="text-align: center; margin-top: 20px;"><a href="index.php" class="btn btn-secondary">Retour à la liste des prêts</a></p>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
