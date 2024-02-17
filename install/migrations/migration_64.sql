-- Add debug ticket category and update tickets
UPDATE `%PREFIX%ticket` SET `categoryID` = 9, `ownerID` = 0 WHERE `subject` LIKE 'USER ERROR' OR `subject` LIKE 'WARNING';
INSERT INTO `%PREFIX%ticket_category` (`categoryID`, `name`) VALUES (9, 'Debug: Code errors and warnings');
ALTER TABLE `%PREFIX%config` 
    ADD COLUMN `discord_logs_hook` varchar(255) DEFAULT "" AFTER `ttf_file`,
    ADD COLUMN `discord_exceptions_hook` varchar(255) DEFAULT "" AFTER `discord_logs_hook`,
    ADD COLUMN `discord_tickets_hook` varchar(255) DEFAULT "" AFTER `discord_exceptions_hook`;