# Latest table structure dump. 
CREATE TABLE IF NOT EXISTS `Banned` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` text,
  `Client` text,
  `Date` text,
  `Reason` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `Files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Filename` text CHARACTER SET utf8,
  `Displayname` text CHARACTER SET utf8,
  `Filename_only` text CHARACTER SET utf8,
  `Hash` text CHARACTER SET utf8,
  `UserID` int(11) NOT NULL,
  `IP` text CHARACTER SET utf8 NOT NULL,
  `Uploaded` datetime NOT NULL,
  `Size` int(11) NOT NULL,
  `Directory` text CHARACTER SET utf8 NOT NULL,
  `Directory_ID` int(11) DEFAULT NULL,
  `Client` text CHARACTER SET latin1,
  `ReadOnly` tinyint(1) NOT NULL,
  `MimeType` text CHARACTER SET latin1,
  `Bin` tinyint(1) NOT NULL DEFAULT '0',
  `Crypted` int(11) NOT NULL,
  `lastWrite` datetime NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

CREATE TABLE IF NOT EXISTS `LocalShare` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FileID` int(11) NOT NULL,
  `TargetUser` int(11) NOT NULL,
  `Mode` int(11) NOT NULL COMMENT 'See CHMOD',
  PRIMARY KEY (`ID`),
  KEY `FileID` (`FileID`),
  KEY `TargetUser` (`TargetUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `Mails` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `Pass_History` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Changed` text COLLATE utf8_bin,
  `IP` text COLLATE utf8_bin NOT NULL,
  `Who` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS `Settings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `User_NoLogout_Warning` int(11) NOT NULL,
  `Program_Display_Icons_if_needed` int(11) NOT NULL,
  `Program_Enable_JQuery` int(11) NOT NULL,
  `Program_Enable_Preview` int(11) NOT NULL,
  `Program_Enable_KeyHooks` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


CREATE TABLE IF NOT EXISTS `Share` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Hash` text NOT NULL,
  `UserID` int(11) NOT NULL,
  `Extern_ID` text NOT NULL,
  `Used` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  KEY `ID_2` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `Users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User` text,
  `Email` text,
  `Password` text,
  `Salt` text,
  `Registered` text,
  `Role` text,
  `Storage` int(11) DEFAULT NULL,
  `Enabled` tinyint(1) NOT NULL,
  `API_Key` text NOT NULL,
  `Enable_API` int(11) DEFAULT NULL,
  `Failed_Logins` int(11) NOT NULL DEFAULT '0',
  `Session_Closed` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;
