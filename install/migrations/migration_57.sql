-- add current market ratio and courrent market value of the ressources 
ALTER TABLE `%PREFIX%log_fleets` ADD COLUMN `current_market_rate` text AFTER `fleet_wanted_resource_amount`;
ALTER TABLE `%PREFIX%log_fleets` ADD COLUMN `current_market_value` double(50,0) unsigned NOT NULL DEFAULT '0' AFTER `current_market_rate`;