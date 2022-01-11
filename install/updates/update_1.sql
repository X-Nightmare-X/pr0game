/*
Nur ausführen, wenn in Tabelle vars bei elementID 110 der name = 'defence_tech' ist.
*/

UPDATE `uni1_vars` SET `name` = 'shield_tech' WHERE `uni1_vars`.`elementID` = 110; 
UPDATE `uni1_vars` SET `name` = 'defence_tech' WHERE `uni1_vars`.`elementID` = 111; 

UPDATE `uni1_users` SET defence_tech = defence_tech + shield_tech, shield_tech = defence_tech - shield_tech, defence_tech = defence_tech - shield_tech; 