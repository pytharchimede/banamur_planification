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
-- Structure de la table `offre_banamur`
--

DROP TABLE IF EXISTS `offre_banamur`;
CREATE TABLE IF NOT EXISTS `offre_banamur` (
  `id_offre` int NOT NULL AUTO_INCREMENT,
  `num_offre` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_offre` date NOT NULL,
  `reference_offre` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `commercial_dedie` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `date_creat_offre` date NOT NULL,
  PRIMARY KEY (`id_offre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `offre_banamur`
--

INSERT INTO `offre_banamur` (`id_offre`, `num_offre`, `date_offre`, `reference_offre`, `commercial_dedie`, `date_creat_offre`) VALUES
(1, 'Labore veniam offic', '2022-09-19', 'Ipsam porro ducimus', 'Eum expedita consequ', '1992-05-13'),
(2, 'In et vero ea unde i', '1971-10-21', 'Ad id beatae possimu', 'Perspiciatis sit e', '2004-12-21'),
(3, 'Dolore pariatur Est', '1997-11-10', 'Aspernatur nisi quas', 'Enim facere rerum ma', '1988-10-29'),
(4, 'Quidem est aut dolor', '1992-06-19', 'Porro necessitatibus', 'Quae ipsam quaerat m', '2025-03-09'),
(5, 'Molestiae ad eum min', '1973-11-20', 'Vero laudantium ven', 'Voluptatum laboriosa', '1992-04-23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
