-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 10 mai 2025 à 10:31
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
-- Structure de la table `unites_mesure`
--

DROP TABLE IF EXISTS `unites_mesure`;
CREATE TABLE IF NOT EXISTS `unites_mesure` (
  `id` int NOT NULL AUTO_INCREMENT,
  `symbole` varchar(20) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbole` (`symbole`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `unites_mesure`
--

INSERT INTO `unites_mesure` (`id`, `symbole`, `libelle`) VALUES
(1, 'm', 'Mètre'),
(2, 'cm', 'Centimètre'),
(3, 'mm', 'Millimètre'),
(4, 'km', 'Kilomètre'),
(5, 'g', 'Gramme'),
(6, 'kg', 'Kilogramme'),
(7, 't', 'Tonne'),
(8, 'l', 'Litre'),
(9, 'ml', 'Millilitre'),
(10, 'm²', 'Mètre carré'),
(11, 'cm²', 'Centimètre carré'),
(12, 'mm²', 'Millimètre carré'),
(13, 'm³', 'Mètre cube'),
(14, 'cm³', 'Centimètre cube'),
(15, 'h', 'Heure'),
(16, 'min', 'Minute'),
(17, 's', 'Seconde'),
(18, 'pcs', 'Pièce'),
(19, 'u', 'Unité'),
(20, 'paire', 'Paire'),
(21, 'lot', 'Lot'),
(22, 'bar', 'Bar'),
(23, 'N', 'Newton'),
(24, 'W', 'Watt'),
(25, 'kW', 'Kilowatt'),
(26, 'V', 'Volt'),
(27, 'A', 'Ampère'),
(28, 'K', 'Kelvin'),
(29, '°C', 'Degré Celsius'),
(30, 'Pa', 'Pascal'),
(31, 'J', 'Joule'),
(32, 'm/s', 'Mètre par seconde'),
(33, 'm/min', 'Mètre par minute'),
(34, 'l/min', 'Litre par minute'),
(35, '%', 'Pourcentage');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
