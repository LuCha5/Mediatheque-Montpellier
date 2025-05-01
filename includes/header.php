<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Maison du Livre</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/maison_du_livre/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/maison_du_livre/index.php">
                    <img src="/maison_du_livre/assets/img/logo.png" alt="Logo La Maison du Livre" id="logo" style="height: 40px; width: auto;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                        <li class="nav-item">
                            <a class="nav-link" href="/maison_du_livre/client/catalogue.php">Catalogue</a>
                        </li>
                        <?php if (isset($_SESSION['user_role'])): ?>
                            <?php if ($_SESSION['user_role'] == 'admin'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/maison_du_livre/admin/documents/index.php">Gestion Documents</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/maison_du_livre/admin/abonnes/index.php">Gestion Abonnés</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/maison_du_livre/admin/prets/index.php">Gestion Prêts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="/maison_du_livre/admin/contentieux/index.php">Gestion Contentieux</a>
                                </li>
                            <?php elseif ($_SESSION['user_role'] == 'client'): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/maison_du_livre/client/mes_prets.php">Mes Prêts</a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                         <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <span class="navbar-text me-2">
                                    Bonjour <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> (<?php echo htmlspecialchars($_SESSION['user_role']); ?>)
                                </span>
                            </li>
                            <li class="nav-item">
                                <a class="btn btn-outline-secondary btn-sm" href="/maison_du_livre/auth/logout.php">Déconnexion</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="btn btn-outline-primary btn-sm" href="/maison_du_livre/auth/login.php">Connexion</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
