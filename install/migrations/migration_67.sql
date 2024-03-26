-- Adds bonus production as percent value to the planet
ALTER TABLE `%PREFIX%planets` 
    ADD COLUMN `metal_bonus_percent` tinyint unsigned NOT NULL DEFAULT 0 AFTER `metal_max`,
    ADD COLUMN `crystal_bonus_percent` tinyint unsigned NOT NULL DEFAULT 0 AFTER `crystal_max`,
    ADD COLUMN `deuterium_bonus_percent` tinyint unsigned NOT NULL DEFAULT 0 AFTER `deuterium_max`;

ALTER TABLE `%PREFIX%config_universe`
    ADD COLUMN `planet_ressource_bonus` tinyint(1) unsigned NOT NULL DEFAULT 0 AFTER `energy_basic_income`;