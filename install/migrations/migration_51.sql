-- add cascading moon chances that increase after each max size try

ALTER TABLE `%PREFIX%config`  ADD `cascading_moon_chance` float(3,1) unsigned NOT NULL DEFAULT '0.0' AFTER `moonSizeFactor`;
ALTER TABLE `%PREFIX%advanced_stats`  ADD `moons_received` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `moons_destroyed`;
ALTER TABLE `%PREFIX%advanced_stats`  ADD `first_moon_trys` bigint(20) unsigned NOT NULL DEFAULT '0' AFTER `moons_received`;