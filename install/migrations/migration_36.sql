-- change Config game_disable to uni_status (combined login and reg status) and drop reg_closed

ALTER TABLE `%PREFIX%config` RENAME COLUMN `game_disable` TO `uni_status`;
UPDATE `%PREFIX%config` SET `reg_closed` = 0; -- discard reg status
UPDATE `%PREFIX%config` SET `reg_closed` = 1 WHERE `uni_status` = 0; -- save closed status
UPDATE `%PREFIX%config` SET `uni_status` = 0 WHERE `uni_status` = 1; -- update open status to new value
UPDATE `%PREFIX%config` SET `uni_status` = 1 WHERE `reg_closed` = 1; -- load closed status to new value
ALTER TABLE `%PREFIX%config` DROP COLUMN `reg_closed`; -- drop old reg status