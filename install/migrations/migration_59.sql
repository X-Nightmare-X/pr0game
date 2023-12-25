-- Add blocklist
CREATE TABLE `%PREFIX%users_blocklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `blocking_user` int(11) unsigned NOT NULL,
  `blocked_user` int(11) unsigned NOT NULL,
  `block_dm` int(1) unsigned NOT NULL DEFAULT '0',
  `block_trade` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE INDEX idx_userids ON `%PREFIX%users_blocklist` (`blocking_user`, `blocked_user`);