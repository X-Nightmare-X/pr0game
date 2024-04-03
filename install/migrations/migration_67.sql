-- Adds user options to show amount of messages
ALTER TABLE `%PREFIX%users` ADD COLUMN `messages_per_page` tinyint(2) NOT NULL DEFAULT 10;
