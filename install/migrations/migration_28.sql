-- add param to planet creation
ALTER TABLE `%PREFIX%config` ADD `initial_temp` int(3) unsigned NOT NULL DEFAULT '50';