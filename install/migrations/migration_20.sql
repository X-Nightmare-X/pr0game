-- Missing fields for config added
ALTER TABLE `%PREFIX%config` ADD `git_issues_link` varchar(128) NOT NULL DEFAULT '<git_issues_link>';