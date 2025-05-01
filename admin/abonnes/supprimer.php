<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

$id_abonne = $_GET['id'] ?? null;

if ($id_abonne) {
    try {
        $queryCheckPret = "SELECT COUNT(*) FROM prets WHERE id_abonne = ? AND date_retour_reelle IS NULL";
        $stmtCheckPret = $pdo->prepare($queryCheckPret);
        $stmtCheckPret->execute([$id_abonne]);
        $nbPretsEnCours = $stmtCheckPret->fetchColumn();

        if ($nbPretsEnCours == 0) {
            $queryDelete = "DELETE FROM abonnes WHERE id_abonne = ?";
            $stmtDelete = $pdo->prepare($queryDelete);
            $stmtDelete->execute([$id_abonne]);

            header("Location: index.php?suppression=succes");
            exit;
        } else {
            header("Location: index.php?erreur=pret_en_cours");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erreur PDO lors de la suppression de l'abonné ID $id_abonne: " . $e->getMessage());
        header("Location: index.php?erreur=db");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}
?>
