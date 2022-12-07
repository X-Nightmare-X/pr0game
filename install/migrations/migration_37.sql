-- add player points to log_fleets

ALTER TABLE `%PREFIX%log_fleets` ADD COLUMN `fleet_owner_points` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `fleet_owner_name`;
ALTER TABLE `%PREFIX%log_fleets` ADD COLUMN `fleet_target_owner_points` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `fleet_target_owner_name`;