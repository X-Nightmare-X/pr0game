-- buyer amount and type
ALTER TABLE `%PREFIX%trades` ADD COLUMN `resource_type` tinyint;
ALTER TABLE `%PREFIX%trades` ADD COLUMN `resource_amount` double(50,0);
ALTER TABLE `%PREFIX%trades` ADD COLUMN `marketplace_galaxy` tinyint;
ALTER TABLE `%PREFIX%trades` ADD COLUMN `marketplace_system` smallint;