-- Adds user options to show all buildable elements and highlight unbuildable elements
ALTER TABLE `%PREFIX%users` ADD COLUMN `achievement_title_id` tinyint unsigned NOT NULL DEFAULT 0;

ALTER TABLE `%PREFIX%achievements` ADD COLUMN `title` varchar(255);

UPDATE `%PREFIX%achievements` SET `title` = 'Ludwig XVI' WHERE `id` = 3;
UPDATE `%PREFIX%achievements` SET `title` = 'Grand Moff Tarkin' WHERE `id` = 5;
UPDATE `%PREFIX%achievements` SET `title` = 'Maximilian Robespierre' WHERE `id` = 7;
UPDATE `%PREFIX%achievements` SET `title` = 'Quak' WHERE `id` = 12;
UPDATE `%PREFIX%achievements` SET `title` = 'Achievement 26' WHERE `id` = 26;
UPDATE `%PREFIX%achievements` SET `title` = 'Blackbeard' WHERE `id` = 32;
UPDATE `%PREFIX%achievements` SET `title` = 'Sun Tzu' WHERE `id` = 43;
UPDATE `%PREFIX%achievements` SET `title` = 'Julius Caesar' WHERE `id` = 52;