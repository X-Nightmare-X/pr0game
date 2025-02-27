<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

define('DB_VERSION_REQUIRED', 66);
define('DB_NAME'			, $database['databasename']);
define('DB_PREFIX'			, $database['tableprefix']);

// Data Tabells
$dbTableNames   = [
    'ACHIEVEMENTS'            => DB_PREFIX . 'achievements',
    'ADVANCED_STATS'          => DB_PREFIX . 'advanced_stats',
    'AKS'                     => DB_PREFIX . 'aks',
    'ALLIANCE'                => DB_PREFIX . 'alliance',
    'ALLIANCE_RANK'           => DB_PREFIX . 'alliance_ranks',
    'ALLIANCE_REQUEST'        => DB_PREFIX . 'alliance_request',
    'BANNED'                  => DB_PREFIX . 'banned',
    'BUDDY'                   => DB_PREFIX . 'buddy',
    'BUDDY_REQUEST'           => DB_PREFIX . 'buddy_request',
    'CONFIG'                  => DB_PREFIX . 'config',
    'CONFIG_UNIVERSE'         => DB_PREFIX . 'config_universe',
    'CONFIG_UNIVERSE_MODULES' => DB_PREFIX . 'config_universe_modules',
    'CRONJOBS'                => DB_PREFIX . 'cronjobs',
    'CRONJOBS_LOG'            => DB_PREFIX . 'cronjobs_log',
    'DIPLO'                   => DB_PREFIX . 'diplo',
    'FLEETS'                  => DB_PREFIX . 'fleets',
    'FLEETS_EVENT'            => DB_PREFIX . 'fleet_event',
    'LOG'                     => DB_PREFIX . 'log',
    'LOG_FLEETS'              => DB_PREFIX . 'log_fleets',
    'LOSTPASSWORD'            => DB_PREFIX . 'lostpassword',
    'NEWS'                    => DB_PREFIX . 'news',
    'NOTES'                   => DB_PREFIX . 'notes',
    'MARKETPLACE'             => DB_PREFIX . 'marketplace',
    'MESSAGES'                => DB_PREFIX . 'messages',
    'MULTI'                   => DB_PREFIX . 'multi',
    'MULTI_TO_USERS'          => DB_PREFIX . 'multi_to_users',
    'PHALANX_FLEETS'          => DB_PREFIX . 'phalanx_fleets',
    'PHALANX_LOG'             => DB_PREFIX . 'phalanx_log',
    'PLANETS'                 => DB_PREFIX . 'planets',
    'PLANET_WRECKFIELD'       => DB_PREFIX . 'planet_wreckfield',
    'RW'                      => DB_PREFIX . 'raports',
    'RECORDS'                 => DB_PREFIX . 'records',
    'RECAPTCHA'               => DB_PREFIX . 'recaptcha',
    'SESSION'                 => DB_PREFIX . 'session',
    'SHORTCUTS'               => DB_PREFIX . 'shortcuts',
    'STATPOINTS'              => DB_PREFIX . 'statpoints',
    'SYSTEM'                  => DB_PREFIX . 'system',
    'TICKETS'                 => DB_PREFIX . 'ticket',
    'TICKETS_ANSWER'          => DB_PREFIX . 'ticket_answer',
    'TICKETS_CATEGORY'        => DB_PREFIX . 'ticket_category',
    'TOPKB'                   => DB_PREFIX . 'topkb',
    'TOPKB_USERS'             => DB_PREFIX . 'users_to_topkb',
    'TRADES'                  => DB_PREFIX . 'trades',
    'USERS'                   => DB_PREFIX . 'users',
    'USERS_BLOCKLIST'         => DB_PREFIX . 'users_blocklist',
    'USERS_TO_ACHIEVEMENTS'   => DB_PREFIX . 'users_to_achievements',
    'USERS_TO_ACS'            => DB_PREFIX . 'users_to_acs',
    'USERS_COMMENTS'          => DB_PREFIX . 'users_comments',
    'USERS_ACS'               => DB_PREFIX . 'users_to_acs',
    'USERS_VALID'             => DB_PREFIX . 'users_valid',
    'VARS'                    => DB_PREFIX . 'vars',
    'VARS_RAPIDFIRE'          => DB_PREFIX . 'vars_rapidfire',
    'VARS_REQUIRE'            => DB_PREFIX . 'vars_requriements',
];
// MOD-TABLES
