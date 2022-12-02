-- add config for fleetprio
ALTER TABLE `%PREFIX%users` ADD `prioMission1` tinyint(2) NOT NULL DEFAULT 1;
ALTER TABLE `%PREFIX%users` ADD `prioMission2` tinyint(2) NOT NULL DEFAULT 2;
ALTER TABLE `%PREFIX%users` ADD `prioMission3` tinyint(2) NOT NULL DEFAULT 0;
ALTER TABLE `%PREFIX%users` ADD `prioMission4` tinyint(2) NOT NULL DEFAULT 3;
ALTER TABLE `%PREFIX%users` ADD `prioMission5` tinyint(2) NOT NULL DEFAULT 4;
ALTER TABLE `%PREFIX%users` ADD `prioMission6` tinyint(2) NOT NULL DEFAULT 5;
ALTER TABLE `%PREFIX%users` ADD `prioMission7` tinyint(2) NOT NULL DEFAULT 6;
ALTER TABLE `%PREFIX%users` ADD `prioMission8` tinyint(2) NOT NULL DEFAULT 7;
ALTER TABLE `%PREFIX%users` ADD `prioMission9` tinyint(2) NOT NULL DEFAULT 8;
ALTER TABLE `%PREFIX%users` ADD `prioMission17` tinyint(2) NOT NULL DEFAULT 9;