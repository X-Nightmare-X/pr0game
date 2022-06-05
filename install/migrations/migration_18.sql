-- Missing fields for config added
ALTER TABLE `%PREFIX%config` ADD max_participants_per_acs tinyint(3) unsigned NOT NULL DEFAULT 5;