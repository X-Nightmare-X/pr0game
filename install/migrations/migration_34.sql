-- change maximiser requirements

UPDATE `%PREFIX%vars_requriements` SET `requirelevel` = 4 where `elementID` in (131,132,133) and `requireID` = 31;
UPDATE `%PREFIX%vars_requriements` SET `requirelevel` = 3 where `elementID` in (131,132,133) and `requireID` = 113;