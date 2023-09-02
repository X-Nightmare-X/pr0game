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

if (!allowedTo(str_replace([dirname(__FILE__), '\\', '/', '.php'], '', __FILE__))) {
    throw new Exception("Permission error!");
}
function ShowLog()
{
    $LNG =& Singleton()->LNG;
    $resources =& Singleton()->resources;
    $table = HTTP::_GP('type', '');

    # Modes:
    # 1 => Playerlist Changes
    #   target => Player-ID
    # 2 => Planetlist Changes
    #   target => Planet-ID
    # 3 => Div. Settings Pages
    #   target 0 => Server-Settings
    #   target 1 => Universe-Settings
    #   target 2 => Stat-Settings
    #   target 5 => Impressum-Settings
    # 4 => Presents
    # 5 => Transport Missions
    # 6 => Alliance Changes
    #   target => Alliance-ID
    #
    # TODO: LOG Search

    switch ($table) {
        case 'planet':
            ShowLogPlanetsList();
            break;
        case 'player':
            ShowLogPlayersList();
            break;
        case 'settings':
            ShowLogSettingsList();
            break;
        case 'present':
            ShowLogPresent();
            break;
        case 'alliance':
            ShowLogAlliance();
            break;
        case 'detail':
            ShowLogDetail();
            break;
        default:
            ShowLogOverview();
            break;
    }
}

function ShowLogOverview()
{
    $USER =& Singleton()->USER;
    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    $template->show("LogOverview.tpl");
}

function loopLogArray(&$LogArray, $Wrapper, $dbTableNames, $conf_after, $array) {
    foreach ($array as $key => $val) {
        if ($key != 'universe') {
            if (is_array($val)) {
                if (array_key_exists($key, $dbTableNames)) {
                    $LogArray[] = [
                        'Table' => $key,
                        'key' => $key2,
                    ];
                }
                loopLogArray($LogArray, $Wrapper, $dbTableNames, $conf_after, $val);
            } else {
                addToLogArray($LogArray, $Wrapper, $conf_after, $key, $val);
            }
        }
    }
}

function addToLogArray(&$LogArray, $Wrapper, $conf_after, $key, $val) {
    $LNG =& Singleton()->LNG;

    if (isset($LNG['tech'][$key])) {
        $Element = $LNG['tech'][$key];
    } elseif (isset($LNG['se_' . $key])) {
        $Element = $LNG['se_' . $key];
    } elseif (isset($LNG[$key])) {
        $Element = $LNG[$key];
    } elseif (isset($Wrapper[$key])) {
        $Element = $Wrapper[$key];
    } else {
        $Element = $key;
    }

    if ($Element == 'urlaubs_until') {
        $oldVaue = _date($LNG['php_tdformat'], $val);
    } elseif (is_numeric($val)) {
        $oldVaue = pretty_number($val);
    } else {
        $oldVaue = $val;
    }

    if (!isset($conf_after[$key])) {
        $newVaue = 'Not found in Log';
    } elseif ($Element == 'urlaubs_until') {
        $newVaue = _date($LNG['php_tdformat'], $conf_after[$key]);
    } elseif (is_numeric($conf_after[$key])) {
        $newVaue = pretty_number($conf_after[$key]);
    } else {
        $newVaue = $conf_after[$key];
    }

    $LogArray[] = [
        'Element'   => $Element,
        'old'       => $oldVaue,
        'new'       => $newVaue,
    ];
}

