-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql209.infinityfree.com
-- Généré le :  ven. 28 mars 2025 à 06:00
-- Version du serveur :  10.6.19-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `vva`
--

-- --------------------------------------------------------

--
-- Structure de la table `ACTIVITE`
--

CREATE TABLE `ACTIVITE` (
  `CODEANIM` char(8) NOT NULL,
  `DATEACT` date NOT NULL,
  `CODEETATACT` char(2) NOT NULL,
  `HRRDVACT` time DEFAULT NULL,
  `PRIXACT` decimal(7,2) DEFAULT NULL,
  `HRDEBUTACT` time DEFAULT NULL,
  `HRFINACT` time DEFAULT NULL,
  `DATEANNULEACT` date DEFAULT NULL,
  `NOMRESP` char(40) DEFAULT NULL,
  `PRENOMRESP` char(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ACTIVITE`
--

INSERT INTO `ACTIVITE` (`CODEANIM`, `DATEACT`, `CODEETATACT`, `HRRDVACT`, `PRIXACT`, `HRDEBUTACT`, `HRFINACT`, `DATEANNULEACT`, `NOMRESP`, `PRENOMRESP`) VALUES
('A001', '2025-12-04', 'E1', '09:00:00', '15.50', '09:00:00', '12:00:00', NULL, 'Dubois', 'Lucas'),
('F9615', '2025-11-24', 'E1', '09:00:00', '12.00', '09:00:00', '11:00:00', NULL, 'Dubois', 'Lucas'),
('TEN22', '2025-04-15', 'E1', '14:00:00', '20.00', '14:00:00', '16:00:00', NULL, 'Dupont', 'Jean'),
('TEN22', '2025-04-22', 'E1', '14:00:00', '20.00', '14:00:00', '16:00:00', NULL, 'Dupont', 'Jean');

-- --------------------------------------------------------

--
-- Structure de la table `ANIMATION`
--

CREATE TABLE `ANIMATION` (
  `CODEANIM` char(8) NOT NULL,
  `CODETYPEANIM` char(5) NOT NULL,
  `NOMANIM` char(40) DEFAULT NULL,
  `DATECREATIONANIM` date DEFAULT NULL,
  `DATEVALIDITEANIM` date DEFAULT NULL,
  `DUREEANIM` double(5,0) DEFAULT NULL,
  `LIMITEAGE` int(11) DEFAULT NULL,
  `TARIFANIM` decimal(7,2) DEFAULT NULL,
  `NBREPLACEANIM` int(11) DEFAULT NULL,
  `DESCRIPTANIM` char(250) DEFAULT NULL,
  `COMMENTANIM` char(250) DEFAULT NULL,
  `DIFFICULTEANIM` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ANIMATION`
--

INSERT INTO `ANIMATION` (`CODEANIM`, `CODETYPEANIM`, `NOMANIM`, `DATECREATIONANIM`, `DATEVALIDITEANIM`, `DUREEANIM`, `LIMITEAGE`, `TARIFANIM`, `NBREPLACEANIM`, `DESCRIPTANIM`, `COMMENTANIM`, `DIFFICULTEANIM`) VALUES
('A001', 'T005', 'Balade à vélo découverte', '2024-01-01', '2025-01-01', 3, 12, '15.50', 21, 'Parcours de découverte à vélo autour du village vacances', 'Vélos et casques fournis, prévoir des chaussures adaptées', 'Facile'),
('A002', 'T002', 'Escalade sur paroi naturelle', '2024-02-15', '2025-02-15', 4, 15, '30.00', 11, 'Initiation à l\'escalade sur site naturel adapté aux débutants', 'Matériel fourni (baudrier, casque), prévoir des vêtements confortables', 'Moyenne'),
('A003', 'T003', 'Descente en canoë sur le Verdon', '2024-03-10', '2025-03-10', 5, 14, '50.00', 8, 'Parcours de 8 km sur les eaux calmes et rapides du Verdon', 'Matériel fourni, savoir nager est obligatoire, prévoir des vêtements de rechange', 'Difficile'),
('ESC03', 'T002', 'Escalade Urbain', '2025-01-14', '2026-01-14', 1, 12, '20.00', 3, 'Escalade en plein coeur de la ville', 'Nothing', 'Medium'),
('F9615', 'T003', 'Rafting en eaux vives', '2024-05-15', '2025-05-15', 3, 30, '12.00', 59, 'Descente en groupe sur les rapides, sensations garanties !', 'Matériel fourni, savoir nager est obligatoire, prévoir des vêtements de rechange', NULL),
('RAN01', 'T001', 'Randonnée forêt', '2024-01-01', '2025-12-31', 3, 8, '15.00', 15, 'Randonnée en forêt sur sentiers balisés', 'Prévoir chaussures de marche et gourde', 'Facile'),
('SKI01', 'T007', 'Ski alpin débutant', '2024-10-01', '2025-04-30', 3, 6, '45.00', 8, 'Initiation au ski alpin sur pistes vertes et bleues', 'Matériel fourni, prévoir vêtements chauds et imperméables', 'Facile'),
('SKI02', 'T007', 'Ski alpin confirmé', '2024-10-01', '2025-04-30', 4, 12, '55.00', 6, 'Ski alpin sur pistes rouges et noires', 'Matériel non fourni, niveau flocon d\'or minimum requis', 'Difficile'),
('TEN22', 'T006', 'Tennis débutant', '2024-01-11', '2025-01-11', 2, 18, '20.00', 2, 'Initiation au tennis sur courts extérieurs', 'Raquettes et balles fournies, prévoir des chaussures de sport', 'Easy'),
('TEN99', 'T006', 'Tennis avancé - Style Wimbledon', '2025-03-20', '2026-03-20', 2, 50, '100.00', 100, 'Perfectionnement technique et tactique sur gazon synthétique', 'Raquettes personnelles recommandées, prévoir tenue blanche', 'Difficile');

-- --------------------------------------------------------

--
-- Structure de la table `COMPTE`
--

CREATE TABLE `COMPTE` (
  `USER` char(8) NOT NULL,
  `MDP` char(10) DEFAULT NULL,
  `NOMCOMPTE` char(40) DEFAULT NULL,
  `PRENOMCOMPTE` char(30) DEFAULT NULL,
  `DATEINSCRIP` date DEFAULT NULL,
  `DATEFERME` date DEFAULT NULL,
  `TYPEPROFIL` char(2) DEFAULT NULL,
  `DATEDEBSEJOUR` date DEFAULT NULL,
  `DATEFINSEJOUR` date DEFAULT NULL,
  `DATENAISCOMPTE` date DEFAULT NULL,
  `ADRMAILCOMPTE` char(70) DEFAULT NULL,
  `NOTELCOMPTE` char(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `COMPTE`
--

INSERT INTO `COMPTE` (`USER`, `MDP`, `NOMCOMPTE`, `PRENOMCOMPTE`, `DATEINSCRIP`, `DATEFERME`, `TYPEPROFIL`, `DATEDEBSEJOUR`, `DATEFINSEJOUR`, `DATENAISCOMPTE`, `ADRMAILCOMPTE`, `NOTELCOMPTE`) VALUES
('admin', 'admin', 'Dupont', 'Jean', '2024-01-01', NULL, 'AD', NULL, NULL, '1990-05-15', 'jean.dupont@example.com', '0601020304'),
('lucas', 'lucas', 'Dubois', 'Lucas', '2024-01-01', NULL, 'EN', NULL, NULL, '1990-05-14', 'lucas.dubpis@example.com', '0601020303'),
('romain', 'romain', 'Pereira', 'Romain', '2025-01-14', NULL, 'VA', '2025-11-20', '2025-12-20', '2000-01-19', 'rpereira.pro@gmail.com', '0789982301'),
('yanis', 'yanis', 'Chaili', 'Yanis', '2025-03-24', NULL, 'VA', '2025-03-24', '2025-04-30', '1995-06-12', 'yanis.chaili@gmail.com', '0702030405');

-- --------------------------------------------------------

--
-- Structure de la table `ETAT_ACT`
--

CREATE TABLE `ETAT_ACT` (
  `CODEETATACT` char(2) NOT NULL,
  `NOMETATACT` char(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `ETAT_ACT`
--

INSERT INTO `ETAT_ACT` (`CODEETATACT`, `NOMETATACT`) VALUES
('E1', 'Ouvert'),
('E2', 'Incertaine'),
('E3', 'Annulé');

-- --------------------------------------------------------

--
-- Structure de la table `INSCRIPTION`
--

CREATE TABLE `INSCRIPTION` (
  `NOINSCRIP` bigint(20) NOT NULL,
  `USER` char(8) NOT NULL,
  `CODEANIM` char(8) NOT NULL,
  `DATEACT` date NOT NULL,
  `DATEINSCRIP` date DEFAULT NULL,
  `DATEANNULE` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `TYPE_ANIM`
--

CREATE TABLE `TYPE_ANIM` (
  `CODETYPEANIM` char(5) NOT NULL,
  `NOMTYPEANIM` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `TYPE_ANIM`
--

INSERT INTO `TYPE_ANIM` (`CODETYPEANIM`, `NOMTYPEANIM`) VALUES
('T001', 'Randonnée'),
('T002', 'Escalade'),
('T003', 'Sports nautiques'),
('T004', 'Parapente'),
('T005', 'Cyclisme'),
('T006', 'Sports de raquette'),
('T007', 'Sports d\'hiver');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `ACTIVITE`
--
ALTER TABLE `ACTIVITE`
  ADD PRIMARY KEY (`CODEANIM`,`DATEACT`),
  ADD KEY `I_FK_ACTIVITE_ANIMATION` (`CODEANIM`),
  ADD KEY `I_FK_ACTIVITE_ETAT_ACT` (`CODEETATACT`);

--
-- Index pour la table `ANIMATION`
--
ALTER TABLE `ANIMATION`
  ADD PRIMARY KEY (`CODEANIM`),
  ADD KEY `I_FK_ANIMATION_TYPE_ANIM` (`CODETYPEANIM`);

--
-- Index pour la table `COMPTE`
--
ALTER TABLE `COMPTE`
  ADD PRIMARY KEY (`USER`);

--
-- Index pour la table `ETAT_ACT`
--
ALTER TABLE `ETAT_ACT`
  ADD PRIMARY KEY (`CODEETATACT`);

--
-- Index pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD PRIMARY KEY (`NOINSCRIP`),
  ADD KEY `I_FK_INSCRIPTION_COMPTE` (`USER`),
  ADD KEY `I_FK_INSCRIPTION_ACTIVITE` (`CODEANIM`,`DATEACT`);

--
-- Index pour la table `TYPE_ANIM`
--
ALTER TABLE `TYPE_ANIM`
  ADD PRIMARY KEY (`CODETYPEANIM`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  MODIFY `NOINSCRIP` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `ACTIVITE`
--
ALTER TABLE `ACTIVITE`
  ADD CONSTRAINT `activite_ibfk_1` FOREIGN KEY (`CODEANIM`) REFERENCES `ANIMATION` (`CODEANIM`),
  ADD CONSTRAINT `activite_ibfk_2` FOREIGN KEY (`CODEETATACT`) REFERENCES `ETAT_ACT` (`CODEETATACT`);

--
-- Contraintes pour la table `ANIMATION`
--
ALTER TABLE `ANIMATION`
  ADD CONSTRAINT `animation_ibfk_1` FOREIGN KEY (`CODETYPEANIM`) REFERENCES `TYPE_ANIM` (`CODETYPEANIM`);

--
-- Contraintes pour la table `INSCRIPTION`
--
ALTER TABLE `INSCRIPTION`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`USER`) REFERENCES `COMPTE` (`USER`),
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`CODEANIM`,`DATEACT`) REFERENCES `ACTIVITE` (`CODEANIM`, `DATEACT`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
