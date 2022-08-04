-- Missing fields for log_fleets added
ALTER TABLE `%PREFIX%log_fleets` ADD `hasCanceled` tinyint(1) unsigned NOT NULL DEFAULT 0;