function ShowLogDetail()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $logid = HTTP::_GP('id', 0);
    $sql = "SELECT l.*, u_a.`username` as admin_username FROM %%LOG%% AS l
        LEFT JOIN %%USERS%% AS u_a ON u_a.`id` = l.`admin`
        WHERE l.`id` = :logid;";
    $result = Database::get()->selectSingle($sql, [
        ':logid' => $logid,
    ]);

    $data = unserialize($result['data']);
    var_dump($data[0]);
    var_dump($data[1]); die();
    $conf_before = [];
    $tables_before = [];
    $conf_after = [];
    $tables_after = [];
    foreach ($data[0] as $key => $i) {
        $conf_before[$key] = $i;
    }
    foreach ($data[1] as $key => $i) {
        $conf_after[$key] = $i;
    }

    $Wrapper = [
        'resource_multiplier' => $LNG['se_resources_producion_speed'],
        'forum_url'           => $LNG['se_forum_link'],

        'stat_settings'     => $LNG['cs_point_per_resources_used'],
        'stat'              => $LNG['cs_points_to_zero'],
        'stat_update_time'  => $LNG['cs_time_between_updates'],
        'stat_level'        => $LNG['cs_access_lvl'],

        'ga_key'        => $LNG['se_google_key'],

        'metal'         => $LNG['tech'][901],
        'crystal'       => $LNG['tech'][902],
        'deuterium'     => $LNG['tech'][903],

        'authattack'    => $LNG['qe_authattack'],
        'username'      => $LNG['adm_username'],
        'field_max'     => $LNG['qe_fields'],
    ];

    $LogArray = [];

    $dbTableNames = [];
    require_once 'includes/dbtables.php';
    loopLogArray($LogArray, $Wrapper, $dbTableNames, $conf_after, $conf_before);

    foreach ($conf_before as $key => $val) {
        if ($key != 'universe') {

            if (is_array($val)) {
                foreach ($val as $key2 => $val2) {
                    $LogArray[] = [
                        'Table' => $key,
                        'key' => $key2,
                    ];
                    if (is_array($val2)) {
                        foreach ($val2 as $key3 => $val3) {
                            if (is_array($val3)) {
                                foreach ($val3 as $key4 => $val4) {
                                    addToLogArray($LogArray, $Wrapper, $conf_after, $key4, $val4);
                                }
                            } else {
                                addToLogArray($LogArray, $Wrapper, $conf_after, $key3, $val3);
                            }
                        }
                    } else {
                        addToLogArray($LogArray, $Wrapper, $conf_after, $key2, $val2);
                    }
                }
            } else {
                addToLogArray($LogArray, $Wrapper, $conf_after, $key, $val);
            }
        }
    }

    $template   = new template();
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'admin'         => $result['admin_username'],
        'target'        => $result['universe'],
        'id'            => $result['id'],
        'time'          => _date($LNG['php_tdformat'], $result['time'], $USER['timezone']),
        'log_info'      => $LNG['log_info'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_target'    => $LNG['log_universe'],
        'log_id'        => $LNG['log_id'],
        'log_element'   => $LNG['log_element'],
        'log_old'       => $LNG['log_old'],
        'log_new'       => $LNG['log_new'],
        'signalColors'  => $USER['signalColors']
    ]);

    $template->show("LogDetail.tpl");
}

function ShowLogSettingsList()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $result = $GLOBALS['DATABASE']->query("SELECT l.id, l.admin, l.time, l.universe, l.target,u_a.username as"
        . " admin_username FROM " . LOG . " as l LEFT JOIN " . USERS . " as u_a ON  u_a.id = l.admin WHERE mode = 3"
        . " ORDER BY id DESC");

    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    if (empty($result)) {
        $template->message($LNG['log_no_data'], '?page=log');
        exit;
    }

    $targetkey = [
        0 => $LNG['log_ssettings'],
        1 => $LNG['log_usettings'],
        2 => $LNG['log_statsettings'],
        3 => '<removed>', // previously used for the chat
        4 => $LNG['log_tssettings'],
        5 => $LNG['log_disclamersettings']
    ];

    while ($LogRow = $GLOBALS['DATABASE']->fetch_array($result)) {
        $LogArray[] = [
            'id'            => $LogRow['id'],
            'admin'         => $LogRow['admin_username'],
            'target_uni'    => ($LogRow['target'] == 0 ? '' : $LogRow['universe']),
            'target'        => $targetkey[$LogRow['target']],
            'time'          => _date($LNG['php_tdformat'], $LogRow['time'], $USER['timezone']),
        ];
    }
    $GLOBALS['DATABASE']->free_result($result);
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'log_log'       => $LNG['log_log'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_uni'       => $LNG['log_uni_short'],
        'log_target'    => $LNG['log_target_universe'],
        'log_id'        => $LNG['log_id'],
        'log_view'      => $LNG['log_view'],
    ]);
    $template->show("LogList.tpl");
}

function ShowLogPlanetsList()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $result    = $GLOBALS['DATABASE']->query("SELECT DISTINCT l.id, l.admin, l.target, l.time, l.universe,u_t.username"
        . " as target_username, p.galaxy as target_galaxy, p.system as target_system, p.planet as target_planet,"
        . "u_a.username as admin_username FROM " . LOG . " as l LEFT JOIN " . USERS . " as u_a ON  u_a.id = l.admin"
        . " LEFT JOIN " . PLANETS . " as p ON p.id = l.target LEFT JOIN " . USERS . " as u_t ON u_t.id = p.id_owner"
        . " WHERE mode = 2 ORDER BY id DESC");

    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    if (empty($result)) {
        $template->message($LNG['log_no_data'], '?page=log');
        exit;
    }

    while ($LogRow = $GLOBALS['DATABASE']->fetch_array($result)) {
        $LogArray[] = [
            'id'        => $LogRow['id'],
            'admin'     => $LogRow['admin_username'],
            'target_uni' => $LogRow['universe'],
            'target'    => '[' . $LogRow['target_galaxy'] . ':' . $LogRow['target_system'] . ':'
                . $LogRow['target_planet'] . '] -> ' . $LogRow['target_username'],
            'time'      => _date($LNG['php_tdformat'], $LogRow['time'], $USER['timezone']),
        ];
    }
    $GLOBALS['DATABASE']->free_result($result);
    $template   = new template();
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'log_log'       => $LNG['log_log'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_uni'       => $LNG['log_uni_short'],
        'log_target'    => $LNG['log_target_planet'],
        'log_id'        => $LNG['log_id'],
        'log_view'      => $LNG['log_view'],
        'signalColors'  => $USER['signalColors']
    ]);
    $template->show("LogList.tpl");
}

