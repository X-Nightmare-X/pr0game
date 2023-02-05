-- update fusion plant energy production to use $BuildEnergy instead of $PLANET field
UPDATE `%PREFIX%vars` SET `production911` = '30 * $BuildLevel * pow(1.05 + ($BuildEnergy * 0.01), $BuildLevel) * (0.1 * $BuildLevelFactor)' WHERE `elementID` = 12;
