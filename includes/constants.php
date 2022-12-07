<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 * For the full copyright and license information, please view the LICENSE
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

//SET TIMEZONE (if Server Timezone are not correct)
//date_default_timezone_set('America/Chicago');

//TEMPLATES DEFAULT SETTINGS
define('DEFAULT_THEME', 'nova');
define('HTTPS', isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == 'on');
define('PROTOCOL', HTTPS ? 'https://' : 'http://');
if (PHP_SAPI === 'cli') {
    $requestUrl = str_replace([dirname(dirname(__FILE__)), '\\'], ['', '/'], $_SERVER["PHP_SELF"]);

    //debug mode
    define('HTTP_BASE', str_replace(['\\', '//'], '/', dirname($_SERVER['SCRIPT_NAME']) . '/'));
    define('HTTP_ROOT', str_replace(basename($_SERVER['SCRIPT_FILENAME']), '', parse_url($requestUrl, PHP_URL_PATH)));

    define('HTTP_FILE', basename($_SERVER['SCRIPT_NAME']));
    define('HTTP_HOST', '127.0.0.1');
    define('HTTP_PATH', PROTOCOL . HTTP_HOST . HTTP_ROOT);
} else {
    define('HTTP_BASE', str_replace(['\\', '//'], '/', dirname($_SERVER['SCRIPT_NAME']) . '/'));
    define(
        'HTTP_ROOT',
        str_replace(
            basename($_SERVER['SCRIPT_FILENAME']),
            '',
            parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        )
    );

    define('HTTP_FILE', basename($_SERVER['SCRIPT_NAME']));
    define('HTTP_HOST', $_SERVER['HTTP_HOST']);
    define('HTTP_PATH', PROTOCOL . HTTP_HOST . HTTP_ROOT);
}

if (!defined('CACHE_PATH')) {
    define('CACHE_PATH', ROOT_PATH . 'cache/');
}

define('DISCORD_URL', 'https://discord.gg/jhYYN3yuat');

define('COMBAT_ENGINE', 'xnova');

// For Fatal Errors!
define('DEFAULT_LANG', 'de');

// SUPPORT WILDCAST DOMAINS
define('UNIS_WILDCAST', false);

// FIELDS FOR EACH LEVEL OF THE LUNAR BASE
define('FIELDS_BY_MOONBASIS_LEVEL', 3);

// FIELDS FOR EACH LEVEL OF THE TERRAFORMER
define('FIELDS_BY_TERRAFORMER', 5);

// TIME IN SECONDS for 24 hours
define('ONE_DAY', 86400);

// TIME IN SECONDS, TO (i) APPEAR ON GALAXY
define('INACTIVE', 604800);

// TIME IN SECONDS, TO (i I) APPEAR ON GALAXY
define('INACTIVE_LONG', 2419200);

// FEE FOR CANCEL QUEUE IN SHIPYARD
define('FACTOR_CANCEL_SHIPYARD', 0.6);

// MINIMUM FLEET TIME
define('MIN_FLEET_TIME', 5);

// PHALANX COST'S
define('PHALANX_DEUTERIUM', 5000);

// Time of changable nick after changing nick.
define('USERNAME_CHANGETIME', 604800);

// Max Results in Searchpage (-1 = disable)
define('SEARCH_LIMIT', 25);

// Messages per page at message list
define('MESSAGES_PER_PAGE', 10);

// banned users per page at ban list
define('BANNED_USERS_PER_PAGE', 25);

// How much IP Block ll be checked
// 1 = (AAA); 2 = (AAA.BBB); 3 = (AAA.BBB.CCC)
define('COMPARE_IP_BLOCKS', 2);

// Max Round on Combats
define('MAX_ATTACK_ROUNDS', 6);

// Enable the one-Click SImulation on Spy-Raports
define('ENABLE_SIMULATOR_LINK', true);

// Max. User Session in Seconds
define('SESSION_LIFETIME', 43200);

// ENABLE Mutlialert on sending fleets
define('ENABLE_MULTIALERT', false);

// UTF-8 support for names (required for non-english chars!)
define('UTF8_SUPPORT', true);

// Prevent the use of one account on multiple devices
define('PREVENT_MULTISESSIONS', false);

// Define, how its more hard to spy all inforation
/*
    if [Spy tech level of sender] > [Spy tech level of target]
        min amount of spies = -1 * (abs([Spy tech level of sender] - [Spy tech level of target])
            * SPY_DIFFENCE_FACTOR) ^ 2;
    else
        min amount of spies = -1 * (abs([Spy tech level of sender] - [Spy tech level of target])
            * SPY_DIFFENCE_FACTOR) ^ 2;

*/
define('SPY_DIFFENCE_FACTOR', 1);

// Define, how its more hard to spy all inforation
/*
    min amount of spies = see MissionCaseSpy.php#78

    To see Fleet        = {min amount of spies}
    To see Defense      = {min amount of spies} + 1 * SPY_VIEW_FACTOR
    To see Buildings    = {min amount of spies} + 3 * SPY_VIEW_FACTOR
    To see Technology   = {min amount of spies} + 5 * SPY_VIEW_FACTOR
*/
define('SPY_VIEW_FACTOR', 1);

// Bash Settings
define('BASH_ON', true);
define('BASH_COUNT', 6);
define('BASH_TIME', 86400);

// Bash rule on wars:
// 0 = NORMAL
// 1 = ON WAR, BASH RULE IS DEACTIVE
define('BASH_WAR', 1);

// MINIMUM FLEET TIME MUST HIGHER THEN BASH_TIME
define('FLEETLOG_AGE', 86400);

