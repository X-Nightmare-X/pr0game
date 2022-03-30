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
