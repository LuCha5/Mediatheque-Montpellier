<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

$erreur = '';
$succes = '';
$abonne = null;
$id_abonne = $_GET['id'] ?? null;

if (!$id_abonne) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM abonnes WHERE id_abonne = ?");
    $stmt->execute([$id_abonne]);
    $abonne = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$abonne) {
        header('Location: index.php?erreur=not_found');
        exit;
    }
} catch (PDOException $e) {
    $erreur = "Erreur lors de la récupération de l'abonné : " . $e->getMessage();
}

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
    $actif = isset($_POST['actif']) ? 1 : 0;

    if (empty($nom) || empty($prenom) || empty($email)) {
        $erreur = "Le nom, le prénom et l'email sont obligatoires.";
    } else {
        try {
            $checkEmail = $pdo->prepare("SELECT id_abonne FROM abonnes WHERE email = ? AND id_abonne != ?");
            $checkEmail->execute([$email, $id_abonne]);
            if ($checkEmail->fetch()) {
                $erreur = "Cette adresse email est déjà utilisée par un autre abonné.";
            } else {
                $params = [$nom, $prenom, $date_naissance ?: null, $email, $adresse, $ville, $code_postal, $tel, $actif];
                $sql = "UPDATE abonnes SET nom = ?, prenom = ?, date_naissance = ?, email = ?, adresse = ?, ville = ?, code_postal = ?, tel = ?, actif = ?";

                if (!empty($mot_de_passe)) {
                    $sql .= ", mot_de_passe = ?";
                    $params[] = $mot_de_passe;
                }

                $sql .= " WHERE id_abonne = ?";
                $params[] = $id_abonne;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                header("Location: index.php?modif=succes");
                exit;
            }
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la modification de l'abonné : " . $e->getMessage();
            $abonne = $_POST;
            $abonne['id_abonne'] = $id_abonne;
        }
    }
}

include('../../includes/header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Abonné</title>
</head>
<body>
<main class="container mt-4">
    <h1>Modifier l'Abonné #<?php echo htmlspecialchars($id_abonne); ?></h1>

    <?php if ($erreur): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erreur); ?>
        </div>
    <?php endif; ?>

    <?php if ($abonne): ?>
    <form method="POST" action="modifier.php?id=<?php echo $id_abonne; ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nom" name="nom" required value="<?php echo htmlspecialchars($abonne['nom'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="prenom" name="prenom" required value="<?php echo htmlspecialchars($abonne['prenom'] ?? ''); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($abonne['email'] ?? ''); ?>">
            </div>
             <div class="col-md-6 mb-3">
                <label for="date_naissance" class="form-label">Date de Naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?php echo htmlspecialchars($abonne['date_naissance'] ?? ''); ?>">
            </div>
        </div>
         <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <textarea class="form-control" id="adresse" name="adresse" rows="3"><?php echo htmlspecialchars($abonne['adresse'] ?? ''); ?></textarea>
        </div>
         <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" class="form-control" id="ville" name="ville" value="<?php echo htmlspecialchars($abonne['ville'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="code_postal" class="form-label">Code Postal</label>
                <input type="text" class="form-control" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($abonne['code_postal'] ?? ''); ?>">
            </div>
        </div>
        <div class="row">
             <div class="col-md-6 mb-3">
                <label for="tel" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="tel" name="tel" value="<?php echo htmlspecialchars($abonne['tel'] ?? ''); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="mot_de_passe" class="form-label">Nouveau Mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" aria-describedby="passwordHelpModif">
                <div id="passwordHelpModif" class="form-text">Laissez vide pour ne pas changer le mot de passe.</div>
            </div>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="actif" name="actif" value="1" <?php echo ($abonne['actif'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="actif">
                Compte Actif
            </label>
        </div>
         <div class="mb-3">
            <label class="form-label">Date d'inscription</label>
            <input type="text" class="form-control" value="<?php echo $abonne['date_inscription'] ? htmlspecialchars(date('d/m/Y', strtotime($abonne['date_inscription']))) : 'N/A'; ?>" disabled readonly>
        </div>

        <button type="submit" class="btn btn-success">Enregistrer les Modifications</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
    <?php endif; ?>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
