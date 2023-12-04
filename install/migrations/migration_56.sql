-- Adds discord id and webhook option for admins

ALTER TABLE `%PREFIX%users` ADD `discord_id` varchar(25) NOT NULL DEFAULT '' AFTER `rights`;
ALTER TABLE `%PREFIX%users` ADD `discord_hook` text AFTER `discord_id`;