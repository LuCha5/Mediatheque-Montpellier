<?php
include('../config.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header('Location: catalogue.php?erreur=non_connecte');
    exit;
}
if ($_SESSION['user_role'] !== 'client') {
    header('Location: catalogue.php?erreur=non_client');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: catalogue.php?erreur=id_manquant');
    exit;
}

$id_document = $_GET['id'];
$id_abonne = $_SESSION['user_id'];
$document = null;

try {
    $queryDoc = "SELECT id_document, titre, disponible FROM documents WHERE id_document = ?";
    $stmtDoc = $pdo->prepare($queryDoc);
    $stmtDoc->execute([$id_document]);
    $document = $stmtDoc->fetch(PDO::FETCH_ASSOC);

    if (!$document || !$document['disponible']) {
        header('Location: catalogue.php?erreur=indisponible');
        exit;
    }

    $pdo->beginTransaction();

    $date_pret = date('Y-m-d');
    $date_retour_prevue = date('Y-m-d', strtotime($date_pret . ' + 3 weeks'));
    $queryInsertPret = "INSERT INTO prets (id_abonne, id_document, date_pret, date_retour_prevue) VALUES (?, ?, ?, ?)";
    $stmtInsertPret = $pdo->prepare($queryInsertPret);
    $insertOk = $stmtInsertPret->execute([$id_abonne, $id_document, $date_pret, $date_retour_prevue]);

    $queryUpdateDoc = "UPDATE documents SET disponible = FALSE WHERE id_document = ?";
    $stmtUpdateDoc = $pdo->prepare($queryUpdateDoc);
    $updateOk = $stmtUpdateDoc->execute([$id_document]);

    if ($insertOk && $updateOk) {
        $pdo->commit();
        header('Location: catalogue.php?reserve=succes&titre=' . urlencode($document['titre']));
        exit;
    } else {
        $pdo->rollBack();
        error_log("Erreur lors de l'insertion du prêt ou de la MAJ du document pour doc ID: $id_document");
        header('Location: catalogue.php?erreur=db');
        exit;
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("PDOException dans reserver.php: " . $e->getMessage());
    header('Location: catalogue.php?erreur=db');
    exit;
}
?>
