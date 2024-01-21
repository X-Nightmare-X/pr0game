-- Add simple buteforce protection
ALTER TABLE `%PREFIX%users` ADD `failed_logins` tinyint(1) NOT NULL DEFAULT '0' AFTER `email_2`;
ALTER TABLE `%PREFIX%users_valid` ADD `failed_logins` tinyint(1) NOT NULL DEFAULT '0';