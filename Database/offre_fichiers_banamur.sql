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
-- Structure de la table `offre_fichiers_banamur`
--

DROP TABLE IF EXISTS `offre_fichiers_banamur`;
CREATE TABLE IF NOT EXISTS `offre_fichiers_banamur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `offre_id` int NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `offre_id` (`offre_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offre_fichiers_banamur`
--

INSERT INTO `offre_fichiers_banamur` (`id`, `offre_id`, `file_path`, `file_name`, `uploaded_at`) VALUES
(1, 2, '../uploads/offres/681de799eb027_demande_agrement_ppjfes.pdf', 'demande_agrement_ppjfes.pdf', '2025-05-09 11:31:37'),
(2, 2, '../uploads/offres/681de799eb95d_nouvelle_entete_BIT.pdf', 'nouvelle_entete_BIT.pdf', '2025-05-09 11:31:37'),
(3, 2, '../uploads/offres/681de799ec554_source.docx', 'source.docx', '2025-05-09 11:31:37'),
(4, 2, '../uploads/offres/681de799ecacd_source.pdf', 'source.pdf', '2025-05-09 11:31:37'),
(5, 2, '../uploads/offres/681de799eceff_source_ancien.docx', 'source_ancien.docx', '2025-05-09 11:31:37'),
(6, 3, '../uploads/offres/681deb0525fe9_source.pdf', 'source.pdf', '2025-05-09 11:46:13'),
(7, 3, '../uploads/offres/681deb0527588_source_ancien.docx', 'source_ancien.docx', '2025-05-09 11:46:13'),
(8, 4, '../uploads/offres/681deb1fdc56b_certificat_hebergement_nicolas.docx', 'certificat_hebergement_nicolas.docx', '2025-05-09 11:46:39'),
(9, 4, '../uploads/offres/681deb1fdcf0c_certificat_hebergement_nicolas.pdf', 'certificat_hebergement_nicolas.pdf', '2025-05-09 11:46:39'),
(10, 4, '../uploads/offres/681deb1fdd4ef_confirmation_non_revente_marchandise_senstar.docx', 'confirmation_non_revente_marchandise_senstar.docx', '2025-05-09 11:46:39'),
(11, 4, '../uploads/offres/681deb1fddaaf_confirmation_non_revente_marchandise_senstar.pdf', 'confirmation_non_revente_marchandise_senstar.pdf', '2025-05-09 11:46:39'),
(12, 4, '../uploads/offres/681deb1fde087_courrier_sir.docx', 'courrier_sir.docx', '2025-05-09 11:46:39'),
(13, 4, '../uploads/offres/681deb1fde6a8_demande_agrement_ppajfes.pdf', 'demande_agrement_ppajfes.pdf', '2025-05-09 11:46:39'),
(14, 4, '../uploads/offres/681deb1fdec41_demande_agrement_ppjfes.docx', 'demande_agrement_ppjfes.docx', '2025-05-09 11:46:39'),
(15, 4, '../uploads/offres/681deb1fdf1d8_demande_agrement_ppjfes.pdf', 'demande_agrement_ppjfes.pdf', '2025-05-09 11:46:39'),
(16, 4, '../uploads/offres/681deb1fdf955_demande_agrement_ppjfes_2.docx', 'demande_agrement_ppjfes_2.docx', '2025-05-09 11:46:39'),
(17, 4, '../uploads/offres/681deb1fe0276_demande_agrement_SIR_achat.docx', 'demande_agrement_SIR_achat.docx', '2025-05-09 11:46:39'),
(18, 4, '../uploads/offres/681deb1fe0836_ordre_mission_amani_mercredi_19.docx', 'ordre_mission_amani_mercredi_19.docx', '2025-05-09 11:46:39'),
(19, 4, '../uploads/offres/681deb1fe0d9a_ordre_mission_amani_mercredi_19.pdf', 'ordre_mission_amani_mercredi_19.pdf', '2025-05-09 11:46:39'),
(20, 4, '../uploads/offres/681deb1fe12dd_ordre_mission_amani_vendredi_14.docx', 'ordre_mission_amani_vendredi_14.docx', '2025-05-09 11:46:39'),
(21, 4, '../uploads/offres/681deb1fe194c_ordre_mission_amani_vendredi_14.pdf', 'ordre_mission_amani_vendredi_14.pdf', '2025-05-09 11:46:39'),
(22, 4, '../uploads/offres/681deb1fe1f7f_ordre_mission_nicolas.docx', 'ordre_mission_nicolas.docx', '2025-05-09 11:46:39'),
(23, 4, '../uploads/offres/681deb1fe25b2_ordre_mission_nicolas.pdf', 'ordre_mission_nicolas.pdf', '2025-05-09 11:46:39'),
(24, 4, '../uploads/offres/681deb1fe2cde_ordre_mission_nicolas_2.docx', 'ordre_mission_nicolas_2.docx', '2025-05-09 11:46:39'),
(25, 4, '../uploads/offres/681deb1fe3364_ordre_mission_nicolas_2.pdf', 'ordre_mission_nicolas_2.pdf', '2025-05-09 11:46:39'),
(26, 5, '../uploads/offres/681dec71aaac8_confirmation_non_revente_marchandise_senstar.docx', 'confirmation_non_revente_marchandise_senstar.docx', '2025-05-09 11:52:17');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
