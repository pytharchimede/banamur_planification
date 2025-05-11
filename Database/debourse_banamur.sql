-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : dim. 11 mai 2025 à 11:37
-- Version du serveur : 10.11.11-MariaDB-cll-lve
-- Version de PHP : 8.3.20

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
-- Structure de la table `debourse_banamur`
--

CREATE TABLE `debourse_banamur` (
  `id` int(11) NOT NULL,
  `devis_id` int(11) NOT NULL,
  `ligne_devis_id` int(11) NOT NULL,
  `montant_debourse` decimal(18,2) NOT NULL,
  `responsable_id` int(11) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `motif_modif` varchar(255) DEFAULT NULL,
  `date_modif` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `debourse_banamur`
--
ALTER TABLE `debourse_banamur`
  ADD PRIMARY KEY (`id`),
  ADD KEY `devis_id` (`devis_id`),
  ADD KEY `ligne_devis_id` (`ligne_devis_id`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `debourse_banamur`
--
ALTER TABLE `debourse_banamur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
