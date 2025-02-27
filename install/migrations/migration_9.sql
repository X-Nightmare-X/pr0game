-- Remove TeamSpeak cronjobs
DELETE FROM `%PREFIX%cronjobs` WHERE `class` = 'TeamSpeakCronjob';

-- Drop TeamSpeak configuration columns
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

-- Drop Facebook configuration columns
ALTER TABLE `%PREFIX%config` DROP COLUMN `fb_on`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `fb_apikey`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `fb_skey`;

-- Drop External Auth columns and table
ALTER TABLE `%PREFIX%users_valid` DROP COLUMN `externalAuthUID`;
ALTER TABLE `%PREFIX%users_valid` DROP COLUMN `externalAuthMethod`;
DROP TABLE IF EXISTS `%PREFIX%users_to_extauth`;

-- Drop reCAPTCHA columns
ALTER TABLE `%PREFIX%config` DROP COLUMN `capaktiv`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `cappublic`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `capprivate`;
