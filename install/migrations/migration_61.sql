-- Add simple buteforce protection
DELETE FROM `%PREFIX%vars` WHERE `elementID` = 221 OR `elementID` = 222;
DELETE FROM `%PREFIX%vars_rapidfire` WHERE `elementID` = 221 OR `rapidfireID` = 221 OR `elementID` = 222 OR `rapidfireID` = 222;
DELETE FROM `%PREFIX%vars_requriements` WHERE `elementID` = 221 OR `elementID` = 222;

INSERT INTO `%PREFIX%vars` (`elementID`, `name`, `class`, `onPlanetType`, `onePerPlanet`, `factor`, `maxLevel`, `cost901`, `cost902`, `cost903`, `cost911`, `consumption1`, `consumption2`, `speedTech`, `speed1`, `speed2`, `speed3`, `capacity`, `attack`, `defend`, `timeBonus`, `bonusAttack`, `bonusDefensive`, `bonusShield`, `bonusBuildTime`, `bonusResearchTime`, `bonusShipTime`, `bonusDefensiveTime`, `bonusResource`, `bonusEnergy`, `bonusResourceStorage`, `bonusShipStorage`, `bonusFlyTime`, `bonusFleetSlots`, `bonusPlanets`, `bonusSpyPower`, `bonusExpedition`, `bonusGateCoolTime`, `bonusMoreFound`, `bonusAttackUnit`, `bonusDefensiveUnit`, `bonusShieldUnit`, `bonusBuildTimeUnit`, `bonusResearchTimeUnit`, `bonusShipTimeUnit`, `bonusDefensiveTimeUnit`, `bonusResourceUnit`, `bonusEnergyUnit`, `bonusResourceStorageUnit`, `bonusShipStorageUnit`, `bonusFlyTimeUnit`, `bonusFleetSlotsUnit`, `bonusPlanetsUnit`, `bonusSpyPowerUnit`, `bonusExpeditionUnit`, `bonusGateCoolTimeUnit`, `bonusMoreFoundUnit`, `speedFleetFactor`, `production901`, `production902`, `production903`, `production911`, `storage901`, `storage902`, `storage903`) VALUES
(221, 'fighter_bomber', 201, '1,3', 0, 1.00, NULL, 10000, 3000, 500, 0, 50, 50, 1, 4000, 4000, NULL, 15, 150, 30, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(222, 'carrier', 201, '1,3', 0, 1.00, NULL, 40000, 20000, 4000, 0, 800, 800, 3, 10000, 10000, NULL, 700, 150, 500, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `%PREFIX%vars_rapidfire` (`elementID`, `rapidfireID`, `shoots`) VALUES
(205, 221, 6),
(215, 221, 4),
(215, 222, 7),
(214, 221, 80),
(214, 222, 30),
(221, 202, 3),
(221, 203, 5),
(221, 206, 3),
(221, 207, 3),
(221, 208, 5),
(221, 209, 3),
(221, 210, 5),
(221, 211, 5),
(221, 212, 5),
(221, 213, 5),
(221, 214, 5),
(221, 215, 5),
(221, 402, 8),
(221, 403, 4),
(221, 404, 2),
(221, 405, 3),
(221, 406, 2),
(222, 210, 5),
(222, 212, 5),
(222, 204, 3);

INSERT INTO `%PREFIX%vars_requriements` (`elementID`, `requireID`, `requireLevel`) VALUES
(221, 21, 5),
(221, 108, 6),
(221, 109, 6),
(221, 120, 6),
(222, 21, 8),
(222, 110, 12),
(222, 114, 7);

ALTER TABLE `%PREFIX%planets` ADD COLUMN `fighter_bomber` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `giga_recykler`;
ALTER TABLE `%PREFIX%planets` ADD COLUMN `carrier` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `fighter_bomber`;