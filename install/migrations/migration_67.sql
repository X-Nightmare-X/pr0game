-- Adds universe defined vars
ALTER TABLE `%PREFIX%vars` 
    ADD COLUMN `universe` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `elementID`,
    DROP PRIMARY KEY,
    ADD PRIMARY KEY (`elementID`, `universe`);
ALTER TABLE `%PREFIX%vars_rapidfire` ADD COLUMN `universe` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `elementID`;
ALTER TABLE `%PREFIX%vars_requriements` ADD COLUMN `universe` tinyint(3) unsigned NOT NULL DEFAULT 0 AFTER `elementID`;
INSERT INTO `%PREFIX%vars` (`elementID`, `universe`, `name`, `class`, `onPlanetType`, `onePerPlanet`, `factor`, `maxLevel`, `cost901`, `cost902`, `cost903`, `cost911`, `consumption1`, `consumption2`, `speedTech`, `speed1`, `speed2`, `speed3`, `capacity`, `attack`, `defend`, `timeBonus`, `bonusAttack`, `bonusDefensive`, `bonusShield`, `bonusBuildTime`, `bonusResearchTime`, `bonusShipTime`, `bonusDefensiveTime`, `bonusResource`, `bonusEnergy`, `bonusResourceStorage`, `bonusShipStorage`, `bonusFlyTime`, `bonusFleetSlots`, `bonusPlanets`, `bonusSpyPower`, `bonusExpedition`, `bonusGateCoolTime`, `bonusMoreFound`, `bonusAttackUnit`, `bonusDefensiveUnit`, `bonusShieldUnit`, `bonusBuildTimeUnit`, `bonusResearchTimeUnit`, `bonusShipTimeUnit`, `bonusDefensiveTimeUnit`, `bonusResourceUnit`, `bonusEnergyUnit`, `bonusResourceStorageUnit`, `bonusShipStorageUnit`, `bonusFlyTimeUnit`, `bonusFleetSlotsUnit`, `bonusPlanetsUnit`, `bonusSpyPowerUnit`, `bonusExpeditionUnit`, `bonusGateCoolTimeUnit`, `bonusMoreFoundUnit`, `speedFleetFactor`, `production901`, `production902`, `production903`, `production911`, `storage901`, `storage902`, `storage903`) VALUES
(205, 4, 'heavy_hunter', 200, '1,3', 0, 1.00, NULL, 6500, 3500, 0, 0, 40, 40, 2, 10000, 10000, NULL, 100, 150, 25, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `%PREFIX%vars_rapidfire` (`elementID`, `universe`, `rapidfireID`, `shoots`) VALUES
(207, 4, 205, 4),
(215, 4, 205, 1);