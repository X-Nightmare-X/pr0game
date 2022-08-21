-- Optimizing log_fleets
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_gained_metal` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `fleet_resource_deuterium`;
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_gained_crystal` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `fleet_gained_metal`;
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_gained_deuterium` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `fleet_gained_crystal`;

ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_start_time_formated` datetime AFTER `fleet_start_time`;
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_end_time_formated` datetime AFTER `fleet_end_time`;
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_end_stay_formated` datetime AFTER `fleet_end_stay`;
ALTER TABLE `%PREFIX%log_fleets` ADD `start_time_formated` datetime AFTER `start_time`;

ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_owner_name` varchar(32) NOT NULL DEFAULT '' AFTER `fleet_owner`;
ALTER TABLE `%PREFIX%log_fleets` ADD `fleet_target_owner_name` varchar(32) NOT NULL DEFAULT '' AFTER `fleet_target_owner`;