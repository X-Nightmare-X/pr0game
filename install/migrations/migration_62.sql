-- add table for universe modules
CREATE TABLE `%PREFIX%config` (
  `VERSION` varchar(8) NOT NULL,
  `users_amount` int(11) unsigned NOT NULL DEFAULT '1', -- Update in statsbuilder + playerUtil(create). Used in stats banner, overview, statistics
  `forum_url` varchar(128) NOT NULL DEFAULT '', -- board page redirect, input field in universe
  `recaptchaPrivKey` varchar(255) DEFAULT '',
  `recaptchaPubKey` varchar(255) DEFAULT '',

  `game_name` varchar(30) NOT NULL,
  `ttf_file` varchar(128) NOT NULL DEFAULT 'styles/resource/fonts/DroidSansMono.ttf',
  `timezone` varchar(32) NOT NULL DEFAULT 'Europe/London',
  `git_issues_link` varchar(128) NOT NULL DEFAULT '<git_issues_link>',

  `del_oldstuff` tinyint(3) unsigned NOT NULL DEFAULT '30',
  `del_user_manually` tinyint(3) unsigned NOT NULL DEFAULT '7',
  `sendmail_inactive` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `del_user_sendmail` tinyint(3) unsigned NOT NULL DEFAULT '21',
  
  `mail_active` tinyint(1) NOT NULL DEFAULT '0',
  `mail_use` tinyint(1) NOT NULL DEFAULT '0',
  `smtp_sendmail` varchar(64) NOT NULL DEFAULT '',
  `smail_path` varchar(30) NOT NULL DEFAULT '/usr/sbin/sendmail',
  `smtp_host` varchar(64) NOT NULL DEFAULT '',
  `smtp_ssl` enum('','ssl','tls') NOT NULL DEFAULT '',
  `smtp_port` smallint(5) NOT NULL DEFAULT '0',
  `smtp_user` varchar(64) NOT NULL DEFAULT '',
  `smtp_pass` varchar(32) NOT NULL DEFAULT '',
  
  `message_delete_behavior` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `message_delete_days` tinyint(3) unsigned NOT NULL DEFAULT '7',
  
  `ga_active` varchar(42) NOT NULL DEFAULT '0',
  `ga_key` varchar(42) NOT NULL DEFAULT '',

  `disclamerAddress` text NOT NULL,
  `disclamerPhone` text NOT NULL,
  `disclamerMail` text NOT NULL,
  `disclamerNotice` text NOT NULL,
  
  `stat_settings` int(11) unsigned NOT NULL DEFAULT '1000',
  `stat` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stat_level` tinyint(3) unsigned NOT NULL DEFAULT '2',

  `sql_revision` INT NOT NULL DEFAULT  '0', -- unused, alternative for uni1_system
  `stat_last_update` int(11) NOT NULL DEFAULT '0', -- unused
  `stat_update_time` tinyint(3) unsigned NOT NULL DEFAULT '25', -- unused
  `stat_last_db_update` int(11) NOT NULL DEFAULT '0', -- unused
  `stats_fly_lock` int(11) NOT NULL DEFAULT '0', -- unused
  `cron_lock` int(11) NOT NULL DEFAULT '0', -- unused
  `moduls` varchar(200) NOT NULL DEFAULT '',
  `dst` enum('0','1','2') NOT NULL DEFAULT '2', -- summertime(LNG'op_dst_mode'): 0=no, 1=yes, 2=automatic, no input field, unused
  PRIMARY KEY (`uni`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `%PREFIX%config_universe` (
  `uni` int(11) NOT NULL AUTO_INCREMENT,
  `LastSettedGalaxyPos` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `LastSettedSystemPos` smallint(5) unsigned NOT NULL DEFAULT '1',
  `LastSettedPlanetPos` tinyint(3) unsigned NOT NULL DEFAULT '1',

  `uni_name` varchar(30) NOT NULL,
  `lang` varchar(2) NOT NULL DEFAULT '',
  `building_speed` bigint(20) unsigned NOT NULL DEFAULT '2500',
  `shipyard_speed` bigint(20) unsigned NOT NULL DEFAULT '2500',
  `research_speed` bigint(20) unsigned NOT NULL DEFAULT '2500',
  `fleet_speed` bigint(20) unsigned NOT NULL DEFAULT '2500',
  `resource_multiplier` smallint(5) unsigned NOT NULL DEFAULT '1',
  `storage_multiplier` smallint(5) unsigned NOT NULL DEFAULT '1',
  `halt_speed` smallint(5) unsigned NOT NULL DEFAULT '1', -- Expedition factor
  `energySpeed` smallint(6) NOT NULL DEFAULT '1',
  `del_user_automatic` tinyint(3) unsigned NOT NULL DEFAULT '90',
  `uni_status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `close_reason` text NOT NULL,

  `max_elements_build` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `max_elements_tech` tinyint(3) unsigned NOT NULL DEFAULT '2',
  `max_elements_ships` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `max_fleet_per_build` bigint(20) unsigned NOT NULL DEFAULT '1000000',

  `ref_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ref_bonus` int(11) unsigned NOT NULL DEFAULT '1000',
  `ref_minpoints` bigint(20) unsigned NOT NULL DEFAULT '2000',
  `ref_max_referals` tinyint(1) unsigned NOT NULL DEFAULT '5',

  `deuterium_cost_galaxy` int(11) unsigned NOT NULL DEFAULT '10',
  `max_galaxy` tinyint(3) unsigned NOT NULL DEFAULT '9',
  `max_system` smallint(5) unsigned NOT NULL DEFAULT '400',
  `max_planets` tinyint(3) unsigned NOT NULL DEFAULT '15',
  `uni_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `galaxy_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `planet_creation` tinyint(1) unsigned NOT NULL DEFAULT '0',

  `min_player_planets` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `planets_tech` TINYINT NOT NULL DEFAULT  '11',
  `planets_per_tech` FLOAT( 2, 1 ) NOT NULL DEFAULT  '0.5',
  `planet_factor` float(2,1) NOT NULL DEFAULT '1.0',
  `all_planet_pictures` tinyint(3) unsigned NOT NULL DEFAULT '0',

  `metal_start` int(11) unsigned NOT NULL DEFAULT '500',
  `crystal_start` int(11) unsigned NOT NULL DEFAULT '500',
  `deuterium_start` int(11) unsigned NOT NULL DEFAULT '0',
  `initial_fields` smallint(5) unsigned NOT NULL DEFAULT '163',
  `initial_temp` int(3) unsigned NOT NULL DEFAULT '50',
  `metal_basic_income` int(11) NOT NULL DEFAULT '20',
  `crystal_basic_income` int(11) NOT NULL DEFAULT '10',
  `deuterium_basic_income` int(11) NOT NULL DEFAULT '0',
  `energy_basic_income` int(11) NOT NULL DEFAULT '0',
  `moon_factor` float(2,1) NOT NULL DEFAULT '1.0',
  `moon_chance` tinyint(3) unsigned NOT NULL DEFAULT '20',
  `debris_moon` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `moonSizeFactor` float(4,3) unsigned NOT NULL DEFAULT '1.000',
  `cascading_moon_chance` float(3,1) unsigned NOT NULL DEFAULT '0.0',
  
  `expo_ress_met_chance` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `expo_ress_crys_chance` tinyint(3) unsigned NOT NULL DEFAULT '33',
  `expo_ress_deut_chance` tinyint(3) unsigned NOT NULL DEFAULT '17',

  `min_build_time` tinyint(2) NOT NULL DEFAULT '1',
  `user_valid` tinyint(1) NOT NULL DEFAULT '0',
  `adm_attack` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `debug` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `Fleet_Cdr` tinyint(3) unsigned NOT NULL DEFAULT '30',
  `Defs_Cdr` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `max_overflow` float(2,1) NOT NULL DEFAULT '1.0',

  `factor_university` tinyint(3) unsigned NOT NULL DEFAULT '8',
  `max_fleets_per_acs` tinyint(3) unsigned NOT NULL DEFAULT '16',
  `max_participants_per_acs` tinyint(3) unsigned NOT NULL DEFAULT '5',
  `silo_factor` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `vmode_min_time` int(11) NOT NULL DEFAULT '259200',
  `jumpgate_factor` int(11) NOT NULL DEFAULT '1',
  `noobprotection` int(11) NOT NULL DEFAULT '1',
  `noobprotectiontime` int(11) NOT NULL DEFAULT '5000',
  `noobprotectionmulti` int(11) NOT NULL DEFAULT '5',
  `alliance_create_min_points` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  
  `trade_allowed_ships` varchar(255) NOT NULL DEFAULT '202,203,204,205,206,207,208,209,210,211,212,213,214,215',
  `trade_charge` varchar(5) NOT NULL DEFAULT '30',
  `OverviewNewsFrame` tinyint(1) NOT NULL DEFAULT '1',
  `OverviewNewsText` text NOT NULL,
  PRIMARY KEY (`uni`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

CREATE TABLE `%PREFIX%config_universe_modules` (
  `uni` int(11) NOT NULL,
  `module` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `module_name` varchar(100) NOT NULL DEFAULT '',
  `state` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`uni`, `module`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;