-- Updates speed of deathstar to its original 100
UPDATE `%PREFIX%vars` SET speed1 = 100, speed2 = 100 WHERE elementID = 214;

-- Add column speed3 to vars for recycler hyperspace drive.
ALTER TABLE `%PREFIX%vars` ADD speed3 int(11) unsigned DEFAULT NULL AFTER speed2;
UPDATE `%PREFIX%vars` SET speedTech = 6, speed3 = 6000 WHERE elementID = 209;
