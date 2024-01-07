-- Add simple buteforce protection
DELETE FROM `%PREFIX%vars` WHERE `elementID` = 220 OR `elementID` = 221 OR `elementID` = 222;
DELETE FROM `%PREFIX%vars_rapidfire` WHERE `elementID` = 220 OR `rapidfireID` = 220 OR `elementID` = 221 OR `rapidfireID` = 221 OR `elementID` = 222 OR `rapidfireID` = 222;
DELETE FROM `%PREFIX%vars_requriements` WHERE `elementID` = 220 OR `elementID` = 221 OR `elementID` = 222;

INSERT INTO `%PREFIX%vars` (`elementID`, `name`, `class`, `onPlanetType`, `onePerPlanet`, `factor`, `maxLevel`, `cost901`, `cost902`, `cost903`, `cost911`, `consumption1`, `consumption2`, `speedTech`, `speed1`, `speed2`, `speed3`, `capacity`, `attack`, `defend`, `timeBonus`, `bonusAttack`, `bonusDefensive`, `bonusShield`, `bonusBuildTime`, `bonusResearchTime`, `bonusShipTime`, `bonusDefensiveTime`, `bonusResource`, `bonusEnergy`, `bonusResourceStorage`, `bonusShipStorage`, `bonusFlyTime`, `bonusFleetSlots`, `bonusPlanets`, `bonusSpyPower`, `bonusExpedition`, `bonusGateCoolTime`, `bonusMoreFound`, `bonusAttackUnit`, `bonusDefensiveUnit`, `bonusShieldUnit`, `bonusBuildTimeUnit`, `bonusResearchTimeUnit`, `bonusShipTimeUnit`, `bonusDefensiveTimeUnit`, `bonusResourceUnit`, `bonusEnergyUnit`, `bonusResourceStorageUnit`, `bonusShipStorageUnit`, `bonusFlyTimeUnit`, `bonusFleetSlotsUnit`, `bonusPlanetsUnit`, `bonusSpyPowerUnit`, `bonusExpeditionUnit`, `bonusGateCoolTimeUnit`, `bonusMoreFoundUnit`, `speedFleetFactor`, `production901`, `production902`, `production903`, `production911`, `storage901`, `storage902`, `storage903`) VALUES
(220, 'fighter_bomber', 200, '1,3', 0, 1.00, NULL, 10000, 3000, 500, 0, 50, 50, 1, 4000, 4000, NULL, 15, 150, 30, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 'carrier', 200, '1,3', 0, 1.00, NULL, 40000, 20000, 4000, 0, 800, 800, 3, 10000, 10000, NULL, 700, 150, 500, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

INSERT INTO `%PREFIX%vars_rapidfire` (`elementID`, `rapidfireID`, `shoots`) VALUES
(205, 220, 6),
(215, 220, 4),
(215, 221, 7),
(214, 220, 80),
(214, 221, 30),
(220, 210, 5),
(220, 212, 5),
(220, 213, 5),
(220, 214, 5),
(220, 215, 5),
(220, 404, 2),
(220, 406, 2),
(221, 210, 5),
(221, 212, 5),
(221, 204, 2);

INSERT INTO `%PREFIX%vars_requriements` (`elementID`, `requireID`, `requireLevel`) VALUES
(220, 21, 5),
(220, 108, 10),
(220, 109, 12),
(220, 120, 14),
(221, 21, 8),
(221, 110, 12),
(221, 114, 7);

ALTER TABLE `%PREFIX%planets` ADD COLUMN `fighter_bomber` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `giga_recykler`;
ALTER TABLE `%PREFIX%planets` ADD COLUMN `carrier` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `fighter_bomber`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `build_220` int(10) unsigned NOT NULL DEFAULT '0' AFTER `build_219`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `build_221` int(10) unsigned NOT NULL DEFAULT '0' AFTER `build_220`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `lost_220` int(10) unsigned NOT NULL DEFAULT '0' AFTER `lost_219`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `lost_221` int(10) unsigned NOT NULL DEFAULT '0' AFTER `lost_220`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `destroyed_220` int(10) unsigned NOT NULL DEFAULT '0' AFTER `destroyed_219`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `destroyed_221` int(10) unsigned NOT NULL DEFAULT '0' AFTER `destroyed_220`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `found_220` int(10) unsigned NOT NULL DEFAULT '0' AFTER `found_219`;
ALTER TABLE `%PREFIX%advanced_stats` ADD COLUMN `found_221` int(10) unsigned NOT NULL DEFAULT '0' AFTER `found_220`;
