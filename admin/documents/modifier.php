<?php
include('../../config.php');

// --- Vérification Accès Admin ---
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}
// --- Fin Vérification ---

$livre = null;
$erreur = '';
$id_document = $_GET['id'] ?? null;

// --- Code de récupération du livre ---
if ($id_document) {
    try {
        $query = "SELECT * FROM documents WHERE id_document = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_document]);
        $livre = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$livre) {
            $erreur = "Aucun document trouvé avec cet ID.";
        }
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la récupération du document : " . $e->getMessage();
    }
} else {
    $erreur = "Aucun ID de document fourni.";
}
// --- Fin Code de récupération ---


if ($_SERVER['REQUEST_METHOD'] == 'POST' && $livre) {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $date_parution = trim($_POST['date_parution'] ?? '');
    $genre = trim($_POST['genre'] ?? '');
    $disponible = isset($_POST['disponible']) ? 1 : 0;

    if (empty($titre) || empty($auteur) || empty($type)) {
        $erreur = "Le titre, l'auteur et le type sont obligatoires.";
        $livre = $_POST;
        $livre['id_document'] = $id_document;
    } else {
        try {
            $queryUpdate = "UPDATE documents SET titre = ?, auteur = ?, type = ?, date_parution = ?, genre = ?, disponible = ? WHERE id_document = ?";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->execute([
                $titre,
                $auteur,
                $type,
                $date_parution ?: null,
                $genre,
                $disponible,
                $id_document
            ]);

             $succes = "Document mis à jour avec succès.";
             $stmt->execute([$id_document]);
             $livre = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            $erreur = "Erreur lors de la mise à jour du document : " . $e->getMessage();
            $livre = $_POST;
            $livre['id_document'] = $id_document;
        }
    }
}

include('../../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un Document</title>
</head>
<body>
<main class="container mt-4">
    <h1>Modifier un Document</h1>

    <?php if ($erreur): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($erreur); ?>
        </div>
    <?php endif; ?>
     <?php if (isset($succes) && $succes): ?>
        <div class="alert alert-success" role="alert">
            <?php echo htmlspecialchars($succes); ?>
        </div>
    <?php endif; ?>

    <?php if ($livre): ?>
    <form method="POST" action="modifier.php?id=<?php echo $id_document; ?>">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="titre" name="titre" required value="<?php echo htmlspecialchars($livre['titre'] ?? ''); ?>">
        </div>
        <div class="mb-3">
            <label for="auteur" class="form-label">Auteur <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="auteur" name="auteur" required value="<?php echo htmlspecialchars($livre['auteur'] ?? ''); ?>">
        </div>
         <div class="mb-3">
            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select" id="type" name="type" required>
                <?php $types = ['livre', 'périodique', 'cd', 'livre audio', 'dvd', 'blu-ray']; ?>
                <?php foreach ($types as $t): ?>
                    <option value="<?php echo $t; ?>" <?php echo (($livre['type'] ?? '') == $t) ? 'selected' : ''; ?>>
                        <?php echo ucfirst($t); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
         <div class="mb-3">
            <label for="date_parution" class="form-label">Date de Parution</label>
            <input type="date" class="form-control" id="date_parution" name="date_parution" value="<?php echo htmlspecialchars($livre['date_parution'] ?? ''); ?>">
        </div>
         <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($livre['genre'] ?? ''); ?>">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="disponible" name="disponible" value="1" <?php echo ($livre['disponible'] ?? 0) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="disponible">
                Disponible
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
        <a href="index.php" class="btn btn-secondary">Retour à la liste</a>
    </form>
    <?php elseif (!$erreur) : ?>
        <p class="alert alert-warning">Aucun document sélectionné ou trouvé.</p>
        <a href="index.php" class="btn btn-secondary">Retour à la liste</a>
    <?php endif; ?>
</main>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
