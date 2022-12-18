-- add change deut formula

UPDATE `%PREFIX%vars` SET `production903` = '(floor(10 * $BuildLevel * pow((1.1), $BuildLevel) * (1.44 -0.004 * $BuildTemp)) * (0.1 * $BuildLevelFactor))' WHERE `elementID` = 3;
