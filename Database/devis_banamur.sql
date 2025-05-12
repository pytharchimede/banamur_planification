-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 12 mai 2025 à 10:20
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
-- Structure de la table `devis_banamur`
--

DROP TABLE IF EXISTS `devis_banamur`;
CREATE TABLE IF NOT EXISTS `devis_banamur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `offre_id` int NOT NULL,
  `numero_devis` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `delai_livraison` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_emission` date NOT NULL,
  `date_expiration` date NOT NULL,
  `emis_par` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `destine_a` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `logo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `termes_conditions` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `pied_de_page` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci,
  `total_ht` decimal(10,2) NOT NULL,
  `total_ttc` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tva_facturable` int NOT NULL,
  `publier_devis` int NOT NULL,
  `tva` decimal(10,2) NOT NULL,
  `correspondant` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `masque` int NOT NULL,
  `validation_technique` int NOT NULL,
  `validation_generale` int NOT NULL,
  `page_garde` longtext COLLATE utf8mb3_unicode_ci,
  `description` longtext COLLATE utf8mb3_unicode_ci,
  `delai` longtext COLLATE utf8mb3_unicode_ci,
  `conditions` longtext COLLATE utf8mb3_unicode_ci,
  `garantie` longtext COLLATE utf8mb3_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
