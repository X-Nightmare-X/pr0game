-- add param to uni and galaxy type
ALTER TABLE `%PREFIX%config` ADD `uni_type` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%config` ADD `galaxy_type` tinyint(1) unsigned NOT NULL DEFAULT '0';