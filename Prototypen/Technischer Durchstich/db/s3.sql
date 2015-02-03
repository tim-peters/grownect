-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 03. Feb 2015 um 17:39
-- Server Version: 5.6.16
-- PHP-Version: 5.5.11

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Daten für Tabelle `conflicts`
--

INSERT INTO `conflicts` (`id`, `created`, `solved`, `created_by`, `created_with`, `moment_used`, `progress`, `weight`, `description`, `improvements`, `time_costs`, `explanation`) VALUES
(3, '2015-02-03 16:35:14', '0000-00-00 00:00:00', 3, 1, 0, 6, 76, 'beeing in abetter mood', '', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moments`
--

CREATE TABLE IF NOT EXISTS `moments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(512) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_with` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `path` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
(1, '3b6b1192472a51adc2e7054f16a8ceba', 'Doreen Scheller', 'Ich bin sehr ordentlich organisiert und ja es reicht.', '#acda8d', './img/moments/4e1ee2a22b10baa12bdac47c44ea90ed_bca4424174d209ad38efc7dd49667eaf_8df7a6c0b85fef40c47bbfb6468ca708_tmp_21353-IMG_20150124_141704~2-184854930.jpg', '2015-02-03 16:31:12', '2015-02-03 16:31:12', 1),
(2, '82f585baeae099c070aba8457633ca21', 'Tim J. Peters', 'I''m the coder :)', '#f59556', './img/moments/296240a2286b75ed06102226f859b3a2_2015-02-03 17.32.43.jpg', '2015-02-03 16:33:24', '2015-02-03 16:33:24', 1),
(3, '76cf4ecb943fc5282061fffd96ff4df9', 'Nadine Mlakar', 'Ich mag Rothaarige', '#8d1c1c', './img/moments/3a6a5ed927dc5ce3a0a9ce5ac4945be4_de0c2ea7bf798d79451c766faa29eeee_a29b360ffb38e9434c08a4af85c01656_2eb3d648295c14c2055a39fe25af9b8c_2015-01-28 16.00.39.jpg', '2015-02-03 16:34:13', '2015-02-03 16:34:13', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
