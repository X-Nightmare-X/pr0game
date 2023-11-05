-- multi detection overhawl

DROP TABLE `%PREFIX%multi`;

CREATE TABLE `%PREFIX%multi` (
  `multiID` int(11) NOT NULL AUTO_INCREMENT,
  `multi_ip` varchar(40) NOT NULL DEFAULT '',
  `universe` tinyint(3) unsigned NOT NULL,
  `lastActivity` int(11) NOT NULL DEFAULT '0',
  `allowed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`multiID`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `%PREFIX%multi_to_users` (
  `multiID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `lastActivity` int(11) NOT NULL DEFAULT '0',
  `allowed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`multiID`, `userID`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
