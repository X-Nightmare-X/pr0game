<?php

require_once __DIR__.'/vendor/autoload.php';

define('MODE', 'UPGRADE');
define('ROOT_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/');
set_include_path(ROOT_PATH);

require 'includes/common.php';

$db = Database::get();

$sql = 'ALTER TABLE `%PREFIX%config` RENAME `%PREFIX%config_old`';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'CREATE TABLE `%PREFIX%config` (
  `game_name` varchar(30) NOT NULL,
  `version` varchar(8) NOT NULL,
  
  `timezone` varchar(32) NOT NULL DEFAULT "Europe/London",
  `dst` enum("0","1","2") NOT NULL DEFAULT "2", -- unused, summertime(LNG"op_dst_mode"): 0=no, 1=yes, 2=automatic, no input field
  `forum_url` varchar(128) NOT NULL DEFAULT "", -- board page redirect, input field in universe
  `git_issues_link` varchar(128) NOT NULL DEFAULT "<git_issues_link>",
  `ttf_file` varchar(128) NOT NULL DEFAULT "styles/resource/fonts/DroidSansMono.ttf",
  
  `mail_active` tinyint(1) NOT NULL DEFAULT 0,
  `smtp_sendmail` varchar(64) NOT NULL DEFAULT "",
  `mail_use` tinyint(1) NOT NULL DEFAULT 0,
  `smail_path` varchar(30) NOT NULL DEFAULT "/usr/sbin/sendmail",
  `smtp_ssl` enum("","ssl","tls") NOT NULL DEFAULT "",
  `smtp_host` varchar(64) NOT NULL DEFAULT "",
  `smtp_port` smallint(5) NOT NULL DEFAULT 0,
  `smtp_user` varchar(64) NOT NULL DEFAULT "",
  `smtp_pass` varchar(32) NOT NULL DEFAULT "",

  `del_oldstuff` tinyint(3) unsigned NOT NULL DEFAULT 30,
  `del_user_manually` tinyint(3) unsigned NOT NULL DEFAULT 7,
  `user_valid` tinyint(1) NOT NULL DEFAULT 0,
  `sendmail_inactive` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `del_user_sendmail` tinyint(3) unsigned NOT NULL DEFAULT 21,
  
  `message_delete_behavior` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `message_delete_days` tinyint(3) unsigned NOT NULL DEFAULT 7,
  
  `use_google_analytics` varchar(42) NOT NULL DEFAULT 0,
  `google_analytics_key` varchar(42) NOT NULL DEFAULT "",

  `recaptcha_priv_key` varchar(255) DEFAULT "",
  `recaptcha_pub_key` varchar(255) DEFAULT "",

  `disclamer_address` text,
  `disclamer_phone` text,
  `disclamer_mail` text,
  `disclamer_notice` text,
  
  `stat_settings` int(11) unsigned NOT NULL DEFAULT 1000,
  `stat` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `stat_level` tinyint(3) unsigned NOT NULL DEFAULT 2
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'INSERT INTO `%PREFIX%config` (
    `game_name`, `version`, `timezone`, `dst`, `forum_url`, `git_issues_link`, `ttf_file`, `mail_active`, `mail_use`, `smtp_sendmail`, `smail_path`, `smtp_host`,
    `smtp_ssl`, `smtp_port`, `smtp_user`, `smtp_pass`, `del_oldstuff`, `del_user_manually`, `user_valid`, `sendmail_inactive`, `del_user_sendmail`,
    `message_delete_behavior`, `message_delete_days`, `use_google_analytics`, `google_analytics_key`, `recaptcha_priv_key`, `recaptcha_pub_key`,
    `disclamer_address`, `disclamer_phone`, `disclamer_mail`, `disclamer_notice`, `stat_settings`, `stat`, `stat_level`
  )
  SELECT `game_name`, `VERSION` AS `version`, `timezone`, `dst`, `forum_url`, `git_issues_link`, `ttf_file`, `mail_active`, `mail_use`, `smtp_sendmail`,
    `smail_path`, `smtp_host`, `smtp_ssl`, `smtp_port`, `smtp_user`, `smtp_pass`, `del_oldstuff`, `del_user_manually`, `user_valid`, `sendmail_inactive`,
    `del_user_sendmail`, `message_delete_behavior`, `message_delete_days`, `ga_active` AS `use_google_analytics`, `ga_key` AS `google_analytics_key`,
    `recaptchaPrivKey` AS `recaptcha_priv_key`, `recaptchaPubKey` AS `recaptcha_pub_key`, `disclamerAddress` AS `disclamer_address`,
    `disclamerPhone` AS `disclamer_phone`, `disclamerMail` AS `disclamer_mail`, `disclamerNotice` AS `disclamer_notice`, `stat_settings`,
    `stat`, `stat_level`
  FROM `%PREFIX%config_old` WHERE `uni` = 1;';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'CREATE TABLE `%PREFIX%config_universe` (
  `uni` int(11) NOT NULL AUTO_INCREMENT,
  `last_galaxy_pos` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `last_system_pos` smallint(5) unsigned NOT NULL DEFAULT 1,
  `last_planet_pos` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `users_amount` int(11) unsigned NOT NULL DEFAULT 1, -- Update in statsbuilder + playerUtil(create). Used in stats banner, overview, statistics

  `uni_name` varchar(30) NOT NULL DEFAULT "",
  `lang` varchar(2) NOT NULL DEFAULT "",
  `building_speed` bigint(20) unsigned NOT NULL DEFAULT 2500,
  `shipyard_speed` bigint(20) unsigned NOT NULL DEFAULT 2500,
  `research_speed` bigint(20) unsigned NOT NULL DEFAULT 2500,
  `fleet_speed` bigint(20) unsigned NOT NULL DEFAULT 2500,
  `resource_multiplier` smallint(5) unsigned NOT NULL DEFAULT 1,
  `storage_multiplier` smallint(5) unsigned NOT NULL DEFAULT 1,
  `max_overflow` float(2,1) NOT NULL DEFAULT 1.0,
  `expo_hold_multiplier` smallint(5) unsigned NOT NULL DEFAULT 1, -- Expedition factor
  `energy_multiplier` smallint(6) NOT NULL DEFAULT 1,
  `del_user_automatic` tinyint(3) unsigned NOT NULL DEFAULT 90,
  `vmode_min_time` int(11) NOT NULL DEFAULT 259200,
  `alliance_create_min_points` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `debug` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `uni_status` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `close_reason` text,

  `max_elements_build` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `max_elements_tech` tinyint(3) unsigned NOT NULL DEFAULT 2,
  `max_elements_ships` tinyint(3) unsigned NOT NULL DEFAULT 10,
  `max_fleet_per_build` bigint(20) unsigned NOT NULL DEFAULT 1000000,
  `min_build_time` tinyint(2) NOT NULL DEFAULT 1,

  `ref_active` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ref_bonus` int(11) unsigned NOT NULL DEFAULT 1000,
  `ref_minpoints` bigint(20) unsigned NOT NULL DEFAULT 2000,
  `ref_max_referals` tinyint(1) unsigned NOT NULL DEFAULT 5,

  `deuterium_cost_galaxy` int(11) unsigned NOT NULL DEFAULT 10,
  `max_galaxy` tinyint(3) unsigned NOT NULL DEFAULT 9,
  `max_system` smallint(5) unsigned NOT NULL DEFAULT 400,
  `max_planets` tinyint(3) unsigned NOT NULL DEFAULT 15,
  `uni_type` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `galaxy_type` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `planet_creation` tinyint(1) unsigned NOT NULL DEFAULT 0,

  `max_initial_planets` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `max_additional_planets` TINYINT NOT NULL DEFAULT  11,
  `planets_per_tech` FLOAT( 2, 1 ) NOT NULL DEFAULT  0.5,
  `planet_size_factor` float(2,1) NOT NULL DEFAULT 1.0,
  `all_planet_pictures` tinyint(3) unsigned NOT NULL DEFAULT 0,

  `metal_start` int(11) unsigned NOT NULL DEFAULT 500,
  `crystal_start` int(11) unsigned NOT NULL DEFAULT 500,
  `deuterium_start` int(11) unsigned NOT NULL DEFAULT 0,
  `initial_fields` smallint(5) unsigned NOT NULL DEFAULT 163,
  `initial_temp` int(3) unsigned NOT NULL DEFAULT 50,
  `metal_basic_income` int(11) NOT NULL DEFAULT 20,
  `crystal_basic_income` int(11) NOT NULL DEFAULT 10,
  `deuterium_basic_income` int(11) NOT NULL DEFAULT 0,
  `energy_basic_income` int(11) NOT NULL DEFAULT 0,
  `moon_factor` float(2,1) NOT NULL DEFAULT 1.0,
  `moon_chance` tinyint(3) unsigned NOT NULL DEFAULT 20,
  `debris_moon` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `moon_size_factor` float(4,3) unsigned NOT NULL DEFAULT 1.000,
  `cascading_moon_chance` float(3,1) unsigned NOT NULL DEFAULT 0.0,
  
  `factor_university` tinyint(3) unsigned NOT NULL DEFAULT 8,
  `silo_factor` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `jumpgate_factor` int(11) NOT NULL DEFAULT 1,

  `expo_ress_met_chance` tinyint(3) unsigned NOT NULL DEFAULT 50,
  `expo_ress_crys_chance` tinyint(3) unsigned NOT NULL DEFAULT 33,
  `expo_ress_deut_chance` tinyint(3) unsigned NOT NULL DEFAULT 17,

  `fleet_debris_percentage` tinyint(3) unsigned NOT NULL DEFAULT 30,
  `def_debris_percentage` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `max_fleets_per_acs` tinyint(3) unsigned NOT NULL DEFAULT 16,
  `max_participants_per_acs` tinyint(3) unsigned NOT NULL DEFAULT 5,
  `noob_protection` int(11) NOT NULL DEFAULT 1,
  `noob_protection_time` int(11) NOT NULL DEFAULT 5000,
  `noob_protection_multi` int(11) NOT NULL DEFAULT 5,
  `adm_attack` tinyint(1) unsigned NOT NULL DEFAULT 0,
  
  `trade_allowed_ships` varchar(255) NOT NULL DEFAULT "202,203,204,205,206,207,208,209,210,211,212,213,214,215",
  `trade_charge` varchar(5) NOT NULL DEFAULT 30,

  `overview_news_frame` tinyint(1) NOT NULL DEFAULT 1,
  `overview_news_text` text,
  PRIMARY KEY (`uni`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'INSERT INTO `%PREFIX%config_universe` (
    `uni`, `last_galaxy_pos`, `last_system_pos`, `last_planet_pos`, `users_amount`, `uni_name`, `lang`, `building_speed`, `shipyard_speed`, `research_speed`,
    `fleet_speed`, `resource_multiplier`, `storage_multiplier`, `max_overflow`, `expo_hold_multiplier`, `energy_multiplier`, `del_user_automatic`,
    `vmode_min_time`, `alliance_create_min_points`, `debug`, `uni_status`, `close_reason`, `max_elements_build`, `max_elements_tech`, `max_elements_ships`,
    `max_fleet_per_build`, `min_build_time`, `ref_active`, `ref_bonus`, `ref_minpoints`, `ref_max_referals`, `deuterium_cost_galaxy`, `max_galaxy`,
    `max_system`, `max_planets`, `uni_type`, `galaxy_type`, `planet_creation`, `max_initial_planets`, `max_additional_planets`, `planets_per_tech`,
    `planet_size_factor`, `all_planet_pictures`, `metal_start`, `crystal_start`, `deuterium_start`, `initial_fields`, `initial_temp`, `metal_basic_income`,
    `crystal_basic_income`, `deuterium_basic_income`, `energy_basic_income`, `moon_factor`, `moon_chance`, `debris_moon`, `moon_size_factor`,
    `cascading_moon_chance`, `factor_university`, `silo_factor`, `jumpgate_factor`, `expo_ress_met_chance`, `expo_ress_crys_chance`, `expo_ress_deut_chance`, 
    `fleet_debris_percentage`, `def_debris_percentage`, `max_fleets_per_acs`, `max_participants_per_acs`, `noob_protection`, `noob_protection_time`, 
    `noob_protection_multi`, `adm_attack`, `trade_allowed_ships`, `trade_charge`, `overview_news_frame`, `overview_news_text`
    )
  SELECT `uni`, `LastSettedGalaxyPos` AS `last_galaxy_pos`, `LastSettedSystemPos` AS `last_system_pos`, `LastSettedPlanetPos` AS `last_planet_pos`,
    `users_amount`, `uni_name`, `lang`, `building_speed`, `shipyard_speed`, `research_speed`, `fleet_speed`, `resource_multiplier`, `storage_multiplier`,
    `max_overflow`, `halt_speed` AS `expo_hold_multiplier`, `energySpeed` AS `energy_multiplier`, `del_user_automatic`, `vmode_min_time`,
    `alliance_create_min_points`, `debug`, `uni_status`, `close_reason`, `max_elements_build`, `max_elements_tech`, `max_elements_ships`,
    `max_fleet_per_build`, `min_build_time`, `ref_active`, `ref_bonus`, `ref_minpoints`, `ref_max_referals`, `deuterium_cost_galaxy`, `max_galaxy`,
    `max_system`, `max_planets`, `uni_type`, `galaxy_type`, `planet_creation`, `min_player_planets` AS `max_initial_planets`,
    `planets_tech` AS `max_additional_planets`, `planets_per_tech`, `planet_factor` AS `planet_size_factor`, `all_planet_pictures`, `metal_start`, 
    `crystal_start`, `deuterium_start`, `initial_fields`, `initial_temp`, `metal_basic_income`, `crystal_basic_income`, `deuterium_basic_income`,
    `energy_basic_income`, `moon_factor`, `moon_chance`, `debris_moon`, `moonSizeFactor` AS `moon_size_factor`, `cascading_moon_chance`, `factor_university`,
    `silo_factor`, `jumpgate_factor`, `expo_ress_met_chance`, `expo_ress_crys_chance`, `expo_ress_deut_chance`, `Fleet_Cdr` AS `fleet_debris_percentage`,
    `Defs_Cdr` AS `def_debris_percentage`, `max_fleets_per_acs`, `max_participants_per_acs`, `noobprotection` AS `noob_protection`,
    `noobprotectiontime` AS `noob_protection_time`, `noobprotectionmulti` AS `noob_protection_multi`, `adm_attack`, `trade_allowed_ships`, `trade_charge`,
    `OverviewNewsFrame` AS `overview_news_frame`, `OverviewNewsText` AS `overview_news_text`
  FROM `%PREFIX%config_old`;';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'CREATE TABLE `%PREFIX%config_universe_modules` (
  `uni` int(11) NOT NULL,
  `module` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `module_name` varchar(100) NOT NULL DEFAULT "",
  `state` tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY (`uni`, `module`)
) ENGINE = InnoDB DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;';

$db->nativeQuery(str_replace('%PREFIX%', DB_PREFIX, $sql));

$sql = 'SELECT `uni`, `moduls` FROM `%PREFIX%config_old`;';
$modulsResult = $db->select(str_replace('%PREFIX%', DB_PREFIX, $sql));
foreach ($modulsResult as $row) {
  $moduls = explode(';', $row['moduls']);
  $modules =& Singleton()->modules;
  $rows = [];
  foreach ($modules as $moduleKey => $moduleName) {
    $rows[] = '('.$row['uni'].', '.$moduleKey.', "'.$moduleName.'", '.(isset($moduls[$moduleKey]) ? $moduls[$moduleKey] : 1).')';
  }
  $sql = 'INSERT INTO `%PREFIX%config_universe_modules` VALUES ' . implode(',', $rows);
  $db->insert(str_replace('%PREFIX%', DB_PREFIX, $sql));
}