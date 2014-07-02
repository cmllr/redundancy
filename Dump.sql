-- phpMyAdmin SQL Dump
-- version 4.1.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. Jun 2014 um 20:56
-- Server Version: 5.5.37-MariaDB
-- PHP-Version: 5.5.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `Lenticularis`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `FileSystem`
--

CREATE TABLE IF NOT EXISTS `FileSystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sizeInByte` int(11) NOT NULL DEFAULT '0',
  `filePath` text COLLATE utf8_bin,
  `displayName` text COLLATE utf8_bin NOT NULL,
  `uploadDateTime` datetime NOT NULL,
  `lastChangeDateTime` datetime NOT NULL,
  `uploadUserAgent` text COLLATE utf8_bin NOT NULL,
  `hash` text COLLATE utf8_bin NOT NULL,
  `ownerId` int(11) NOT NULL,
  `parentFolder` int(11) NOT NULL,
  `mimeType` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ownerId` (`ownerId`),
  KEY `parentFolder` (`parentFolder`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=286 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Role`
--

CREATE TABLE IF NOT EXISTS `Role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_bin,
  `permissions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `Role`
--

INSERT INTO `Role` (`id`, `description`, `permissions`) VALUES
(1, 'Root', '111111111');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `Session`
--

CREATE TABLE IF NOT EXISTS `Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `token` text COLLATE utf8_bin NOT NULL,
  `sessionStartedDateTime` datetime NOT NULL,
  `sessionEndDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=65 ;

--
-- Daten für Tabelle `Session`
--

INSERT INTO `Session` (`id`, `userID`, `token`, `sessionStartedDateTime`, `sessionEndDateTime`) VALUES
(23, 9, '7f0dd90a3009619b041e52589008df90', '2014-06-30 18:22:22', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loginName` text COLLATE utf8_bin,
  `displayName` text COLLATE utf8_bin,
  `mailAddress` text COLLATE utf8_bin,
  `registrationDateTime` datetime DEFAULT NULL,
  `lastLoginDateTime` datetime DEFAULT NULL,
  `passwordHash` text COLLATE utf8_bin,
  `isEnabled` tinyint(1) DEFAULT NULL,
  `contingentInByte` int(11) DEFAULT NULL,
  `roleID` int(11) DEFAULT NULL,
  `failedLogins` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Role` (`roleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=53 ;

--
-- Daten für Tabelle `User`
--

INSERT INTO `User` (`id`, `loginName`, `displayName`, `mailAddress`, `registrationDateTime`, `lastLoginDateTime`, `passwordHash`, `isEnabled`, `contingentInByte`, `roleID`, `failedLogins`) VALUES
(1, 'fury', 'CM', 'bla@bla.de', '2014-05-16 00:00:00', '2014-05-16 00:00:00', 'bla', 1, 100000, NULL, 0),
(9, 'fuxry', 'CM', 'bla@blxa.de', '2014-05-17 14:12:59', '2014-06-30 18:22:23', '$2y$11$KY/Tjuko2xX/4WqhhyYj6.FnzdN/9Ui7D2JUUujy/bPSVKheUoKJO', 1, 5242880, 1, 0);

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `FileSystem`
--
ALTER TABLE `FileSystem`
  ADD CONSTRAINT `FileSystem_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `User` (`id`);

--
-- Constraints der Tabelle `Session`
--
ALTER TABLE `Session`
  ADD CONSTRAINT `Session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

--
-- Constraints der Tabelle `User`
--
ALTER TABLE `User`
  ADD CONSTRAINT `fk_Role` FOREIGN KEY (`roleID`) REFERENCES `Role` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
