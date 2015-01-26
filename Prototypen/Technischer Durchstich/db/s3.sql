-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 26. Jan 2015 um 17:13
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=39 ;

--
-- Daten für Tabelle `conflicts`
--

INSERT INTO `conflicts` (`id`, `created`, `solved`, `created_by`, `created_with`, `moment_used`, `progress`, `weight`, `description`, `improvements`, `time_costs`, `explanation`) VALUES
(24, '2015-01-22 21:41:20', '0000-00-00 00:00:00', 2, 3, 0, 1, 0, '', '', 0, ''),
(25, '2015-01-23 14:33:01', '0000-00-00 00:00:00', 1, 2, 0, 1, 0, '', '', 0, ''),
(38, '2015-01-26 15:13:09', '0000-00-00 00:00:00', 1, 3, 0, 1, 0, '', '', 0, '');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `moments`
--

INSERT INTO `moments` (`id`, `created_by`, `created_with`, `type`, `path`, `content`, `rating`) VALUES
(1, 3, 1, 0, '', 'hello', 0),
(2, 1, 2, 0, '', 'Moment zwischen 1 und 2. Erstellt von 2', 30),
(3, 2, 1, 0, '', 'Konflikt zwischen 2 und 1. Erstellt bei 2', 0),
(5, 1, 2, 1, './api/files/87e5b1559d104c16f2d812795c814d08/tmp_23200-IMG-20150122-WA0009450471060.jpg', '', 25);

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
(1, 2, 1, '2015-01-19 15:28:24');

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
(1, '12345techid', 'Tim Scheller', 'Der beste', '#ff0000', './img/users/8df7a6c0b85fef40c47bbfb6468ca708_tmp_21353-IMG_20150124_141704~2-184854930.jpg', '2015-01-07 18:56:02', '0000-00-00 00:00:00', 1),
(2, 'dfgdfgfdsg', 'Nadine Peters', 'Hi Folks!', '#44ff44', './img/user.png', '2015-01-09 12:46:29', '2015-01-09 18:46:21', 1),
(3, 'dhgffjdfhj', 'Doreen Mlakar', 'sdfasdf sdfdsaf', '#0000ff', './img/user.png', '2015-01-09 12:46:29', '2015-01-09 18:44:57', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
