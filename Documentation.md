## 1. Cahier des Charges 
### 1.1 Introduction

Le projet avait pour but de faire un site de gestion de la médiathèque de Montpellier. Les admins doivent pouvoir gérer les livres, les CD, etc., les abonnés et les prêts. Les abonnés (clients) doivent pouvoir voir ce qu'il y a, réserver des trucs et voir ce qu'ils ont emprunté.

### 1.2 Objectifs

*   Avoir un seul endroit pour gérer la médiathèque facilement.
*   Permettre aux abonnés de faire des trucs depuis chez eux (voir le catalogue, réserver).
*   Que la gestion des documents et des prêts soit simple.
*   Sécuriser le site : tout le monde ne peut pas tout faire (Admin vs Client).

### 1.3 Fonctionnalités Requises

#### 1.3.1 Authentification (Qui peut se connecter ?)
*   Connexion classique avec email et mot de passe.
*   Deux types d'utilisateurs : Admin et Client.
*   Un client doit être "actif" pour se connecter.
*   Pouvoir se déconnecter proprement.
*   Bloquer l'accès aux pages si on n'a pas le bon rôle ou si on n'est pas connecté.

#### 1.3.2 Module Administrateur (Ce que l'admin peut faire)
*   **Gestion des Documents:**
    *   Voir la liste de tout ce qu'il y a, et si c'est dispo ou pas.
    *   Ajouter des nouveaux documents.
    *   Modifier les infos d'un document.
    *   Supprimer un document (mais pas s'il est emprunté).
*   **Gestion des Abonnés:**
    *   Voir la liste des abonnés (actifs ou pas).
    *   Ajouter un nouvel abonné.
    *   Modifier les infos d'un abonné (même son statut ou son mot de passe).
    *   Supprimer un abonné (mais pas s'il a des trucs empruntés).
*   **Gestion des Prêts:**
    *   Voir la liste des prêts en cours (qui a quoi, depuis quand, jusqu'à quand).
    *   Ajouter un prêt manuellement (pour un abonné et un document dispo).
    *   Dire qu'un prêt est "retourné" (ça met à jour la date et ça rend le document dispo).

#### 1.3.3 Module Client (Ce que l'abonné peut faire)
*   **Catalogue:**
    *   Voir la liste des documents dispos (par exemple, que les livres).
    *   Voir les infos de base (titre, auteur...).
*   **Réservation:**
    *   Réserver un truc dispo dans le catalogue.
    *   Quand c'est réservé, ça devient indisponible et ça crée un prêt.
*   **Mes Prêts:**
    *   Voir ce qu'il a emprunté en ce moment.
    *   Voir l'historique de ce qu'il a déjà rendu.

### 1.4 Contraintes Techniques (Comment j'ai fait)
*   Site web en PHP.
*   Base de données MySQL (ou MariaDB).
*   Sessions PHP pour savoir qui est connecté.
*   Interface en HTML/CSS (j'ai utilisé Bootstrap pour que ça ressemble à quelque chose).
*   Ça tourne sur un serveur web type WAMP/LAMP/MAMP.
---

## 2. Modèle Conceptuel de Données (MCD) - La structure de la base

Le MCD, c'est le plan de la base de données. Ça montre les tables principales et comment elles sont liées.

### 2.1 Entités Principales (Les grosses tables)

*   **Abonnes:** Les clients.
    *   Infos: id, nom, prénom, date naissance, email (unique), adresse, ville, code postal, tél, date inscription, actif (oui/non), mot de passe.
*   **Documents:** Les trucs à emprunter.
    *   Infos: id, titre, auteur, type (livre, cd...), date parution, genre, dispo (oui/non).
*   **Employes:** Les admins.
    *   Infos: id, nom, prénom, poste, email (unique), mot de passe.
*   **Prets:** Quand quelqu'un emprunte quelque chose.
    *   Infos: id, id de l'abonné, id du document, date du prêt, date retour prévue, date retour réelle.
*   **Abonnements:** Les types d'abonnements (annuel, mensuel...).
    *   Infos: id, id de l'abonné, type, tarif, date début, date fin.
*   **Contentieux:** Pour suivre les problèmes (retards, pertes...).
    *   Infos: id, id du prêt, id de l'employé, motif, pénalité, date création, résolu (oui/non).
*   **Lettres_Rappel:** Pour garder une trace des rappels envoyés.
    *   Infos: id, id de l'abonné, id de l'employé, date envoi, type (retard, abo expiré...).

### 2.2 Relations Principales (Comment les tables sont liées)

*   Un abonné peut avoir plusieurs abonnements.
*   Un abonné peut faire plusieurs prêts.
*   Un document peut être prêté plusieurs fois (mais pas en même temps).
*   Un prêt peut causer un contentieux.
*   Un abonné peut recevoir plusieurs lettres de rappel.
*   Un employé peut gérer plusieurs contentieux.
*   Un employé peut envoyer plusieurs lettres de rappel.

![Modèle Conceptuel de Données](/MCD.png)


---

## 3. Tests Effectués (Ce que j'ai vérifié)

J'ai pas fait de tests automatisés super poussés, mais j'ai vérifié manuellement que tout fonctionnait comme prévu. Voici le genre de tests qu'il faudrait faire plus sérieusement :

### 3.1 Tests Unitaires (Tester les petits bouts de code)
*   **Objectif:** Vérifier que chaque petite fonction fait bien son boulot.
*   **Exemples:**
    *   Est-ce que la connexion à la BDD dans `config.php` marche ?
    *   Est-ce que `auth/process_login.php` reconnaît bien un admin, un client, un client inactif, ou des mauvais identifiants ?
    *   Est-ce que l'ajout d'un abonné marche bien (et refuse si l'email existe déjà) ?
    *   Est-ce que la date de retour prévue est bien calculée ?

### 3.2 Tests d'Intégration (Tester comment les bouts de code marchent ensemble)
*   **Objectif:** Vérifier que les différents modules communiquent bien.
*   **Exemples:**
    *   Quand un client réserve (`client/reserver.php`), est-ce que ça crée bien un prêt ET ça met le document en indisponible ?
    *   Quand un admin marque un retour (`admin/prets/retourner.php`), est-ce que ça met bien à jour le prêt ET ça rend le document dispo ?
    *   Est-ce qu'on ne peut vraiment pas supprimer un abonné qui a des prêts en cours ?
    *   Est-ce qu'on ne peut pas supprimer un document prêté ?
    *   Est-ce qu'un client ne peut pas aller sur les pages admin ?

### 3.3 Tests Fonctionnels (Tester comme un vrai utilisateur)
*   **Objectif:** Simuler ce que ferait un utilisateur pour voir si ça marche.
*   **Exemples:**
    *   **Scénario Admin:** Se connecter -> Ajouter un livre -> Le modifier -> Ajouter un abonné -> Lui prêter le livre -> Marquer le retour -> Supprimer l'abonné -> Supprimer le livre -> Se déconnecter.
    *   **Scénario Client:** Se connecter -> Regarder le catalogue -> Réserver un livre -> Voir "Mes Prêts" -> Se déconnecter.
    *   Est-ce que les formulaires râlent bien si on oublie un champ obligatoire ou si l'email est mal formé ?
    *   Est-ce que les messages d'erreur/succès s'affichent bien ?
    *   Est-ce qu'on peut naviguer partout sans problème ?

### 3.4 Tests de Sécurité (Vérifier qu'il n'y a pas de failles)
*   **Objectif:** Chercher les problèmes de sécurité.
*   **Exemples:**
    *   **Injection SQL:** J'ai utilisé PDO avec des requêtes préparées, ça devrait être bon de ce côté-là.
    *   **XSS:** J'ai utilisé `htmlspecialchars()` pour afficher les données, ça devrait éviter les scripts qui veulent nuire au site.
    *   **Session:** Est-ce que la déconnexion marche bien ?
    *   **Accès:** Est-ce qu'un client ne peut VRAIMENT pas accéder aux trucs admin ?
    *   **Mots de passe:** Les mots de passe sont en clair dans la BDD. Il faudrait les "hasher" avec `password_hash()` pour plus de sécurité.

---

## 4. Scénarios d'Utilisation (Comment on utilise le site)

### 4.1 Acteurs (Qui utilise ?)
*   **Administrateur:** Quelqu'un qui bosse à la médiathèque.
*   **Client:** Un abonné.

### 4.2 Scénarios Administrateur

*   **UC-ADM-01: Gérer les documents**
    1.  L'admin se connecte.
    2.  Va dans "Gestion Documents".
    3.  Là, il peut : voir la liste, ajouter, modifier, ou supprimer (si pas prêté).

*   **UC-ADM-02: Gérer les abonnés**
    1.  L'admin se connecte.
    2.  Va dans "Gestion Abonnés".
    3.  Là, il peut : voir la liste, ajouter, modifier, ou supprimer (si pas de prêt en cours).

*   **UC-ADM-03: Gérer les prêts**
    1.  L'admin se connecte.
    2.  Va dans "Gestion Prêts".
    3.  Là, il peut : voir les prêts en cours, ajouter un prêt manuellement, ou marquer un prêt comme retourné.

### 4.3 Scénarios Client

*   **UC-CLI-01: Consulter le catalogue et réserver**
    1.  Le client se connecte.
    2.  Va dans "Catalogue".
    3.  Regarde ce qu'il y a.
    4.  Clique sur "Réserver" (et confirme).
    5.  Le site dit si c'est bon ou s'il y a un problème.

*   **UC-CLI-02: Consulter ses prêts**
    1.  Le client se connecte.
    2.  Va dans "Mes Prêts".
    3.  Voit ce qu'il a emprunté et l'historique de ce qu'il a rendu.

### 4.4 Scénarios Communs (Pour tout le monde)

*   **UC-ALL-01: Se connecter**
    1.  Aller sur la page de connexion.
    2.  Mettre email et mot de passe.
    3.  Valider.
    4.  Si c'est bon, on arrive sur le tableau de bord. Sinon, message d'erreur.

*   **UC-ALL-02: Se déconnecter**
    1.  Cliquer sur "Déconnexion".
    2.  La session est fermée.
    3.  On revient à la page de connexion.

---
