<?php
include('../../config.php');

// --- Vérification Accès Admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}
// --- Fin Vérification ---

$id_contentieux = $_GET['id'] ?? null;

if ($id_contentieux) {
    try {
        $query = "UPDATE contentieux SET resolu = TRUE WHERE id_contentieux = ?";
        $stmt = $pdo->prepare($query);
        $success = $stmt->execute([$id_contentieux]);

        if ($success && $stmt->rowCount() > 0) {
            header("Location: index.php?resolu=succes");
            exit;
        } elseif ($success && $stmt->rowCount() == 0) {
             header("Location: index.php?erreur=not_found"); // ID non trouvé ou déjà résolu
             exit;
        } else {
            header("Location: index.php?erreur=db");
            exit;
        }
    } catch (PDOException $e) {
        error_log("PDOException in admin/contentieux/resoudre.php: " . $e->getMessage());
        header("Location: index.php?erreur=db");
        exit;
    }
} else {
    // Rediriger si aucun ID n'est fourni
    header("Location: index.php");
    exit;
}
?>
