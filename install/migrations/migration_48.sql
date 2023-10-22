-- multi detection overhawl

DROP TABLE `%PREFIX%multi`;

CREATE TABLE `%PREFIX%multi` (
  `multiID` int(11) NOT NULL AUTO_INCREMENT,
  `userID_1` int(11) NOT NULL,
  `userID_2` int(11) NOT NULL,
  `multi_ip` varchar(40) NOT NULL DEFAULT '',
  `lastActivity` int(11) NOT NULL DEFAULT '0',
  `allowed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`multiID`),
  KEY `userID_1` (`userID_1`),
  KEY `userID_2` (`userID_2`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
