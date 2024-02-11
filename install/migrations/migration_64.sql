-- Add phalanx_log
CREATE TABLE `%PREFIX%phalanx_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(11) unsigned NOT NULL,
  `owner_planet_id` int(11) unsigned NOT NULL,
  `target` int(11) unsigned NOT NULL,
  `target_planet_id` int(11) unsigned NOT NULL,
  `phalanx_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `%PREFIX%phalanx_fleets` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `phalanx_log_id` int(11) unsigned NOT NULL,
  `fleet_id` bigint(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;