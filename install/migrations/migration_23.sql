-- add param to enable pausing in umode
ALTER TABLE `%PREFIX%planets` ADD `tf_active` tinyint(1) NOT NULL DEFAULT '0' AFTER `der_crystal`;