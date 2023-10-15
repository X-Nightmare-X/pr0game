-- add market ratios
CREATE TABLE IF NOT EXISTS `%PREFIX%marketplace` (
  `universeId` int(11) NOT NULL AUTO_INCREMENT,
  `metCrysRatio` float(23) NOT NULL DEFAULT 2,
  `metDeutRatio` float(23) NOT NULL DEFAULT 2,
  `crysDeutRatio` float(23) NOT NULL DEFAULT 1,
  PRIMARY KEY (`universeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `%PREFIX%marketplace` (`universeId`, `metCrysRatio`, `metDeutRatio`, `crysDeutRatio`) VALUES
(1, 1.5, 3.1, 2),
(2, 3, 1.5, 0.5);