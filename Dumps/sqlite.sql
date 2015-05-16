CREATE TABLE `Bans` (
  `Id` integer primary key autoincrement,
  `Ip` text  NOT NULL,
  `Reason` text  NOT NULL,
  `BanDateTime` datetime NOT NULL
) ;

CREATE TABLE `FileSystem` (
  `id` integer primary key autoincrement,
  `sizeInByte` int(11) NOT NULL DEFAULT '0',
  `filePath` text ,
  `displayName` text  NOT NULL,
  `uploadDateTime` datetime NOT NULL,
  `lastChangeDateTime` datetime NOT NULL,
  `uploadUserAgent` text  NOT NULL,
  `hash` text  NOT NULL,
  `ownerId` int(11) NOT NULL,
  `parentFolder` int(11) NOT NULL,
  `mimeType` text  NOT NULL
)  ;

CREATE TABLE `Role` (
  `id` integer primary key autoincrement,
  `description` text ,
  `permissions` text ,
  `IsDefault` tinyint(4) NOT NULL DEFAULT '0'
)  ;

INSERT INTO `Role` (`id`, `description`, `permissions`, `IsDefault`) VALUES
(1, 'Root', '11111111111', 0),
(2, 'Users', '11111111101', 1);

CREATE TABLE `Session` (
  `id` integer primary key autoincrement,
  `userID` int(11) NOT NULL,
  `token` text  NOT NULL,
  `sessionStartedDateTime` datetime NOT NULL,
  `sessionEndDateTime` datetime DEFAULT NULL
)  ;

CREATE TABLE `Settings` (
  `ID` integer primary key autoincrement,
  `SettingName` text  NOT NULL,
  `SettingType` text  NOT NULL,
  `SettingValue` text  NOT NULL
)  ;

INSERT INTO `Settings` (`ID`, `SettingName`, `SettingType`, `SettingValue`) VALUES
(1, 'Enable_Register', 'Boolean', 'false'),
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
(12, 'Max_User_Storage', 'Number', '10000000');

CREATE TABLE `SharedFileSystem` (
  `id` integer primary key autoincrement,
  `entryID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `targetUserID` int(11) DEFAULT NULL,
  `permissions` text ,
  `shareCode` text ,
  `shared` datetime NOT NULL
) ;

CREATE TABLE `User` (
  `Id` integer primary key autoincrement,
  `loginName` text ,
  `displayName` text ,
  `mailAddress` text ,
  `registrationDateTime` datetime DEFAULT NULL,
  `lastLoginDateTime` datetime DEFAULT NULL,
  `passwordHash` text ,
  `isEnabled` tinyint(1) DEFAULT NULL,
  `contingentInByte` bigint(11) DEFAULT NULL,
  `roleID` int(11) DEFAULT NULL,
  `failedLogins` int(11) NOT NULL
)  ;

INSERT INTO `User` (`id`, `loginName`, `displayName`, `mailAddress`, `registrationDateTime`, `lastLoginDateTime`, `passwordHash`, `isEnabled`, `contingentInByte`, `roleID`, `failedLogins`) VALUES
(1, 'fury', 'Administrator', 'me@0fury.de', '2015-03-07 20:42:01', '2015-03-07 20:42:16', '$2y$11$hEF1r6lt1vDNShJe5ti0EetU9apyLJvKpe4gxhPim7/FWWUrD2RHK', 1, 5242880, 1, 0);

