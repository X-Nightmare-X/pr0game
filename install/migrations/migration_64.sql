-- Add debug ticket category and update tickets
UPDATE `%PREFIX%ticket` SET `categoryID` = 9, `ownerID` = 0 WHERE `subject` LIKE 'USER ERROR' OR `subject` LIKE 'WARNING';
INSERT INTO `%PREFIX%ticket_category` (`categoryID`, `name`) VALUES (9, 'Debug: Code errors and warnings');