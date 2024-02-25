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

function ShowMultiIPPage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $db = Database::get();
    if (isset($_GET['action'])) {
        $id = (int) $_GET['id'];
        $allowed = $_GET['action'] === 'known' ? 1 : 0;

        if (!isset($_GET['userID'])) {
            $sql = "SELECT * FROM %%MULTI%% WHERE `multiID` = :multiID;";
            $old = $db->selectSingle($sql, [':multiID' => $id]);

            $sql = "UPDATE %%MULTI%% SET `allowed` = :allowed WHERE `multiID` = :multiID;";
            $db->update($sql, [
                ':multiID' => $id,
                ':allowed' => $allowed,
            ]);
        } else {
            $userID = (int) $_GET['userID'];
            $sql = "SELECT * FROM %%MULTI_TO_USERS%% WHERE `multiID` = :multiID AND userID = :userID;";
            $old = $db->selectSingle($sql, [
                ':multiID' => $id,
                ':userID' => $userID,
            ]);

            $sql = "UPDATE %%MULTI_TO_USERS%% SET `allowed` = :allowed WHERE `multiID` = :multiID AND userID = :userID;";
            $db->update($sql, [
                ':multiID' => $id,
                ':userID' => $userID,
                ':allowed' => $allowed,
            ]);
        }

        $new = $old;
        $new['allowed'] = $allowed;

        $LOG = new Log(7);
        $LOG->target = $id;
        $LOG->old = $old;
        $LOG->new = $new;
        $LOG->save();

        HTTP::redirectTo("admin.php?page=multiips");
    }

    $sql = "SELECT * FROM %%MULTI%% WHERE universe = :universe ORDER BY `lastActivity`";
    $multis = $db->select($sql, [':universe' => Universe::getEmulated()]);
    $IPs	= [];
    foreach ($multis as $multiEntry) {
        $sql = "SELECT u.`id`, u.`username`, u.`email`, u.`register_time`, u.`onlinetime`, u.`user_lastip`, mu.`lastActivity`, mu.`allowed`
            FROM %%USERS%% AS u
            JOIN %%MULTI_TO_USERS%% AS mu ON mu.userID = u.id
            WHERE mu.`multiID` = :multiID;";
        $multiUsers = $db->select($sql, [
            ':multiID' => $multiEntry['multiID'],
        ]);

        if (empty($multiUsers)) {
            continue;
        }

        $hide = $multiEntry['allowed'] == 1;
        foreach ($multiUsers as $multiUser) {
            $hide = $hide && $multiUser['allowed'] == 1;
        }
        if ($hide) {
            // continue; TODO: Add hide to IPs[] to filter in tpl.
        }

        $IPs[$multiEntry['multiID']] = [];
        $IPs[$multiEntry['multiID']]['multi_ip'] = $multiEntry['multi_ip'];
        $IPs[$multiEntry['multiID']]['lastActivity'] = _date($LNG['php_tdformat'], $multiEntry['lastActivity']);
        $IPs[$multiEntry['multiID']]['allowed'] = $multiEntry['allowed'];
        $IPs[$multiEntry['multiID']]['users'] = [];

        foreach ($multiUsers as $multiUser) {
            $multiUser['register_time']	= _date($LNG['php_tdformat'], $multiUser['register_time']);
            $multiUser['onlinetime'] = _date($LNG['php_tdformat'], $multiUser['onlinetime']);
            $multiUser['lastActivity'] = _date($LNG['php_tdformat'], $multiUser['lastActivity']);

            $IPs[$multiEntry['multiID']]['users'][$multiUser['id']]	= $multiUser;
        }
    }

    $template	= new template();
    $template->assign_vars([
        'multiGroups'	=> $IPs,
        'signalColors'	=> $USER['signalColors'],
    ]);
    $template->show('MultiIPs.tpl');
}
