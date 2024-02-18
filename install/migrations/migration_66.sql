-- Adds user options to show all buildable elements and highlight unbuildable elements
ALTER TABLE `%PREFIX%users` 
    ADD COLUMN `show_all_buildable_elements` tinyint unsigned NOT NULL DEFAULT 1,
    ADD COLUMN `missing_requirements_opacity` tinyint unsigned NOT NULL DEFAULT 1,
    ADD COLUMN `missing_resources_opacity` tinyint unsigned NOT NULL DEFAULT 0;

ALTER TABLE `%PREFIX%users` ALTER `spyMessagesMode` SET DEFAULT 1;