function ShowLogPlayersList()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $result    = $GLOBALS['DATABASE']->query("SELECT DISTINCT l.id, l.admin, l.target, l.time, l.universe,u_t.username"
        . " as target_username,u_a.username as admin_username FROM " . LOG . " as l LEFT JOIN " . USERS
        . " as u_a ON  u_a.id = l.admin LEFT JOIN " . USERS . " as u_t ON u_t.id = l.target WHERE mode = 1 ORDER BY"
        . " l.id DESC");

    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    if (empty($result)) {
        $template->message($LNG['log_no_data'], '?page=log');
        exit;
    }

    while ($LogRow = $GLOBALS['DATABASE']->fetch_array($result)) {
        $LogArray[] = [
            'id'        => $LogRow['id'],
            'admin'     => $LogRow['admin_username'],
            'target_uni' => $LogRow['universe'],
            'target'    => $LogRow['target_username'],
            'time'      => _date($LNG['php_tdformat'], $LogRow['time'], $USER['timezone']),
        ];
    }
    $GLOBALS['DATABASE']->free_result($result);
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'log_log'       => $LNG['log_log'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_uni'       => $LNG['log_uni_short'],
        'log_target'    => $LNG['log_target_user'],
        'log_id'        => $LNG['log_id'],
        'log_view'      => $LNG['log_view'],
    ]);
    $template->show("LogList.tpl");
}

function ShowLogPresent()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $result    = $GLOBALS['DATABASE']->query("SELECT DISTINCT l.id, l.admin, l.target, l.time, l.universe, u_a.username"
        . " as admin_username FROM " . LOG . " as l LEFT JOIN " . USERS . " as u_a ON u_a.id = l.admin WHERE mode = 4"
        . " ORDER BY l.id DESC;");

    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    if (empty($result)) {
        $template->message($LNG['log_no_data'], '?page=log');
        exit;
    }

    while ($LogRow = $GLOBALS['DATABASE']->fetch_array($result)) {
        $LogArray[] = [
            'id'        => $LogRow['id'],
            'admin'     => $LogRow['admin_username'],
            'target_uni' => $LogRow['universe'],
            'target'    => $LNG['fcm_universe'],
            'time'      => _date($LNG['php_tdformat'], $LogRow['time'], $USER['timezone']),
        ];
    }
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $GLOBALS['DATABASE']->free_result($result);
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'log_log'       => $LNG['log_log'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_uni'       => $LNG['log_uni_short'],
        'log_target'    => $LNG['log_target_user'],
        'log_id'        => $LNG['log_id'],
        'log_view'      => $LNG['log_view'],
    ]);
    $template->show("LogList.tpl");
}

function ShowLogAlliance()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $sql = "SELECT DISTINCT l.`id`, u_a.`username` AS admin_username, l.`universe`, l.`time`,
        CONCAT(a.`id`, ' - ', a.`ally_name`, ' [', a.`ally_tag`, ']') AS alliance
        FROM %%LOG%% AS l
        LEFT JOIN %%USERS%% AS u_a ON u_a.`id` = l.`admin`
        LEFT JOIN %%ALLIANCE%% AS a ON a.`id` = l.`target`
        WHERE mode = 6
        ORDER BY l.`id` DESC;";
    $result = Database::get()->select($sql);

    $template   = new template();
    $template->assign_vars([
        'signalColors' => $USER['signalColors']
    ]);
    if (empty($result)) {
        $template->message($LNG['log_no_data'], '?page=log');
        exit;
    }

    foreach ($result as $LogRow) {
        $LogArray[] = [
            'id'        => $LogRow['id'],
            'admin'     => $LogRow['admin_username'],
            'target_uni' => $LogRow['universe'],
            'target'    => $LogRow['alliance'],
            'time'      => _date($LNG['php_tdformat'], $LogRow['time'], $USER['timezone']),
        ];
    }
    if (!isset($LogArray)) {
        $LogArray = [];
    }
    $template->assign_vars([
        'LogArray'      => $LogArray,
        'log_log'       => $LNG['log_log'],
        'log_admin'     => $LNG['log_admin'],
        'log_time'      => $LNG['log_time'],
        'log_uni'       => $LNG['log_uni_short'],
        'log_target'    => $LNG['log_target_alliance'],
        'log_id'        => $LNG['log_id'],
        'log_view'      => $LNG['log_view'],
    ]);
    $template->show("LogList.tpl");
}
