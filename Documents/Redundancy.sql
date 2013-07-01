SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `Banned` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` text,
  `Client` text,
  `Date` text,
  `Reason` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

CREATE TABLE IF NOT EXISTS `Files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Filename` text CHARACTER SET utf8 COLLATE utf8_bin,
  `Displayname` text CHARACTER SET utf8 COLLATE utf8_bin,
  `Filename_only` text CHARACTER SET utf8 COLLATE utf8_bin,
  `Hash` text CHARACTER SET utf8 COLLATE utf8_bin,
  `UserID` int(11) NOT NULL,
  `IP` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Uploaded` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Size` int(11) NOT NULL,
  `Directory` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `Directory_ID` int(11) DEFAULT NULL,
  `Client` text,
  `ReadOnly` tinyint(1) NOT NULL,
  `MimeType` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1252 ;
CREATE TABLE IF NOT EXISTS `Mails` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;
INSERT INTO `Mails` (`ID`, `Text`, `Description`) VALUES
(1, 'Hallo %s,\n\ndu hast dich bei %s registriert. Um deinen Account zu aktivieren, klicke bitte folgenden Link an:\n\n%s\n\nDu hast diese Email erhalten, weil du dich bei %s registriert hast.', 'Register email in german language'),
(2, 'Hallo %s,\n\ndein Passwort %s wurde zurückgesetzt. Das Passwort wurde auf\n\n%s\n\ngesetzt. Du bekommst diese Mail, weil du bei %s registriert bist.\n', 'New Password Description de'),
(3, 'Hallo %s,\n\ndein Account wurde gesperrt, es wurde zu oft versucht sich mit deinen Logindaten anzumelden.\n\nDie IP lautete: %s.\n\n%s%s\n', 'Account_Locked');
CREATE TABLE IF NOT EXISTS `Pass_History` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Changed` text COLLATE utf8_bin,
  `IP` text COLLATE utf8_bin NOT NULL,
  `Who` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=6 ;
CREATE TABLE IF NOT EXISTS `Share` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Hash` text NOT NULL,
  `UserID` int(11) NOT NULL,
  `Extern_ID` text NOT NULL,
  `Used` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;
CREATE TABLE IF NOT EXISTS `Users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User` text,
  `Email` text,
  `Password` text,
  `Salt` text,
  `Safety_Question` text,
  `Safety_Answer` text,
  `Registered` text,
  `Role` text,
  `Storage` int(11) DEFAULT NULL,
  `Enabled` tinyint(1) NOT NULL,
  `API_Key` text NOT NULL,
  `Enable_API` int(11) DEFAULT NULL,
  `Failed_Logins` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
