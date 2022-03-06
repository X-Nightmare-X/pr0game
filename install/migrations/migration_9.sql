-- Remove TeamSpeak cronjobs
DELETE FROM `%PREFIX%cronjobs` WHERE `class` = 'TeamSpeakCronjob';

-- Drop TeamSpeak configuration tables
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_modon`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_server`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_tcpport`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_udpport`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_timeout`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_version`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_cron_last`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_cron_interval`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_login`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `ts_password`;
