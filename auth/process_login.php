<?php
include('../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $mot_de_passe_saisi = $_POST['mot_de_passe'] ?? '';

    if (empty($email) || empty($mot_de_passe_saisi)) {
        header('Location: login.php?erreur=identifiants');
        exit;
    }

    $queryEmploye = "SELECT id_employe, prenom, nom, email, mot_de_passe FROM employes WHERE email = ?";
    $stmtEmploye = $pdo->prepare($queryEmploye);
    $stmtEmploye->execute([$email]);
    $employe = $stmtEmploye->fetch(PDO::FETCH_ASSOC);

    if ($employe && $mot_de_passe_saisi === $employe['mot_de_passe']) {
        $_SESSION['user_id'] = $employe['id_employe'];
        $_SESSION['user_role'] = 'admin';
        $_SESSION['user_prenom'] = $employe['prenom'];
        $_SESSION['user_nom'] = $employe['nom'];
        header('Location: ../index.php');
        exit;
    }

    $queryAbonne = "SELECT id_abonne, prenom, nom, email, mot_de_passe, actif FROM abonnes WHERE email = ?";
    $stmtAbonne = $pdo->prepare($queryAbonne);
    $stmtAbonne->execute([$email]);
    $abonne = $stmtAbonne->fetch(PDO::FETCH_ASSOC);

    if ($abonne && $mot_de_passe_saisi === $abonne['mot_de_passe']) {
        if ($abonne['actif']) {
            $_SESSION['user_id'] = $abonne['id_abonne'];
            $_SESSION['user_role'] = 'client';
            $_SESSION['user_prenom'] = $abonne['prenom'];
            $_SESSION['user_nom'] = $abonne['nom'];
            header('Location: ../index.php');
            exit;
        } else {
            header('Location: login.php?erreur=inactif');
            exit;
        }
    }

    header('Location: login.php?erreur=identifiants');
    exit;

} else {
    header('Location: login.php');
    exit;
}
?>
