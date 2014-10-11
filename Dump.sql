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
  `permissions` text COLLATE utf8_bin,
  `IsDefault` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `Role` (`id`, `description`, `permissions`, `IsDefault`) VALUES
(1, 'Root', '1111111111', 1);
CREATE TABLE IF NOT EXISTS `Session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `token` text COLLATE utf8_bin NOT NULL,
  `sessionStartedDateTime` datetime NOT NULL,
  `sessionEndDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
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
(1, 'root', 'root', 'info@rdcy.de', '2014-09-21 15:01:42', '2014-10-11 11:06:59', '', 1, 1, 1, 0),
(99, 'testFS', 'testFS', 'jfafalfjl', '2014-10-27 00:00:00', '2014-10-11 11:12:40', '$2y$11$V0Fhy/2nVYpmT9RmPyaj3eiXKuE.Vb9tKUABb6Dylh.r8RKv/LROW', 1, 42424320, 1, 0);


ALTER TABLE `FileSystem`
  ADD CONSTRAINT `FileSystem_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `User` (`id`);

ALTER TABLE `Session`
  ADD CONSTRAINT `Session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `SharedFileSystem`
  ADD CONSTRAINT `SharedFileSystem_ibfk_1` FOREIGN KEY (`entryID`) REFERENCES `FileSystem` (`id`),
  ADD CONSTRAINT `SharedFileSystem_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `User`
  ADD CONSTRAINT `fk_Role` FOREIGN KEY (`roleID`) REFERENCES `Role` (`id`);