// Root IDs
define('ROOT_UNI', 1);
define('ROOT_USER', 1);

// AUTHLEVEL
define('AUTH_ADM', 3);
define('AUTH_OPS', 2);
define('AUTH_MOD', 1);
define('AUTH_USR', 0);

// Modules
define('MODULE_AMOUNT', 47); //+1 to largest index
define('MODULE_ALLIANCE', 0);
define('MODULE_BANLIST', 21);
define('MODULE_BANNER', 37);
define('MODULE_BATTLEHALL', 12);
define('MODULE_BUDDYLIST', 6);
define('MODULE_BUILDING', 2);
define('MODULE_FLEET_EVENTS', 10);
define('MODULE_FLEET_TABLE', 9);
define('MODULE_FLEET_TRADER', 38);
define('MODULE_GALAXY', 11);
define('MODULE_IMPERIUM', 15);
define('MODULE_INFORMATION', 14);
define('MODULE_MESSAGES', 16);
define('MODULE_MISSILEATTACK', 40);
define('MODULE_MISSION_ATTACK', 1);
define('MODULE_MISSION_ACS', 42);
define('MODULE_MISSION_COLONY', 35);
define('MODULE_MISSION_DESTROY', 29);
define('MODULE_MISSION_EXPEDITION', 30);
define('MODULE_MISSION_HOLD', 33);
define('MODULE_MISSION_RECYCLE', 32);
define('MODULE_MISSION_TRADE', 43);
define('MODULE_MISSION_SPY', 24);
define('MODULE_MISSION_STATION', 36);
define('MODULE_MISSION_TRANSPORT', 34);
define('MODULE_MISSION_TRANSFER', 44);
define('MODULE_NOTICE', 17);
define('MODULE_PHALANX', 19);
define('MODULE_PLAYERCARD', 20);
define('MODULE_RECORDS', 22);
define('MODULE_RESEARCH', 3);
define('MODULE_RESSOURCE_LIST', 23);
define('MODULE_SEARCH', 26);
define('MODULE_SHIPYARD_FLEET', 4);
define('MODULE_SHIPYARD_DEFENSIVE', 5);
define('MODULE_SHORTCUTS', 41);
define('MODULE_SIMULATOR', 39);
define('MODULE_STATISTICS', 25);
define('MODULE_SUPPORT', 27);
define('MODULE_TECHTREE', 28);
define('MODULE_MARKET_TRADE', 45);
define('MODULE_MARKET_TRANSFER', 46);

// FLEET STATE
define('FLEET_OUTWARD', 0);
define('FLEET_RETURN', 1);
define('FLEET_HOLD', 2);

// ELEMENT FLAGS
define('ELEMENT_BUILD', 1); # ID 0 - 99
define('ELEMENT_TECH', 2); # ID 101 - 199
define('ELEMENT_FLEET', 4); # ID 201 - 399
define('ELEMENT_DEFENSIVE', 8); # ID 401 - 599
define('ELEMENT_OFFICIER', 16); # ID 601 - 699
define('ELEMENT_BONUS', 32); # ID 701 - 799
define('ELEMENT_RACE', 64); # ID 801 - 899
define('ELEMENT_PLANET_RESOURCE', 128); # ID 901 - 949
define('ELEMENT_USER_RESOURCE', 256); # ID 951 - 999

// .. 512, 1024, 2048, 4096, 8192, 16384, 32768

define('ELEMENT_PRODUCTION', 65536);
define('ELEMENT_STORAGE', 131072);
define('ELEMENT_ONEPERPLANET', 262144);
define('ELEMENT_BOUNS', 524288);
define('ELEMENT_BUILD_ON_PLANET', 1048576);
define('ELEMENT_BUILD_ON_MOONS', 2097152);
define('ELEMENT_RESOURCE_ON_TF', 4194304);
define('ELEMENT_RESOURCE_ON_FLEET', 8388608);
define('ELEMENT_RESOURCE_ON_STEAL', 16777216);

// Resources

define('RESOURCE_METAL', 901);
define('RESOURCE_CRYSTAL', 902);
define('RESOURCE_DEUT', 903);

// ships

define('SHIP_SMALL_CARGO', 202);
define('SHIP_LARGE_CARGO', 203);
define('SHIP_LIGHT_FIGHTER', 204);
define('SHIP_HEAVY_FIGHTER', 205);
define('SHIP_CRUISER', 206);
define('SHIP_BATTLESHIP', 207);
define('SHIP_PROBE', 210);
define('SHIP_BOMBER', 211);
define('SHIP_DESTROYER', 213);
define('SHIP_BATTLECRUISER', 215);

// Missions
define('MISSION_ATTACK', 1);
define('MISSION_ACS', 2);
define('MISSION_TRANSPORT', 3);
define('MISSION_STATION', 4);
define('MISSION_HOLD', 5);
define('MISSION_SPY', 6);
define('MISSION_COLONISATION', 7);
define('MISSION_RECYCLING', 8);
define('MISSION_DESTRUCTION', 9);
define('MISSION_MISSILE', 10);
define('MISSION_EXPEDITION', 15);
define('MISSION_TRADE', 16);
define('MISSION_TRANSFER', 17);

// general time constants
define('TIME_6_HOURS', 21600);
define('TIME_24_HOURS', 86400);
define('TIME_1_WEEK', 604800);
define('TIME_1_MONTH', 2592000);

// universe status
define('STATUS_OPEN', 0);
define('STATUS_CLOSED', 1);
define('STATUS_REG_ONLY', 2);
define('STATUS_LOGIN_ONLY', 3);