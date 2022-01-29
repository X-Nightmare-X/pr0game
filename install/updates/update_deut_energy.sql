-- Updates energy consumption of deuterium synthesizers from - (30 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor)

UPDATE `uni1_vars` SET `production911` = '- (20 * $BuildLevel * pow((1.1), $BuildLevel)) * (0.1 * $BuildLevelFactor)' WHERE `uni1_vars`.`elementID` = 3; 