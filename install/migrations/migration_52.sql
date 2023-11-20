-- add update module varchar length

ALTER TABLE `%PREFIX%config` MODIFY COLUMN `moduls` varchar(200);
