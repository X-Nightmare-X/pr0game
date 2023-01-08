-- add optin for records
ALTER TABLE `%PREFIX%users` ADD `records_optIn` tinyint(1) NOT NULL DEFAULT 0;
