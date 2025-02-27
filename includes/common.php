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

if (isset($_POST['GLOBALS']) || isset($_GET['GLOBALS'])) {
    exit('You cannot set the GLOBALS-array from outside the script.');
}

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding("UTF-8");
}

ignore_user_abort(true);
error_reporting(E_ALL & ~E_STRICT);


// If date.timezone is invalid
date_default_timezone_set(@date_default_timezone_get());

ini_set('display_errors', 1);
header('Content-Type: text/html; charset=UTF-8');
define('TIMESTAMP', time());

require 'includes/classes/class.Singleton.php';
require 'includes/constants.php';

ini_set('log_errors', 'On');
ini_set('error_log', 'includes/error.log');

require 'includes/GeneralFunctions.php';
set_exception_handler('exceptionHandler');
set_error_handler('errorHandler');

require 'includes/classes/ArrayUtil.class.php';
require 'includes/classes/Cache.class.php';
require 'includes/classes/Database.class.php';
require 'includes/classes/Config.class.php';
require 'includes/classes/class.FleetFunctions.php';
require_once 'includes/classes/HTTP.class.php';
require 'includes/classes/Language.class.php';
require 'includes/classes/PlayerUtil.class.php';
require 'includes/classes/Session.class.php';
require_once('includes/classes/Universe.class.php');

require 'includes/classes/class.theme.php';
require 'includes/classes/class.template.php';

