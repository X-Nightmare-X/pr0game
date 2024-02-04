-- buyer amount and type
ALTER TABLE `%PREFIX%trades` ADD COLUMN `resource_type` tinyint unsigned NOT NULL DEFAULT 0;
ALTER TABLE `%PREFIX%trades` ADD COLUMN `resource_amount` double(50,0) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%trades` ADD COLUMN `marketplace_galaxy` tinyint unsigned NOT NULL DEFAULT 0;
ALTER TABLE `%PREFIX%trades` ADD COLUMN `marketplace_system` smallint unsigned NOT NULL DEFAULT 0;