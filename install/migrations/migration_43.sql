-- update recycler speed tech for newer servers. It had not been changed in install.sql since migration_13
UPDATE `%PREFIX%vars` SET speedTech = 6 WHERE elementID = 209;
