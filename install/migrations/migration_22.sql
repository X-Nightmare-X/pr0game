-- add param to enable pausing in umode
ALTER TABLE `%PREFIX%users` ADD `urlaubs_start` int(11) NOT NULL DEFAULT 0 AFTER `urlaubs_until`;