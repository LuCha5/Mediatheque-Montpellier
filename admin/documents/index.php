<?php
include('../../config.php');

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /maison_du_livre/auth/login.php?erreur=acces_refuse');
    exit;
}

$query = "SELECT * FROM documents ORDER BY titre";
$stmt = $pdo->query($query);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

include('../../includes/header.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Documents</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .dispo-oui { color: green; font-weight: bold; }
        .dispo-non { color: red; font-weight: bold; }
        .add-button { display: inline-block; margin-top: 20px; padding: 10px 15px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; }
        .add-button:hover { background-color: #0056b3; }
        .message.success { color: green; font-weight: bold; margin-top: 20px; }
        .message.error { color: red; font-weight: bold; margin-top: 20px; }
        .action-link { color: #007bff; text-decoration: none; }
        .action-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>Gestion des Documents</h1>

    <?php if (isset($_GET['suppression']) && $_GET['suppression'] == 'succes'): ?>
        <p class="message success">Le document a été supprimé avec succès.</p>
    <?php elseif (isset($_GET['erreur']) && $_GET['erreur'] == 'pret_en_cours'): ?>
        <p class="message error">Impossible de supprimer le document car il est actuellement prêté.</p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Titre</th>
            <th>Auteur</th>
            <th>Type</th>
            <th>Date de Parution</th>
            <th>Genre</th>
            <th>Disponibilité</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($documents as $doc): ?>
            <tr>
                <td><?php echo htmlspecialchars($doc['titre']); ?></td>
                <td><?php echo htmlspecialchars($doc['auteur']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($doc['type'])); ?></td>
                <td><?php echo $doc['date_parution'] ? htmlspecialchars(date('d/m/Y', strtotime($doc['date_parution']))) : 'N/A'; ?></td>
                <td><?php echo htmlspecialchars($doc['genre']); ?></td>
                <td>
                    <?php if ($doc['disponible']): ?>
                        <span class="dispo-oui">Oui</span>
                    <?php else: ?>
                        <span class="dispo-non">Non</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="modifier.php?id=<?php echo $doc['id_document']; ?>" class="action-link">Modifier</a> |
                    <a href="supprimer.php?id=<?php echo $doc['id_document']; ?>" class="action-link" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?\nAttention : Cette action est irréversible.');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
         <?php if (count($documents) === 0): ?>
             <tr><td colspan="7">Aucun document trouvé.</td></tr>
        <?php endif; ?>
    </table>
    <a href="ajouter.php" class="add-button">Ajouter un Document</a>
</body>
<?php include('../../includes/footer.php'); ?>
</html>
