-- add param to uni and galaxy type
ALTER TABLE `%PREFIX%config` ADD `expo_ress_met_chance` tinyint(3) unsigned NOT NULL DEFAULT '50';
ALTER TABLE `%PREFIX%config` ADD `expo_ress_crys_chance` tinyint(3) unsigned NOT NULL DEFAULT '33';
ALTER TABLE `%PREFIX%config` ADD `expo_ress_deut_chance` tinyint(3) unsigned NOT NULL DEFAULT '17';