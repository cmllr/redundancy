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
(1, 'Root', '111111111111', 1);
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
  `contingentInByte` bigint(11) DEFAULT NULL,
  `roleID` int(11) DEFAULT NULL,
  `failedLogins` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_Role` (`roleID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
CREATE TABLE IF NOT EXISTS `Bans` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Ip` text COLLATE utf8_bin NOT NULL,
  `Reason` text COLLATE utf8_bin NOT NULL,
  `BanDateTime` datetime NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
INSERT INTO `User` (`id`, `loginName`, `displayName`, `mailAddress`, `registrationDateTime`, `lastLoginDateTime`, `passwordHash`, `isEnabled`, `contingentInByte`, `roleID`, `failedLogins`) VALUES
(1, 'test', 'Administrator', 'test@bla.de', '2014-09-21 15:33:45', '2014-10-18 12:48:49', '$2y$11$.Vz58QMNFzqfUsRIrzXkAesBXW8kGSNfwOMmv3tURMqf3IdArZypm', 1, 5242880, 1, 0);
CREATE TABLE IF NOT EXISTS `Settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SettingName` text COLLATE utf8_bin NOT NULL,
  `SettingType` text COLLATE utf8_bin NOT NULL,
  `SettingValue` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `PasswordRecoveries` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Token` text COLLATE utf8_bin NOT NULL,
  `TokenEndDateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `UserId` (`UserId`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


INSERT INTO `Settings` (`ID`, `SettingName`, `SettingType`, `SettingValue`) VALUES
(1, 'Enable_Register', 'Boolean', 'true'),
(2, 'Program_Storage_Dir', 'Text', 'Storage'),
(3, 'Program_Temp_Dir', 'Text', 'Temp'),
(4, 'Program_Snapshots_Dir', 'Text', 'Snapshots'),
(5, 'Program_XSS_Timeout', 'Number', '20'),
(6, 'Program_Name', 'Text', 'Redundancy'),
(7, 'User_Contingent', 'Number', '5'),
(8, 'User_Recover_Password_Length', 'Number', '10'),
(9, 'Program_Session_Timeout', 'Number', '300'),
(10, 'Program_Share_Link_Length', 'Number', '7'),
(11, 'Program_Language', 'Text', 'en'),
(12, 'Max_User_Storage','Number','10000000');

Update Role set permissions = '111111111111' where id = 1;

Update User set isEnabled = '0' where loginName = 'testFS';

Replace into Settings (`ID`, `SettingName`, `SettingType`, `SettingValue`) VALUES (12, 'Max_User_Storage','Number','10000000');

ALTER TABLE `FileSystem`
  ADD CONSTRAINT `FileSystem_ibfk_1` FOREIGN KEY (`ownerId`) REFERENCES `User` (`id`);

ALTER TABLE `Session`
  ADD CONSTRAINT `Session_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `SharedFileSystem`
  ADD CONSTRAINT `SharedFileSystem_ibfk_1` FOREIGN KEY (`entryID`) REFERENCES `FileSystem` (`id`),
  ADD CONSTRAINT `SharedFileSystem_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `User` (`id`);

ALTER TABLE `User`
  ADD CONSTRAINT `fk_Role` FOREIGN KEY (`roleID`) REFERENCES `Role` (`id`);

ALTER TABLE `PasswordRecoveries`
  ADD CONSTRAINT `PasswordRecoveries_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `User` (`id`);
