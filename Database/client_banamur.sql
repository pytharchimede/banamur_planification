-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 12 mai 2025 à 09:44
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
-- Base de données : `fidestci_app_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `client_banamur`
--

DROP TABLE IF EXISTS `client_banamur`;
CREATE TABLE IF NOT EXISTS `client_banamur` (
  `id_client` int NOT NULL AUTO_INCREMENT,
  `code_client` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `nom_client` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `localisation_client` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `commune_client` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `bp_client` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `pays_client` text COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_creat_client` int NOT NULL,
  `telephone_client` varchar(30) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logo_client` varchar(255) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_client`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `client_banamur`
--

INSERT INTO `client_banamur` (`id_client`, `code_client`, `nom_client`, `localisation_client`, `commune_client`, `bp_client`, `pays_client`, `date_creat_client`, `telephone_client`, `logo_client`) VALUES
(1, 'FID-CLI-001', 'SIFCA', 'Bd. du Havre, Vers les Grands Moulins', 'Zone portuaire, Treichville', '01 BP 1289 Abidjan 01', 'Cote d Ivoire', 0, NULL, NULL),
(2, 'FID-CLI-002', 'BIA', 'Sur le VGE, a cote d\'Orange CI', 'Marcory - 18 BP 1081 Abidjan 18', '01 BP 1289 Abidjan 01', 'Cote d Ivoire', 0, NULL, NULL),
(25, 'BAN-CLI-001', 'SIR', '7258+45J, Boulevard de Petit Bassam, Abidjan', 'Port-Bouet Vridi zone industrielle', '01 BP 1269 Abidjan 01', 'Côte d’Ivoire', 2025, NULL, NULL),
(26, 'Qui aut asperiores c', 'Aliqua Velit invent', 'Suscipit ipsa aute ', 'Dolorum velit iste h', 'Quo amet sed dolor ', 'Est minus deserunt ', 1993, '+1 (221) 898-1936', 'logo_6821c1b47d89a.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
