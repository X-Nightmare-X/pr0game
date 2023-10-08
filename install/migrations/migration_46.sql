-- add more stats
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_count`              bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_black_hole`         bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_pirates_small`      bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_pirates_medium`     bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_pirates_large`      bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_aliens_small`       bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_aliens_medium`      bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_aliens_large`       bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_res_small`          bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_res_medium`         bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_res_large`          bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_ships_small`        bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_ships_medium`       bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_ships_large`        bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_slow`               bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_fast`               bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `expo_nothing`            bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%advanced_stats`  ADD `moons_created`           bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `moons_destroyed`         bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `destroy_moon_rips_lost`  bigint(20) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `%PREFIX%advanced_stats`  ADD `set_sail_wins`           bigint(20) unsigned NOT NULL DEFAULT '0';

ALTER TABLE `%PREFIX%planets`         ADD `creation_time`           int(11) unsigned NOT NULL DEFAULT 0;