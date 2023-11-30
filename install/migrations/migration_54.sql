-- added all planet pictures everywhere

ALTER TABLE `%PREFIX%config`  ADD `all_planet_pictures` tinyint(3) unsigned NOT NULL DEFAULT '0' AFTER `planet_factor`;
