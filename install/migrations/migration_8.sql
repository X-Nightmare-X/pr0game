-- Drop all chat tables
DROP TABLE IF EXISTS `%PREFIX%chat_bans`;
DROP TABLE IF EXISTS `%PREFIX%chat_invitations`;
DROP TABLE IF EXISTS `%PREFIX%chat_messages`;
DROP TABLE IF EXISTS `%PREFIX%chat_online`;

ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_closed`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_allowchan`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_allowmes`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_allowdelmes`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_logmessage`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_nickchange`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_botname`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_channelname`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_socket_active`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_socket_host`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_socket_ip`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_socket_port`;
ALTER TABLE `%PREFIX%config` DROP COLUMN `chat_socket_chatid`;

-- Remove log entries of target `chat`
DELETE FROM `%PREFIX%log` WHERE `target` = 3;
