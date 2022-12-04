-- add config for recaptcha

ALTER TABLE `%PREFIX%config` RENAME COLUMN `game_speed` TO `building_speed`;
ALTER TABLE `%PREFIX%config` ADD `shipyard_speed` bigint(20) unsigned NOT NULL DEFAULT '2500' AFTER `building_speed`;
ALTER TABLE `%PREFIX%config` ADD `research_speed` bigint(20) unsigned NOT NULL DEFAULT '2500' AFTER `shipyard_speed`;