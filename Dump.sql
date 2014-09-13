SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `Role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text COLLATE utf8_bin,
  `permissions` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

INSERT INTO `Role` (`id`, `description`, `permissions`) VALUES
(1, 'Root', 111111111);

CREATE TABLE IF NOT EXISTS `Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `token` text COLLATE utf8_bin NOT NULL,
  `sessionStartedDateTime` datetime NOT NULL,
  `sessionEndDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

CREATE TABLE IF NOT EXISTS `SharedFileSystem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entryID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `targetUserID` int(11) DEFAULT NULL,
  `permissions` text COLLATE utf8_bin,
  `shareCode` text COLLATE utf8_bin,
  `shared` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entryID` (`entryID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `User` (`id`, `loginName`, `displayName`, `mailAddress`, `registrationDateTime`, `lastLoginDateTime`, `passwordHash`, `isEnabled`, `contingentInByte`, `roleID`, `failedLogins`) VALUES
(9, 'fuxry', 'CM', 'bla@blxa.de', '2014-05-17 14:12:59', '2014-07-13 11:25:47', '$2y$11$KY/Tjuko2xX/4WqhhyYj6.FnzdN/9Ui7D2JUUujy/bPSVKheUoKJO', 1, 5242880, 1, 0),
(84, 'testFS', 'FileSystemTestUser', 'test@fs.local', '2014-07-06 15:52:01', '2014-07-20 13:05:22', '$2y$11$y1ldtxNAWEa6HTOWkHQtRuwU1pagqX0GydlLx2RMpPx5KLa7zizqK', 1, 5242880, 1, 0);


ALTER TABLE `FileSystem`
  ADD CONSTRAINT `FileSystem_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `User` (`id`);

ALTER TABLE `Session`
  ADD CONSTRAINT `Session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `SharedFileSystem`
  ADD CONSTRAINT `SharedFileSystem_ibfk_1` FOREIGN KEY (`entryID`) REFERENCES `FileSystem` (`id`),
  ADD CONSTRAINT `SharedFileSystem_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `User`
  ADD CONSTRAINT `fk_Role` FOREIGN KEY (`roleID`) REFERENCES `Role` (`id`);

