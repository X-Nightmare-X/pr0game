-- add change deut formula

UPDATE `%PREFIX%vars` SET `production903` = '(10 * $BuildLevel * pow((1.1), $BuildLevel) * (-0.002 * $BuildTemp + 1.28) * (0.1 * $BuildLevelFactor))' WHERE `elementID` = 3;