-- add galaxy and memorial columns to topkb table

ALTER TABLE `%PREFIX%topkb` ADD `galaxy` TINYINT(3) unsigned NULL DEFAULT NULL AFTER `universe`, ADD `memorial` TINYINT(1) unsigned NOT NULL DEFAULT '0' AFTER `galaxy`;