CREATE TABLE IF NOT EXISTS `LocalShare` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `FileID` int(11) NOT NULL,
  `TargetUser` int(11) NOT NULL,
  `Mode` int(11) NOT NULL COMMENT 'See CHMOD',
  PRIMARY KEY (`ID`),
  KEY `FileID` (`FileID`),
  KEY `TargetUser` (`TargetUser`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ;
ALTER TABLE `LocalShare`
  ADD CONSTRAINT `LocalShare_ibfk_1` FOREIGN KEY (`FileID`) REFERENCES `Files` (`ID`),
  ADD CONSTRAINT `LocalShare_ibfk_2` FOREIGN KEY (`TargetUser`) REFERENCES `Users` (`ID`);
