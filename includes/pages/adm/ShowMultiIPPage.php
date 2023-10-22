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

        $sql = "SELECT * FROM %%MULTI%% WHERE `multiID` = :multiID;";
        $old = $db->selectSingle($sql, [':multiID' => $id]);

        $sql = "UPDATE %%MULTI%% SET `allowed` = :allowed WHERE `multiID` = :multiID;";
        $db->update($sql, [
            ':multiID' => $id,
            ':allowed' => $allowed,
        ]);

        $new = $old;
        $new['allowed'] = $allowed;

        $LOG = new Log(7);
        $LOG->target = $id;
        $LOG->old = $old;
        $LOG->new = $new;
        $LOG->save();

        HTTP::redirectTo("admin.php?page=multiips");
    }

    $sql = "SELECT * FROM %%MULTI%% ORDER BY `multi_ip`, `lastActivity`";
    $multis = $db->select($sql);
    $IPs	= [];
    foreach ($multis as $multiEntry) {
        $sql = "SELECT `id`, `username`, `email`, `register_time`, `onlinetime`, `user_lastip`
            FROM %%USERS%%
            WHERE `id` = :user1 OR `id` = :user2";
        $multiUsers = $db->select($sql, [
            ':user1' => $multiEntry['userID_1'],
            ':user2' => $multiEntry['userID_2'],
        ]);

        if (!isset($IPs[$multiEntry['multiID']])) {
            $IPs[$multiEntry['multiID']] = [];
        }

        $IPs[$multiEntry['multiID']]['multi_ip'] = $multiEntry['multi_ip'];
        $IPs[$multiEntry['multiID']]['lastActivity'] = _date($LNG['php_tdformat'], $multiEntry['lastActivity']);
        $IPs[$multiEntry['multiID']]['isKnown'] = $multiEntry['allowed'];

        foreach ($multiUsers as $multiUser) {

            $multiUser['register_time']	= _date($LNG['php_tdformat'], $multiUser['register_time']);
            $multiUser['onlinetime'] = _date($LNG['php_tdformat'], $multiUser['onlinetime']);

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
