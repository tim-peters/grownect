-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 20. Jan 2015 um 19:04
-- Server Version: 5.6.16
-- PHP-Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `s3`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `conflicts`
--

CREATE TABLE IF NOT EXISTS `conflicts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solved` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL,
  `created_with` int(11) NOT NULL,
  `moment_used` int(11) NOT NULL,
  `progress` int(11) NOT NULL,
  `weight` int(11) NOT NULL,
  `description` text NOT NULL,
  `improvements` text NOT NULL,
  `time_costs` int(11) NOT NULL,
  `explanation` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Daten für Tabelle `conflicts`
--

INSERT INTO `conflicts` (`id`, `created`, `solved`, `created_by`, `created_with`, `moment_used`, `progress`, `weight`, `description`, `improvements`, `time_costs`, `explanation`) VALUES
(1, '2015-01-07 19:30:35', '0000-00-00 00:00:00', 2, 1, 0, 1, 0, 'erstes Problem', '', 0, ''),
(2, '2015-01-07 19:31:18', '0000-00-00 00:00:00', 3, 1, 0, 0, 0, 'zweitest Problem', '', 0, ''),
(3, '2015-01-07 19:31:18', '0000-00-00 00:00:00', 1, 3, 0, 2, 0, 'drittes Problem', '', 0, ''),
(4, '2015-01-16 10:50:39', '0000-00-00 00:00:00', 3, 3, 0, 1, 0, '', '', 0, ''),
(5, '2015-01-20 13:38:00', '0000-00-00 00:00:00', 2, 3, 0, 1, 0, '', '', 0, ''),
(6, '2015-01-20 13:40:21', '0000-00-00 00:00:00', 2, 1, 0, 2, 0, '', '', 0, ''),
(7, '2015-01-20 13:50:43', '0000-00-00 00:00:00', 2, 1, 0, 1, 0, '', '', 0, ''),
(23, '2015-01-20 16:49:23', '0000-00-00 00:00:00', 1, 2, 3, 1, 0, '', '', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moments`
--

CREATE TABLE IF NOT EXISTS `moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `created_with` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `path` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `moments`
--

INSERT INTO `moments` (`id`, `created_by`, `created_with`, `type`, `path`, `content`, `rating`) VALUES
(1, 3, 1, 0, '', 'hello', 0),
(2, 1, 2, 0, '', 'Moment zwischen 1 und 2. Erstellt von 2', 30),
(3, 2, 1, 0, '', 'Konflikt zwischen 2 und 1. Erstellt bei 2', 0);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moments_use`
--

CREATE TABLE IF NOT EXISTS `moments_use` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moment` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

--
-- Daten für Tabelle `moments_use`
--

INSERT INTO `moments_use` (`id`, `moment`, `user`, `used`) VALUES
(1, 2, 1, '2015-01-19 15:28:24'),
(2, 2, 2, '2015-01-19 15:28:24'),
(3, 2, 2, '2015-01-19 15:28:35'),
(18, 2, 2, '2015-01-20 14:08:53'),
(19, 2, 1, '2015-01-20 17:29:50'),
(20, 3, 1, '2015-01-20 17:33:07');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tech_id` varchar(512) NOT NULL,
  `name` varchar(128) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(20) NOT NULL,
  `picture` varchar(256) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `initialized` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tech_id` (`tech_id`,`picture`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `tech_id`, `name`, `description`, `color`, `picture`, `created`, `last_modified`, `initialized`) VALUES
(1, '12345techid', 'Tim Scheller', 'Der beste', '#ff0000', './img/user.png', '2015-01-07 18:56:02', '2015-01-09 18:44:44', 1),
(2, 'dfgdfgfdsg', 'Nadine Peters', 'Hi Folks!', '#44ff44', './img/user.png', '2015-01-09 12:46:29', '2015-01-09 18:46:21', 1),
(3, 'dhgffjdfhj', 'Doreen Mlakar', 'sdfasdf sdfdsaf', '#0000ff', './img/user.png', '2015-01-09 12:46:29', '2015-01-09 18:44:57', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
