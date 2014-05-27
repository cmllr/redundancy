SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=24 ;

INSERT INTO `User` (`id`, `loginName`, `displayName`, `mailAddress`, `registrationDateTime`, `lastLoginDateTime`, `passwordHash`, `isEnabled`, `contingentInByte`, `roleID`, `failedLogins`) VALUES
(1, 'fury', 'CM', 'bla@bla.de', '2014-05-16 00:00:00', '2014-05-16 00:00:00', 'bla', 1, 100000, NULL, 0),
(9, 'fuxry', 'CM', 'bla@blxa.de', '2014-05-17 14:12:59', '0000-00-00 00:00:00', '$2y$11$KY/Tjuko2xX/4WqhhyYj6.FnzdN/9Ui7D2JUUujy/bPSVKheUoKJO', 1, 5242880, 1, 0);


ALTER TABLE `User`
  ADD CONSTRAINT `fk_Role` FOREIGN KEY (`roleID`) REFERENCES `Role` (`id`);