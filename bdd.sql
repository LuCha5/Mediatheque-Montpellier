-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 30 avr. 2025 à 17:07
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `mediatheque2`
--

-- --------------------------------------------------------

--
-- Structure de la table `abonnements`
--

DROP TABLE IF EXISTS `abonnements`;
CREATE TABLE IF NOT EXISTS `abonnements` (
  `id_abonnement` int NOT NULL AUTO_INCREMENT,
  `id_abonne` int DEFAULT NULL,
  `type` enum('annuel','mensuel') DEFAULT NULL,
  `tarif` decimal(5,2) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  PRIMARY KEY (`id_abonnement`),
  KEY `id_abonne` (`id_abonne`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `abonnements`
--

INSERT INTO `abonnements` (`id_abonnement`, `id_abonne`, `type`, `tarif`, `date_debut`, `date_fin`) VALUES
(1, 1, 'annuel', 22.00, '2024-01-10', '2025-01-10'),
(2, 2, 'mensuel', 10.50, '2023-11-15', '2023-12-15'),
(3, 3, 'annuel', 22.00, '2022-07-22', '2023-07-22'),
(4, 4, 'mensuel', 10.50, '2023-03-30', '2023-04-30'),
(5, 1, 'annuel', 0.00, '2024-01-10', '2025-01-09'),
(6, 2, 'annuel', 0.00, '2024-05-20', '2025-05-19'),
(7, 3, 'annuel', 0.00, '2024-02-15', '2025-02-14'),
(8, 4, 'annuel', 22.00, '2023-11-05', '2024-11-04'),
(9, 5, 'annuel', 0.00, '2024-03-01', '2025-02-28');

-- --------------------------------------------------------

--
-- Structure de la table `abonnes`
--

DROP TABLE IF EXISTS `abonnes`;
CREATE TABLE IF NOT EXISTS `abonnes` (
  `id_abonne` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `adresse` text,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `date_inscription` date DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `mot_de_passe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_abonne`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `abonnes`
--

INSERT INTO `abonnes` (`id_abonne`, `nom`, `prenom`, `date_naissance`, `email`, `adresse`, `ville`, `code_postal`, `tel`, `date_inscription`, `actif`, `mot_de_passe`) VALUES
(1, 'okok', 'Maxence', '1985-07-15', 'test@test.com', '10 rue des Lilas', 'Montpellier', '34000', '0123456789', '2024-01-10', 1, 'password'),
(2, 'Martin', 'Marie', '1992-03-23', 'marie.martin@example.com', '5 avenue des Champs', 'Montpellier', '34000', '0987654321', '2023-11-15', 1, NULL),
(4, 'Bernard', 'Sophie', '2000-12-12', 'sophie.bernard@example.com', '8 boulevard des Anglais', 'Montpellier', '34000', '0671827359', '2023-03-30', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contentieux`
--

DROP TABLE IF EXISTS `contentieux`;
CREATE TABLE IF NOT EXISTS `contentieux` (
  `id_contentieux` int NOT NULL AUTO_INCREMENT,
  `id_pret` int DEFAULT NULL,
  `id_employe` int DEFAULT NULL,
  `motif` text,
  `montant_penalite` decimal(6,2) DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `resolu` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_contentieux`),
  KEY `id_pret` (`id_pret`),
  KEY `id_employe` (`id_employe`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

--
-- Structure de la table `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `id_document` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `auteur` varchar(255) DEFAULT NULL,
  `type` enum('livre','périodique','cd','livre audio','dvd','blu-ray') DEFAULT NULL,
  `date_parution` date DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `disponible` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_document`)
) ENGINE=MyISAM AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `documents`
--

INSERT INTO `documents` (`id_document`, `titre`, `auteur`, `type`, `date_parution`, `genre`, `disponible`) VALUES
(1, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'livre', '1943-04-06', 'Conte', 1),
(2, 'Les Misérables', 'Victor Hugo', 'livre', '1862-01-01', 'Roman', 1),
(3, 'Les Échos du Futur', 'Jean Dupont', 'cd', '2021-07-10', 'Musique', 1),
(4, 'Le Dernier Voyage', 'Hélène Dubois', 'dvd', '2019-05-15', 'Science-Fiction', 1),
(5, 'La Mémoire du Temps', 'Pierre Lemoine', 'blu-ray', '2020-03-23', 'Historique', 1),
(6, 'Dune', 'Frank Herbert', 'livre', '1965-08-01', 'Science-Fiction', 1),
(14, 'L\'Étranger', 'Albert Camus', 'livre', '1942-06-19', 'Philosophique', 1),
(8, 'Le Monde', 'Hubert Beuve Méry', 'périodique', '2025-04-25', 'Actualités', 1),
(9, 'Thriller', 'Michael Jackson', 'cd', '1982-11-30', 'Pop', 0),
(10, 'Harry Potter à l\'école des sorciers (Livre Audio)', 'J.K. Rowling', 'livre audio', '2017-01-12', 'Fantastique', 1),
(11, 'Interstellar', 'Christopher Nolan', 'dvd', '2015-03-31', 'Science-Fiction', 1),
(12, 'Le Seigneur des Anneaux : La Communauté de l\'Anneau', 'Peter Jackson', 'blu-ray', '2011-06-28', 'Fantastique', 1),
(13, '1984', 'George Orwell', 'livre', '1949-06-08', 'Dystopie', 1),
(15, '1984', 'George Orwell', 'livre', '1949-06-08', 'Dystopie', 0),
(16, 'Le Petit Prince', 'Antoine de Saint-Exupéry', 'livre', '1943-04-06', 'Enfants', 1);

-- --------------------------------------------------------

--
-- Structure de la table `employes`
--

DROP TABLE IF EXISTS `employes`;
CREATE TABLE IF NOT EXISTS `employes` (
  `id_employe` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_employe`),
  UNIQUE KEY `UQ_employes_email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `employes`
--

INSERT INTO `employes` (`id_employe`, `nom`, `prenom`, `poste`, `email`, `mot_de_passe`) VALUES
(1, 'test', 'admin', 'Directeur', 'admin@admin.com', 'password'),
(6, 'Bernard', 'Luc', 'Agent accueil', 'luc.bernard@mediamtp.fr', NULL),
(7, 'Petit', 'Alice', 'Bibliothécaire jeunesse', 'alice.petit@mediamtp.fr', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `lettres_rappel`
--

DROP TABLE IF EXISTS `lettres_rappel`;
CREATE TABLE IF NOT EXISTS `lettres_rappel` (
  `id_lettre` int NOT NULL AUTO_INCREMENT,
  `id_abonne` int DEFAULT NULL,
  `id_employe` int DEFAULT NULL,
  `date_envoi` date DEFAULT NULL,
  `type_lettre` enum('retard','abonnement expiré') DEFAULT NULL,
  PRIMARY KEY (`id_lettre`),
  KEY `id_abonne` (`id_abonne`),
  KEY `id_employe` (`id_employe`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `lettres_rappel`
--

INSERT INTO `lettres_rappel` (`id_lettre`, `id_abonne`, `id_employe`, `date_envoi`, `type_lettre`) VALUES
(1, 1, 2, '2024-02-16', 'retard'),
(2, 3, 1, '2022-08-16', 'retard'),
(3, 4, 2, '2023-04-15', 'abonnement expiré'),
(4, 1, 2, '2025-04-02', 'retard');

-- --------------------------------------------------------

--
-- Structure de la table `prets`
--

DROP TABLE IF EXISTS `prets`;
CREATE TABLE IF NOT EXISTS `prets` (
  `id_pret` int NOT NULL AUTO_INCREMENT,
  `id_abonne` int DEFAULT NULL,
  `id_document` int DEFAULT NULL,
  `date_pret` date DEFAULT NULL,
  `date_retour_prevue` date DEFAULT NULL,
  `date_retour_reelle` date DEFAULT NULL,
  PRIMARY KEY (`id_pret`),
  KEY `id_abonne` (`id_abonne`),
  KEY `id_document` (`id_document`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `prets`
--

INSERT INTO `prets` (`id_pret`, `id_abonne`, `id_document`, `date_pret`, `date_retour_prevue`, `date_retour_reelle`) VALUES
(14, 1, 15, '2025-04-30', '2025-05-21', NULL),
(13, 1, 13, '2025-04-30', '2025-05-21', '2025-04-30');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
