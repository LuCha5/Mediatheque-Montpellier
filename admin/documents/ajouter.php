<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header("Location: index.php");
    exit;
}

include('../../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Document</title>
</head>
<body>
    <h1>Ajouter un Document</h1>
    <form method="POST">
        <input type="submit" value="Ajouter">
    </form>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
