-- add config for ScavengersToolBox
ALTER TABLE `%PREFIX%users` ADD `stb_small_ress` int NOT NULL DEFAULT 500;
ALTER TABLE `%PREFIX%users` ADD `stb_med_ress` int NOT NULL DEFAULT 3000;
ALTER TABLE `%PREFIX%users` ADD `stb_big_ress` int NOT NULL DEFAULT 7000;
ALTER TABLE `%PREFIX%users` ADD `stb_small_time` tinyint(2) NOT NULL DEFAULT 1;
ALTER TABLE `%PREFIX%users` ADD `stb_med_time` tinyint(2) NOT NULL DEFAULT 2;
ALTER TABLE `%PREFIX%users` ADD `stb_big_time` tinyint(2) NOT NULL DEFAULT 3;
ALTER TABLE `%PREFIX%users` ADD `stb_enabled` tinyint(1) NOT NULL DEFAULT 0;
