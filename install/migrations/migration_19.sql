-- Remove remaining officers
DELETE FROM `%PREFIX%vars` WHERE `elementID` >= 600;
-- Remove officer requirements
DELETE FROM `%PREFIX%vars_requriements` WHERE `requireID` >= 600;

-- Add missing field to log_fleets - faulty migration_17 corrected
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_start_array` text;

-- Entries from update_*.sql
-- Switches shield and defence tech column names to the correct order
/*
Only execute if in table vars name is 'defence_tech' for elementID 110.
*/
-- UPDATE `uni1_users` SET defence_tech = defence_tech + shield_tech, shield_tech = defence_tech - shield_tech, defence_tech = defence_tech - shield_tech;
UPDATE `%PREFIX%vars` SET `name` = 'shield_tech' WHERE `elementID` = 110; 
UPDATE `%PREFIX%vars` SET `name` = 'defence_tech' WHERE `elementID` = 111; 

-- Updates rapidfire of battlecuser vs battleship from 10 to original 7
UPDATE `%PREFIX%vars_rapidfire` SET shoots = '7' WHERE elementID = '215' AND rapidfireID = '207';
-- Updates deuterium consumption of battleships to its original 500
UPDATE `%PREFIX%vars` SET `consumption1` = '500', `consumption2` = '500' WHERE `elementID` = 207; 
-- Updates fuel consumtion of bombers from 1000 to 700
UPDATE `%PREFIX%vars` SET `consumption2` = '700', `consumption2` = '700' WHERE `elementID` = 211;
-- Updates energy consumption of deuterium synthesizers from - (30 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor)
UPDATE `%PREFIX%vars` SET `production911` = '- (20 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor)' WHERE `elementID` = 3; 