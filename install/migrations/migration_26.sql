-- add param to planet creation
ALTER TABLE `%PREFIX%config` ADD `planet_creation` tinyint(1) unsigned NOT NULL DEFAULT '0';