// Say Browsers to Allow ThirdParty Cookies (Thanks to morktadela)
HTTP::sendHeader('P3P', 'CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
define('AJAX_REQUEST', HTTP::_GP('ajax', 0));

$THEME      = new Theme();
Singleton()->THEME = $THEME;
$THEME =& Singleton()->THEME;

if (MODE === 'INSTALL') {
    return;
}

if (!file_exists('includes/config.php') || filesize('includes/config.php') === 0) {
    HTTP::redirectTo('install/index.php');
}

$config = Config::get();
date_default_timezone_set($config->timezone);

$USER = [];
$USER['lang'] = 'en';
$USER['bana'] = 0;
$USER['timezone'] = $config->timezone;
$USER['urlaubs_modus'] = 0;
$USER['authlevel'] = 0;
$USER['id'] = 0;
$USER['signalColors'] = PlayerUtil::player_signal_colors();
$USER['colors'] = PlayerUtil::player_colors();
$USER['stb_settings'] = PlayerUtil::player_stb_settings();
Singleton()->USER = $USER;
$USER =& Singleton()->USER;

if (MODE === 'UPGRADE') {
    return;
}

try {
    $sql    = "SELECT dbVersion FROM %%SYSTEM%%;";

    $dbVersion  = Database::get()->selectSingle($sql, [], 'dbVersion');

    $dbNeedsUpgrade = $dbVersion < DB_VERSION_REQUIRED;
} catch (Exception $e) {
    $dbNeedsUpgrade = true;
}

if ($dbNeedsUpgrade) {
    HTTP::redirectTo('install/index.php?mode=upgrade');
}

if (defined('DATABASE_VERSION') && DATABASE_VERSION === 'OLD') {
    /* For our old Admin panel */
    require 'includes/classes/Database_BC.class.php';
    $DATABASE   = new Database_BC();

    $dbTableNames   = Database::get()->getDbTableNames();
    $dbTableNames   = array_combine($dbTableNames['keys'], $dbTableNames['names']);

    foreach ($dbTableNames as $dbAlias => $dbName) {
        define(substr($dbAlias, 2, -2), $dbName);
    }
}

if (MODE === 'INGAME' || MODE === 'ADMIN' || MODE === 'CRON') {
    $session    = Session::load();

    $raportWithoutSession = !$session->isValidSession() && isset($_GET['page']) && $_GET['page'] == "raport" && isset($_GET['raport']) && count($_GET) == 2 && MODE === 'INGAME';

    if (!$raportWithoutSession) {
        if (!$session->isValidSession()) {
            $session->delete();
            HTTP::redirectTo('index.php?code=3');
        }
    }

    require 'includes/vars.php';
    require 'includes/classes/class.BuildFunctions.php';
    require 'includes/classes/class.PlanetRessUpdate.php';

    if (!AJAX_REQUEST && MODE === 'INGAME' && isModuleAvailable(MODULE_FLEET_EVENTS)) {
        require('includes/FleetHandler.php');
    }

    $db     = Database::get();

    if (!$raportWithoutSession) {
        $sql    = "SELECT
    	user.*, s.total_points,
    	COUNT(message.message_id) as messages,
        CASE
            WHEN COUNT(DISTINCT message.message_type) = 1 THEN message.message_type
            ELSE ''
        END AS message_type
    	FROM %%USERS%% as user
        LEFT JOIN %%STATPOINTS%% s ON s.id_owner = user.id AND s.stat_type = :statTypeUser
    	LEFT JOIN %%MESSAGES%% as message ON message.message_owner = user.id AND message.message_unread = :unread
    	WHERE user.id = :userId
    	GROUP BY message.message_owner;";

        $USER   = $db->selectSingle($sql, [
            ':statTypeUser'     => 1,
            ':unread'           => 1,
            ':userId'           => $session->userId
        ]);
        Singleton()->USER = $USER;
        $USER =& Singleton()->USER;

        if (empty($USER)) {
            HTTP::redirectTo('index.php?code=3');
        } else {
            if ($USER['banaday'] <= TIMESTAMP) {
                $sql = "UPDATE %%USERS%% SET bana = 0, banaday = 0 WHERE id = :userId";
                $db->update($sql, [
                    ':userId'   => $session->userId,
                ]);
                $USER['bana'] = 0;
                $USER['banaday'] = 0;
            }

            $USER['signalColors'] = PlayerUtil::player_signal_colors($USER);
            $USER['colors'] = PlayerUtil::player_colors($USER);
        }
    }

    $LNG    = new Language($USER['lang']);
    Singleton()->LNG = $LNG;
    $LNG =& Singleton()->LNG;
    $LNG->includeData(['L18N', 'INGAME', 'TECH', 'CUSTOM']);
    if (!empty($USER['dpath'])) {
        $THEME->setUserTheme($USER['dpath']);
    }

    if ($USER['bana'] == 1) {
        $sql = 'SELECT theme FROM %%BANNED%% WHERE who = :username';
        $reason = $db->selectSingle($sql, [
            ':username' => $USER['username'],
        ], 'theme');

        ShowErrorPage::printError("<font size=\"6px\">" . $LNG['css_account_banned_message'] . "</font><br><br>" .
        sprintf($LNG['css_account_banned_expire'], _date($LNG['php_tdformat'], $USER['banaday'], $USER['timezone']), $config->forum_url) . "<br><br>" .
        sprintf($LNG['css_account_banned_reason'], $reason) . "<br><br>" .
        $LNG['css_goto_homeside'], false);
    }

    if (!$raportWithoutSession) {
        if (MODE === 'INGAME') {
            $universeAmount = count(Universe::availableUniverses());
            if (Universe::current() != $USER['universe'] && $universeAmount > 1) {
                HTTP::redirectToUniverse($USER['universe']);
            }

            $session->selectActivePlanet();

            $sql    = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
            $PLANET = $db->selectSingle($sql, [
            ':planetId' => $session->planetId,
            ]);
            Singleton()->PLANET = $PLANET;
            $PLANET =& Singleton()->PLANET;

            if (empty($PLANET)) {
                $sql    = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
                $PLANET = $db->selectSingle($sql, [
                    ':planetId' => $USER['id_planet'],
                ]);
                Singleton()->PLANET = $PLANET;
                $PLANET =& Singleton()->PLANET;

                if (empty($PLANET)) {
                    throw new Exception("Main Planet does not exist!");
                } else {
                    $session->planetId = $USER['id_planet'];
                }
            }

            $USER['PLANETS']    = getPlanets($USER);
        } elseif (MODE === 'ADMIN') {
            error_reporting(E_ERROR | E_WARNING | E_PARSE);

            $USER['rights']     = !empty($USER['rights']) ? unserialize($USER['rights']) : [];
            $LNG->includeData(['ADMIN', 'CUSTOM']);
        }
    }

    if (
        ($config->uni_status == STATUS_CLOSED || $config->uni_status == STATUS_REG_ONLY) && $USER['authlevel'] == AUTH_USR &&
        (!isset($_GET['page']) || $_GET['page'] !== 'logout') && !$raportWithoutSession
    ) {
        ShowErrorPage::printError($LNG['sys_closed_game'] . '<br><br>' . $config->close_reason, false);
    }
    $privKey = $config->recaptcha_priv_key;
    $rcaptcha = HTTP::_GP('rcaptcha', '');

    if (isset($privKey) && isset($rcaptcha)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['secret' => $privKey, 'response' => $rcaptcha]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $arrResponse = json_decode($response, true);
        $sql = "insert into %%RECAPTCHA%% (userId, success, time, score, url) VALUES (:userId, :success, :time, :score, :url);";

        if (isset($arrResponse) && $arrResponse['success'] == true) {
            $db->insert($sql, [
                ':userId'   => $USER['id'],
                ':success'  => $arrResponse['success'],
                ':time'     => TIMESTAMP,
                ':score'    => $arrResponse['score'],
                ':url'      => $_SERVER['REQUEST_URI'],
            ]);
        }
    }
} elseif (MODE === 'LOGIN') {
    $LNG    = new Language();
    Singleton()->LNG = $LNG;
    $LNG->getUserAgentLanguage();
    $LNG->includeData(['L18N', 'INGAME', 'PUBLIC', 'CUSTOM']);
}
