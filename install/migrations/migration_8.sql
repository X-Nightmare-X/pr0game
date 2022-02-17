-- Drop all chat tables
DROP TABLE IF EXISTS `%PREFIX%chat_bans`;
DROP TABLE IF EXISTS `%PREFIX%chat_invitations`;
DROP TABLE IF EXISTS `%PREFIX%chat_messages`;
DROP TABLE IF EXISTS `%PREFIX%chat_online`;

-- Remove log entries of target `chat`
DELETE FROM `%PREFIX%log` WHERE `target` = 3;
