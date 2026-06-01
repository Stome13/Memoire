-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 23 mai 2026 à 11:11
-- Version du serveur : 8.4.7
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `pharmalocal`
--

-- --------------------------------------------------------

--
-- Structure de la table `gardes`
--

DROP TABLE IF EXISTS `gardes`;
CREATE TABLE IF NOT EXISTS `gardes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pharmacie_id` int NOT NULL,
  `date_garde` date NOT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pharmacie_id` (`pharmacie_id`,`date_garde`),
  KEY `date_garde` (`date_garde`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `gardes`
--

INSERT INTO `gardes` (`id`, `pharmacie_id`, `date_garde`, `heure_debut`, `heure_fin`) VALUES
(1, 1, '2026-04-29', '22:00:00', '06:00:00'),
(3, 1, '2026-04-30', '08:00:00', '07:59:00'),
(4, 7, '2026-05-07', '22:22:00', '15:55:00'),
(5, 1, '2026-05-22', '11:07:00', '22:13:00');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `details` json DEFAULT NULL,
  `date_action` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `date_action` (`date_action`)
) ENGINE=MyISAM AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `details`, `date_action`) VALUES
(1, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-18 10:50:43'),
(2, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-18 10:59:57'),
(3, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-18 11:06:47'),
(4, 3, 'login_success', '{\"email\": \"marcvht@gmail.com\"}', '2026-04-18 11:11:09'),
(5, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-18 11:23:09'),
(6, 1, 'pharmacy_created', '{\"pharmacy_id\": \"4\"}', '2026-04-18 11:34:12'),
(7, 1, 'pharmacy_created', '{\"pharmacy_id\": \"5\"}', '2026-04-18 11:55:16'),
(8, 1, 'pharmacy_created', '{\"pharmacy_id\": \"6\", \"pharmacien_id\": \"4\"}', '2026-04-18 13:54:01'),
(9, 4, 'login_success', '{\"email\": \"loko@gmail.com\"}', '2026-04-18 13:56:41'),
(10, 4, 'login_success', '{\"email\": \"loko@gmail.com\"}', '2026-04-18 14:21:40'),
(11, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-18 14:26:39'),
(12, 4, 'login_success', '{\"email\": \"loko@gmail.com\"}', '2026-04-18 14:29:32'),
(13, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-18 18:35:18'),
(14, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-18 19:21:23'),
(15, 5, 'login_success', '{\"email\": \"arnoldpierre@gmail.com\"}', '2026-04-20 09:13:59'),
(16, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-20 11:37:34'),
(17, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-20 11:40:23'),
(18, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-20 11:40:36'),
(19, NULL, 'login_failed', '{\"email\": \"Pharmacien@pharmalokal.com\"}', '2026-04-20 11:41:04'),
(20, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-20 11:41:41'),
(21, NULL, 'login_failed', '{\"email\": \"Pharmacien@pharmalokal.com\"}', '2026-04-20 11:41:57'),
(22, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-20 11:43:16'),
(23, NULL, 'login_failed', '{\"email\": \"Pharmadowa@gmail.com\"}', '2026-04-21 13:22:51'),
(24, NULL, 'login_failed', '{\"email\": \"Pharmadowa@gmail.com\"}', '2026-04-21 13:23:16'),
(25, NULL, 'login_failed', '{\"email\": \"pharmadowa@gmail.com\"}', '2026-04-21 13:23:42'),
(26, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-21 13:24:32'),
(27, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-21 13:27:51'),
(28, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-23 16:28:16'),
(29, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-23 16:30:44'),
(30, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-23 16:31:18'),
(31, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-23 16:32:14'),
(32, 10, 'login_success', '{\"email\": \"alie@gmail.com\"}', '2026-04-23 17:54:35'),
(33, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-23 18:00:36'),
(34, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-26 21:43:52'),
(35, 2, 'garde_registered', '{\"garde_id\": \"1\", \"date_garde\": \"2026-04-29\", \"pharmacie_id\": 1}', '2026-04-26 21:48:36'),
(36, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-27 06:30:35'),
(37, 11, 'login_success', '{\"email\": \"paul@gmail.com\"}', '2026-04-27 15:47:38'),
(38, 11, 'profile_updated', 'null', '2026-04-27 15:48:39'),
(39, NULL, 'login_failed', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-27 15:51:10'),
(40, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-04-27 15:51:20'),
(41, 1, 'pharmacy_created', '{\"pharmacy_id\": \"7\", \"pharmacien_id\": \"12\"}', '2026-04-27 15:55:11'),
(42, 12, 'login_success', '{\"email\": \"sam@gmail.com\"}', '2026-04-27 16:00:48'),
(43, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-04-28 12:16:59'),
(44, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-04 20:15:40'),
(45, 2, 'profile_updated', 'null', '2026-05-05 11:12:16'),
(46, 2, 'profile_updated', 'null', '2026-05-05 11:13:46'),
(47, NULL, 'login_failed', '{\"email\": \"jean@gmail.com\"}', '2026-05-05 11:15:23'),
(48, NULL, 'login_failed', '{\"email\": \"$jean@gmail.com\"}', '2026-05-05 11:18:25'),
(49, NULL, 'login_failed', '{\"email\": \"$jean@gmail.com\"}', '2026-05-05 11:18:38'),
(50, 14, 'login_success', '{\"email\": \"$jeanpaul@gmail.com\"}', '2026-05-05 11:18:47'),
(51, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-05 15:00:45'),
(52, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-05 15:08:22'),
(53, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-05 15:09:30'),
(54, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-06 01:01:34'),
(55, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-06 01:03:30'),
(56, 15, 'reservation_created', '{\"reservation_id\": \"1\"}', '2026-05-06 01:05:09'),
(57, 15, 'reservation_created', '{\"reservation_id\": \"2\"}', '2026-05-06 01:05:09'),
(58, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-06 01:10:18'),
(59, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-06 01:20:30'),
(60, 15, 'reservation_created', '{\"reservation_id\": \"5\"}', '2026-05-06 08:15:21'),
(61, 15, 'reservation_deleted', '{\"reservation_id\": \"5\"}', '2026-05-06 08:24:10'),
(62, 15, 'reservation_created', '{\"reservation_id\": \"6\"}', '2026-05-07 08:33:48'),
(63, 15, 'reservation_created', '{\"reservation_id\": \"7\"}', '2026-05-07 08:33:48'),
(64, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 08:38:33'),
(65, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-07 09:56:04'),
(66, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:07:16'),
(67, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:17:34'),
(68, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:17:44'),
(69, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:19:15'),
(70, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:19:25'),
(71, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-07 10:19:40'),
(72, NULL, 'login_failed', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-08 10:01:11'),
(73, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-08 10:01:24'),
(74, NULL, 'login_failed', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-08 10:03:40'),
(75, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-08 10:03:47'),
(76, 2, 'reservation_status_updated', '{\"new_statut\": \"confirmée\", \"reservation_id\": \"6\"}', '2026-05-08 10:28:53'),
(77, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-08 10:29:20'),
(78, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-08 10:29:24'),
(79, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-08 10:29:34'),
(80, 2, 'reservation_status_updated', '{\"new_statut\": \"prête\", \"reservation_id\": \"6\"}', '2026-05-08 10:33:13'),
(81, 2, 'reservation_status_updated', '{\"new_statut\": \"retirée\", \"reservation_id\": \"6\"}', '2026-05-08 10:33:21'),
(82, 2, 'reservation_status_updated', '{\"new_statut\": \"confirmée\", \"reservation_id\": \"3\"}', '2026-05-08 10:36:38'),
(83, 2, 'reservation_status_updated', '{\"new_statut\": \"confirmée\", \"reservation_id\": \"2\"}', '2026-05-08 10:43:27'),
(84, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-08 20:12:16'),
(85, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-09 09:30:57'),
(86, 15, 'reservation_created', '{\"reservation_id\": \"1\"}', '2026-05-09 17:49:21'),
(87, 15, 'reservation_created', '{\"reservation_id\": \"2\"}', '2026-05-09 18:37:47'),
(88, 2, 'reservation_status_updated', '{\"new_statut\": \"confirmée\", \"reservation_id\": \"2\"}', '2026-05-09 20:11:11'),
(89, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 11:03:24'),
(90, NULL, 'login_failed', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-15 11:09:30'),
(91, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-15 11:09:44'),
(92, 1, 'pharmacy_created', '{\"pharmacy_id\": \"8\", \"pharmacien_id\": \"16\"}', '2026-05-15 11:12:52'),
(93, 16, 'login_success', '{\"email\": \"marc@gmail.com\"}', '2026-05-15 11:14:40'),
(94, 15, 'reservation_created', '{\"reservation_id\": \"3\"}', '2026-05-15 11:16:10'),
(95, NULL, 'login_failed', '{\"email\": \"marc@gmail\"}', '2026-05-15 11:48:52'),
(96, NULL, 'login_failed', '{\"email\": \"marc@gmail\"}', '2026-05-15 11:49:00'),
(97, NULL, 'login_failed', '{\"email\": \"marc@gmail\"}', '2026-05-15 11:49:13'),
(98, NULL, 'login_failed', '{\"email\": \"marc@gmail\"}', '2026-05-15 11:49:26'),
(99, NULL, 'login_failed', '{\"email\": \"marc@gmail\"}', '2026-05-15 12:42:21'),
(100, 16, 'login_success', '{\"email\": \"marc@gmail.com\"}', '2026-05-15 12:42:34'),
(101, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:08:01'),
(102, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:08:37'),
(103, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:15:30'),
(104, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:15:40'),
(105, 15, 'password_changed', 'null', '2026-05-15 18:23:04'),
(106, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:23:44'),
(107, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-15 18:23:50'),
(108, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-15 18:34:51'),
(109, 16, 'login_success', '{\"email\": \"marc@gmail.com\"}', '2026-05-15 18:52:18'),
(110, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-16 18:18:37'),
(111, 1, 'pharmacy_created', '{\"pharmacy_id\": \"9\", \"pharmacien_id\": \"17\"}', '2026-05-16 18:24:03'),
(112, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-16 20:08:53'),
(113, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-18 07:57:16'),
(114, 18, 'login_success', '{\"email\": \"ab@gmail.com\"}', '2026-05-18 11:58:00'),
(115, 18, 'login_success', '{\"email\": \"ab@gmail.com\"}', '2026-05-18 11:58:00'),
(116, 18, 'reservation_created', '{\"reservation_id\": \"4\"}', '2026-05-18 11:59:35'),
(117, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-19 09:44:57'),
(118, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-19 09:45:37'),
(119, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-19 09:45:46'),
(120, 15, 'reservation_created', '{\"reservation_id\": \"5\"}', '2026-05-19 13:44:40'),
(121, 15, 'reservation_created', '{\"reservation_id\": \"6\"}', '2026-05-19 13:44:40'),
(122, 2, 'reservation_status_updated', '{\"new_statut\": \"confirmée\", \"reservation_id\": \"5\"}', '2026-05-19 13:45:28'),
(123, 2, 'reservation_status_updated', '{\"new_statut\": \"retirée\", \"reservation_id\": \"5\"}', '2026-05-19 13:45:53'),
(124, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-20 11:50:42'),
(125, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-20 11:50:50'),
(126, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-20 11:52:59'),
(127, 1, 'login_success', '{\"email\": \"admin@pharmalokal.com\"}', '2026-05-20 11:53:36'),
(128, NULL, 'login_failed', '{\"email\": \"vht@gmail.com\"}', '2026-05-20 13:24:32'),
(129, NULL, 'login_failed', '{\"email\": \"vht@gmail.com\"}', '2026-05-20 13:24:45'),
(130, NULL, 'login_failed', '{\"email\": \"vht@gmail.com\"}', '2026-05-20 13:24:57'),
(131, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-20 13:55:26'),
(132, 15, 'reservation_created', '{\"reservation_id\": \"7\"}', '2026-05-20 14:11:03'),
(133, 15, 'reservation_created', '{\"reservation_id\": \"8\"}', '2026-05-20 14:14:22'),
(134, NULL, 'login_failed', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-20 14:27:03'),
(135, 15, 'login_success', '{\"email\": \"charbel13@gmail.com\"}', '2026-05-20 14:27:14'),
(136, 15, 'reservation_created', '{\"reservation_id\": \"9\"}', '2026-05-20 14:28:21'),
(137, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-20 18:24:46'),
(138, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-20 18:26:55'),
(139, 15, 'reservation_created', '{\"reservation_id\": \"10\"}', '2026-05-20 19:01:44'),
(140, 15, 'reservation_created', '{\"reservation_id\": \"11\"}', '2026-05-20 19:02:05'),
(141, 15, 'reservation_created', '{\"reservation_id\": \"12\"}', '2026-05-20 19:02:22'),
(142, 15, 'reservation_created', '{\"reservation_id\": \"13\"}', '2026-05-20 23:08:46'),
(143, 15, 'reservation_created', '{\"reservation_id\": \"14\"}', '2026-05-21 12:17:17'),
(144, 15, 'reservation_created', '{\"reservation_id\": \"15\"}', '2026-05-21 12:25:56'),
(145, 2, 'login_success', '{\"email\": \"pharmacien@pharmalokal.com\"}', '2026-05-21 12:26:54'),
(146, 15, 'reservation_created', '{\"reservation_id\": \"16\"}', '2026-05-21 16:29:28');

-- --------------------------------------------------------

--
-- Structure de la table `medicaments`
--

DROP TABLE IF EXISTS `medicaments`;
CREATE TABLE IF NOT EXISTS `medicaments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dosage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `prix` decimal(10,2) DEFAULT NULL,
  `categorie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_ajout` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `nom` (`nom`),
  KEY `categorie` (`categorie`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `medicaments`
--

INSERT INTO `medicaments` (`id`, `nom`, `dosage`, `description`, `prix`, `categorie`, `date_ajout`) VALUES
(1, 'Paracétamol', '500 mg', 'Utilisé pour soulager la douleur et faire baisser la fièvre.', 1500.00, 'Antalgique', '2026-05-09 10:56:37'),
(2, 'Amoxicilline', '1 g', 'Antibiotique utilisé pour traiter les infections bactériennes.', 3500.00, 'Antibiotique', '2026-05-09 10:56:37'),
(3, 'Ibuprofène', '400 mg', 'Réduit les douleurs, inflammations et la fièvre.', 2000.00, 'Anti-inflammatoire', '2026-05-09 10:56:37'),
(4, 'Metformine', '850 mg', 'Médicament utilisé dans le traitement du diabète de type 2.', 4000.00, 'Antidiabétique', '2026-05-09 10:56:37'),
(5, 'Vitamine C', '500 mg', 'Renforce le système immunitaire et lutte contre la fatigue.', 1200.00, 'Complément alimentaire', '2026-05-09 10:56:38'),
(6, 'Ciprofloxacine', '500 mg', 'Utilisé contre certaines infections bactériennes sévères.', 4500.00, 'Antibiotique', '2026-05-09 10:56:38'),
(7, 'Oméprazole', '20 mg', 'Réduit l\'acidité de l\'estomac et traite les ulcères.', 2800.00, 'Antiacide', '2026-05-09 10:56:38'),
(8, 'Artéméther/Luméfantrine', '480 mg', 'Traitement du paludisme non compliqué.', 5000.00, 'Antipaludique', '2026-05-09 10:56:38'),
(9, 'Salbutamol', '2 mg', 'Facilite la respiration chez les personnes asthmatiques.', 3000.00, 'Bronchodilatateur', '2026-05-09 10:56:38'),
(10, 'Aspirine', '500 mg', 'Utilisé pour les douleurs légères et comme fluidifiant sanguin.', 1000.00, 'Antalgique', '2026-05-09 10:56:38'),
(11, 'Paracétamol', '500 mg', 'Utilisé pour soulager la douleur et faire baisser la fièvre.', 1500.00, 'Antalgique', '2026-05-15 11:15:06'),
(12, 'Amoxicilline', '1 g', 'Antibiotique utilisé pour traiter les infections bactériennes.', 3500.00, 'Antibiotique', '2026-05-15 11:15:06'),
(13, 'Ibuprofène', '400 mg', 'Réduit les douleurs, inflammations et la fièvre.', 2000.00, 'Anti-inflammatoire', '2026-05-15 11:15:06'),
(14, 'Metformine', '850 mg', 'Médicament utilisé dans le traitement du diabète de type 2.', 4000.00, 'Antidiabétique', '2026-05-15 11:15:06'),
(15, 'Vitamine C', '500 mg', 'Renforce le système immunitaire et lutte contre la fatigue.', 1200.00, 'Complément alimentaire', '2026-05-15 11:15:06'),
(16, 'Ciprofloxacine', '500 mg', 'Utilisé contre certaines infections bactériennes sévères.', 4500.00, 'Antibiotique', '2026-05-15 11:15:06'),
(17, 'Oméprazole', '20 mg', 'Réduit l\'acidité de l\'estomac et traite les ulcères.', 2800.00, 'Antiacide', '2026-05-15 11:15:06'),
(18, 'Artéméther/Luméfantrine', '80/480 mg', 'Traitement du paludisme non compliqué.', 5000.00, 'Antipaludique', '2026-05-15 11:15:06'),
(19, 'Salbutamol', '2 mg', 'Facilite la respiration chez les personnes asthmatiques.', 3000.00, 'Bronchodilatateur', '2026-05-15 11:15:06'),
(20, 'Aspirine', '500 mg', 'Utilisé pour les douleurs légères et comme fluidifiant sanguin.', 1000.00, 'Antalgique', '2026-05-15 11:15:06');

-- --------------------------------------------------------

--
-- Structure de la table `pharmacies`
--

DROP TABLE IF EXISTS `pharmacies`;
CREATE TABLE IF NOT EXISTS `pharmacies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `horaire_ouverture` time DEFAULT NULL,
  `horaire_fermeture` time DEFAULT NULL,
  `pharmacien_id` int DEFAULT NULL,
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ifu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pharmacien_id` (`pharmacien_id`),
  KEY `nom` (`nom`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `pharmacies`
--

INSERT INTO `pharmacies` (`id`, `nom`, `adresse`, `telephone`, `email`, `horaire_ouverture`, `horaire_fermeture`, `pharmacien_id`, `date_inscription`, `ville`, `ifu`) VALUES
(1, 'Pharmacie Centrale', '123 Rue de la Paix', '0123456789', 'central@pharmalokal.com', '08:00:00', '20:00:00', 2, '2026-04-18 10:48:49', NULL, NULL),
(2, 'Pharmacie du Plateau', '456 Avenue du Commerce', '0987654321', 'plateau@pharmalokal.com', '08:30:00', '19:30:00', NULL, '2026-04-18 10:48:49', NULL, NULL),
(3, 'Pharmacie du Port', '789 Boulevard Maritime', '0555555555', 'port@pharmalokal.com', '08:00:00', '21:00:00', NULL, '2026-04-18 10:48:49', NULL, NULL),
(4, 'Pharmciedodji', 'Dodji', '0185754896', NULL, NULL, NULL, NULL, '2026-04-18 11:34:12', NULL, NULL),
(5, 'Pharmciedowagbago', 'Dowa gbago', '0178954848', 'Pharmadowagbago@gmail.com', '08:00:00', '18:00:00', 2, '2026-04-18 11:55:16', NULL, NULL),
(6, 'Pharmacie dowa centre', 'Dowa centre', '0155761830', 'Pharmadowacentre@gmail.com', '08:00:00', '18:00:00', 4, '2026-04-18 13:54:01', 'Porto-Novo', NULL),
(7, 'PHARMACIE JEANPAUL', 'BP 01673', '0156100070', 'ph@gmail.com', '00:00:00', '22:22:00', 12, '2026-04-27 15:55:11', 'PARIS', NULL),
(8, 'Les palmiers', 'Ouando', '0120586453', 'palmier@gmail.com', '08:00:00', '18:00:00', 16, '2026-05-15 11:12:52', 'Porto-Novo', NULL),
(9, 'Vakon', 'vakon', '+2290120356548', 'vakon@gmail.com', '08:00:00', '18:00:00', 17, '2026-05-16 18:24:03', 'Missérété', '12345678910111213');

-- --------------------------------------------------------

--
-- Structure de la table `reservations`
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE IF NOT EXISTS `reservations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `medicament_id` int NOT NULL,
  `pharmacie_id` int NOT NULL,
  `quantite` int NOT NULL,
  `date_reservation` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_retrait` datetime DEFAULT NULL,
  `statut` enum('en attente','confirmée','prête','retirée','annulée') COLLATE utf8mb4_unicode_ci DEFAULT 'en attente',
  `notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `medicament_id` (`medicament_id`),
  KEY `user_id` (`user_id`),
  KEY `pharmacie_id` (`pharmacie_id`),
  KEY `statut` (`statut`),
  KEY `date_reservation` (`date_reservation`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `medicament_id`, `pharmacie_id`, `quantite`, `date_reservation`, `date_retrait`, `statut`, `notes`) VALUES
(1, 15, 7, 1, 6, '2026-05-09 17:49:21', NULL, 'confirmée', NULL),
(2, 15, 4, 1, 1, '2026-05-09 18:37:47', NULL, 'confirmée', NULL),
(3, 15, 20, 8, 2, '2026-05-15 11:16:10', NULL, 'en attente', NULL),
(4, 18, 17, 8, 1, '2026-05-18 11:59:35', NULL, 'en attente', NULL),
(5, 15, 7, 1, 14, '2026-05-19 13:44:40', '2026-05-19 12:45:53', 'retirée', NULL),
(6, 15, 17, 8, 1, '2026-05-19 13:44:40', NULL, 'en attente', NULL),
(7, 15, 10, 1, 2, '2026-05-20 14:11:03', NULL, 'confirmée', NULL),
(8, 15, 1, 1, 1, '2026-05-20 14:14:22', NULL, 'confirmée', NULL),
(9, 15, 10, 1, 1, '2026-05-20 14:28:21', NULL, 'en attente', NULL),
(10, 15, 10, 1, 1, '2026-05-20 19:01:44', NULL, 'en attente', NULL),
(11, 15, 10, 1, 1, '2026-05-20 19:02:05', NULL, 'en attente', NULL),
(12, 15, 10, 1, 1, '2026-05-20 19:02:22', NULL, 'en attente', NULL),
(13, 15, 10, 1, 91, '2026-05-20 23:08:46', NULL, 'en attente', NULL),
(14, 15, 10, 1, 1, '2026-05-21 12:17:17', NULL, 'en attente', NULL),
(15, 15, 10, 1, 1, '2026-05-21 12:25:56', NULL, 'en attente', NULL),
(16, 15, 10, 1, 2, '2026-05-21 16:29:28', NULL, 'en attente', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pharmacie_id` int NOT NULL,
  `medicament_id` int NOT NULL,
  `quantite` int DEFAULT '0',
  `date_modification` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pharmacie_id` (`pharmacie_id`,`medicament_id`),
  KEY `pharmacie_id_2` (`pharmacie_id`),
  KEY `medicament_id` (`medicament_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `stocks`
--

INSERT INTO `stocks` (`id`, `pharmacie_id`, `medicament_id`, `quantite`, `date_modification`) VALUES
(1, 1, 1, 120, NULL),
(2, 1, 2, 70, '2026-05-18 10:57:07'),
(3, 1, 3, 85, NULL),
(4, 1, 4, 0, '2026-05-09 20:04:42'),
(5, 1, 5, 150, NULL),
(6, 1, 6, 35, NULL),
(7, 1, 7, 70, NULL),
(8, 1, 8, 55, NULL),
(9, 1, 9, 25, NULL),
(10, 1, 10, 95, NULL),
(11, 8, 11, 120, NULL),
(12, 8, 12, 50, '2026-05-15 12:44:09'),
(13, 8, 13, 85, NULL),
(14, 8, 14, 40, NULL),
(15, 8, 15, 150, NULL),
(16, 8, 16, 35, NULL),
(17, 8, 17, 70, NULL),
(18, 8, 18, 55, NULL),
(19, 8, 19, 25, NULL),
(20, 8, 20, 95, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` text COLLATE utf8mb4_unicode_ci,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('client','admin','pharmacie') COLLATE utf8mb4_unicode_ci DEFAULT 'client',
  `date_inscription` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_modification` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `email_2` (`email`),
  KEY `role` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `password`, `role`, `date_inscription`, `date_modification`) VALUES
(1, 'Admin', 'Système', 'admin@pharmalokal.com', NULL, NULL, '$2y$10$yacFOEtDrS0FpDg1xnlr8.VgUTmz5I2LUcInuFTcARUbZ7GDwQLWG', 'admin', '2026-04-18 10:48:49', '2026-04-18 10:57:14'),
(2, 'Sani', 'Jean', 'pharmacien@pharmalokal.com', '34569KLMPOIUGNNG', 'Porto-Novo', '$2y$10$7vCQxt5DNBM4XLedYy.sWOljx3gpYJj7ffv979GVkVE/1xFYyL0vy', 'pharmacie', '2026-04-18 10:48:49', '2026-05-05 11:13:46'),
(14, 'da$ilva', 'Jeanpaul', '$jeanpaul@gmail.com', '+229 0156100070', NULL, '$2y$10$V3tLwqs5fGiYaqIcYKWhKen9nRR7dMqyzchhTgsSky2ysYj0EpDz2', 'client', '2026-05-05 11:18:03', NULL),
(4, 'Loko', 'Samson', 'loko@gmail.com', '0153813102', 'Dowa centre', '$2y$10$0yk74b9cJgxND9mrJSbuPOFzS9PXtJgwiUtV0asDjVBFrF2N5u.Nm', 'pharmacie', '2026-04-18 13:54:01', NULL),
(15, 'VHT', 'charvbo', 'charbel13@gmail.com', '0155895621', NULL, '$2y$10$kMVjZVUgYf/0HKTz46KKuui5aSM34frpTUO6Sf1IoLqbYGvIYfNm.', 'client', '2026-05-06 01:03:15', '2026-05-15 18:23:04'),
(16, 'Dossou', 'Marc', 'marc@gmail.com', '0197803156', 'Ouando', '$2y$10$HdBRp2kfpwFr9kDHHX9B0Obbd3pFaIBdIkjenGIP8Q/6veuUknuIC', 'pharmacie', '2026-05-15 11:12:52', NULL),
(17, 'Dossa', 'Paul', 'paul@gmail.com', '0196566234', 'vakon', '$2y$10$veKAxMZiUIG6THcF4ARL2eo.JA7ypv99d0FTD8Ic.ggD.r3idiT.C', 'pharmacie', '2026-05-16 18:24:03', NULL),
(18, 'bbb', 'aaa', 'ab@gmail.com', '0155895621', NULL, '$2y$10$NhevEuBkBVV6/AEDzosGM..el8GcqDrBjaHaN7doGDZ51bTyUrCJ.', 'client', '2026-05-18 11:57:20', NULL),
(19, 'VHT', 'Ja', 'vht@gmail.com', '+22901655898', NULL, '$2y$10$Lg/ioBlv4CMHUfleRFrf7OSE3cMXkgAgOT.ut/RODC6Lyq0to2cnq', 'client', '2026-05-20 13:23:59', NULL),
(20, 'Stone', 'Luc', 'stone@gmail.com', '+2290165589860', 'Dowa, Porto-Novo', '$2y$10$uvQzFCZapXXqiz1R7YHjL.ztULyiq1hV.IZ8HOq68MMu3hECSehNW', 'client', '2026-05-20 13:46:29', NULL),
(12, 'LOKO', 'SAMSON agbegui', 'sam@gmail.com', '0164895999', 'BP 01673', '$2y$10$r6ZFDPJw70myJjaP2NK2Ouwn11caYFHFqaUaCFOotcldUDq0/pMZe', 'pharmacie', '2026-04-27 15:55:11', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
