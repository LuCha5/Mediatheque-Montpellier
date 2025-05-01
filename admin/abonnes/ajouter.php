<?php
include('../../config.php');

// --- Vérification Accès Admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}
// --- Fin Vérification ---

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $date_naissance = trim($_POST['date_naissance'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $ville = trim($_POST['ville'] ?? '');
    $code_postal = trim($_POST['code_postal'] ?? '');
    $tel = trim($_POST['tel'] ?? '');
    $date_inscription = date('Y-m-d');
    $actif = isset($_POST['actif']) ? 1 : 0;

    if (empty($nom) || empty($prenom) || empty($email)) {
        $erreur = "Le nom, le prénom et l'email sont obligatoires.";
    } else {
        try {
            $checkEmail = $pdo->prepare("SELECT id_abonne FROM abonnes WHERE email = ?");
            $checkEmail->execute([$email]);
            if ($checkEmail->fetch()) {
                $erreur = "Cette adresse email est déjà utilisée par un autre abonné.";
            } else {
                $query = "INSERT INTO abonnes (nom, prenom, date_naissance, email, adresse, mot_de_passe, ville, code_postal, tel, date_inscription, actif)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$nom, $prenom, $date_naissance ?: null, $email, $adresse, $mot_de_passe ?: null, $ville, $code_postal, $tel, $date_inscription, $actif]);

                header("Location: index.php?ajout=succes");
                exit;
            }
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'ajout de l'abonné : " . $e->getMessage();
        }
    }
}

include('../../includes/header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Abonné</title>
</head>
<body>
<main class="container mt-4">
    <h1>Ajouter un Nouvel Abonné</h1>

    <?php if ($erreur): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erreur); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="ajouter.php">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo htmlspecialchars($_POST['nom'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($_POST['prenom'] ?? ''); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
             <div class="col-md-6 mb-3">
                <label for="date_naissance" class="form-label">Date de Naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($_POST['date_naissance'] ?? ''); ?>">
            </div>
        </div>
         <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($_POST['adresse'] ?? ''); ?></textarea>
        </div>
         <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?php echo htmlspecialchars($_POST['ville'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="code_postal" class="form-label">Code Postal</label>
                <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($_POST['code_postal'] ?? ''); ?>">
            </div>
        </div>
        <div class="row">
             <div class="col-md-6 mb-3">
                <label for="tel" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="tel" name="tel" value="<?php echo htmlspecialchars($_POST['tel'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" aria-describedby="passwordHelp">
            </div>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" <?php echo (isset($_POST['actif']) || !$_POST) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="actif">
                Compte Actif
            </label>
        </div>

        <button type="submit" class="btn btn-success">Ajouter l'Abonné</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
