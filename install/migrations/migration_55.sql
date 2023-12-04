-- Add option to navigate directly to message category
ALTER TABLE `%PREFIX%users`  ADD `showMessageCategory` tinyint(1) NOT NULL DEFAULT '0' AFTER `banaday`;
