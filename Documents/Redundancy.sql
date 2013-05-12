SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


CREATE TABLE IF NOT EXISTS `Access` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Site` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `Files` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Filename` text,
  `Displayname` text,
  `Filename_only` text,
  `Hash` text,
  `UserID` int(11) NOT NULL,
  `IP` text NOT NULL,
  `Uploaded` text NOT NULL,
  `Size` int(11) NOT NULL,
  `Directory` text NOT NULL,
  `Client` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=275 ;



CREATE TABLE IF NOT EXISTS `Mails` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Text` text NOT NULL,
  `Description` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

INSERT INTO `Mails` (`ID`, `Text`, `Description`) VALUES
(1, 'Hallo %s,\n\ndu hast dich bei %s registriert. Um deinen Account zu aktivieren, klicke bitte folgenden Link an:\n\n%s\n\nDu hast diese Email erhalten, weil du dich bei %s registriert hast.', 'Register email in german language'),
(2, 'Hallo %s,\n\ndein Passwort wurde zurückgesetzt. Um das Passwort zu setzen, klicke bitte auf folgenden Link:\n\n%s\n\nDu bekommst diese Email, weil du bei %s registriert bist.', 'New Password Description de');

CREATE TABLE IF NOT EXISTS `Share` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Hash` text NOT NULL,
  `UserID` int(11) NOT NULL,
  `Extern_ID` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

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
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
