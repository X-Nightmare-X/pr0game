-- Remove officer maximum planet increase configuration
ALTER TABLE `%PREFIX%config` DROP COLUMN `planets_officier`;

-- Remove officers from user table
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_geologue`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_amiral`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_ingenieur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_technocrate`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_espion`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_constructeur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_scientifique`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_commandant`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_stockeur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_defenseur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_destructeur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_general`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_bunker`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_raideur`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `rpg_empereur`;

-- Remove officer vars
DELETE FROM `%PREFIX%vars_requriements` WHERE `elementID` IN (603,604,605,607,608,609,610,611,612,613,614,615);
DELETE FROM `%PREFIX%vars` WHERE `elementID` IN (601,602,603,604,605,607,608,609,610,611,612,613,614,615);

-- Remove Dark Matter
ALTER TABLE `%PREFIX%config` DROP COLUMN `darkmatter_cost_trader`;
ALTER TABLE `%PREFIX%fleets` DROP COLUMN `fleet_resource_darkmatter`;
ALTER TABLE `%PREFIX%log_fleets` DROP COLUMN `fleet_resource_darkmatter`;
ALTER TABLE `%PREFIX%vars` DROP COLUMN `cost921`;
ALTER TABLE `%PREFIX%vars` DROP COLUMN `production921`;

DELETE FROM `%PREFIX%vars_requriements` WHERE `elementID` IN (220);
DELETE FROM `%PREFIX%vars` WHERE `elementID` IN (220, 701, 702, 703, 704, 705, 706, 707);
DELETE FROM `%PREFIX%vars_rapidfire` WHERE `elementID` IN (220);
ALTER TABLE `%PREFIX%planets` DROP COLUMN `dm_ship`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `darkmatter_start`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `max_dm_missions`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_attack`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_defensive`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_buildtime`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_researchtime`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_resource`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_energie`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `dm_fleettime`;
ALTER TABLE `%PREFIX%users` DROP COLUMN `darkmatter`;

