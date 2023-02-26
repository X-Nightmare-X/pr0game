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

class ShowMessagesPage extends AbstractGamePage
{
    public static $requireModule = MODULE_MESSAGES;

    public function __construct()
    {
        parent::__construct();
    }

    // function view()
    // {
        // global $LNG, $USER;
        // $MessCategory  	= HTTP::_GP('messcat', 100);
        // $page  			= HTTP::_GP('site', 1);

        // $db = Database::get();

        // $this->initTemplate();
        // $this->setWindow('ajax');

        // $MessageList	= array();
        // $MessagesID		= array();

        // if($MessCategory == 999)  {

            // $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_sender = :userId AND message_type != 50 AND message_deleted IS NULL;";
            // $MessageCount = $db->selectSingle($sql, array(
                // ':userId'   => $USER['id'],
            // ), 'state');

            // $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
            // $page		= max(1, min($page, $maxPage));

            // $sql = "SELECT message_id, message_time, CONCAT(username, ' [',galaxy, ':', system, ':', planet,']') as message_from, message_subject, message_sender, message_type, message_unread, message_text
            // FROM %%MESSAGES%% INNER JOIN %%USERS%% ON id = message_owner
            // WHERE message_sender = :userId AND message_type != 50 AND message_deleted IS NULL
            // ORDER BY message_time DESC
            // LIMIT :offset, :limit;";

            // $MessageResult = $db->select($sql, array(
                // ':userId'   => $USER['id'],
                // ':offset'   => (($page - 1) * MESSAGES_PER_PAGE),
                // ':limit'    => MESSAGES_PER_PAGE
            // ));
        // }
        // else
        // {
            // if ($MessCategory == 100)
            // {
                // $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_owner = :userId AND message_deleted IS NULL;";
                // $MessageCount = $db->selectSingle($sql, array(
                    // ':userId'   => $USER['id'],
                // ), 'state');

                // $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
                // $page		= max(1, min($page, $maxPage));

                // $sql = "SELECT message_id, message_time, message_from, message_subject, message_sender, message_type, message_unread, message_text
                           // FROM %%MESSAGES%%
                           // WHERE message_owner = :userId AND message_deleted IS NULL
                           // ORDER BY message_time DESC
                           // LIMIT :offset, :limit";

                // $MessageResult = $db->select($sql, array(
                    // ':userId'       => $USER['id'],
                    // ':offset'       => (($page - 1) * MESSAGES_PER_PAGE),
                    // ':limit'        => MESSAGES_PER_PAGE
                // ));
            // }
            // else
            // {
                // $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_owner = :userId AND message_type = :messCategory AND message_deleted IS NULL;";

                // $MessageCount = $db->selectSingle($sql, array(
                    // ':userId'       => $USER['id'],
                    // ':messCategory' => $MessCategory
                // ), 'state');

                // $sql = "SELECT message_id, message_time, message_from, message_subject, message_sender, message_type, message_unread, message_text
                           // FROM %%MESSAGES%%
                           // WHERE message_owner = :userId AND message_type = :messCategory AND message_deleted IS NULL
                           // ORDER BY message_time DESC
                           // LIMIT :offset, :limit";

                // $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
                // $page		= max(1, min($page, $maxPage));

                // $MessageResult = $db->select($sql, array(
                    // ':userId'       => $USER['id'],
                    // ':messCategory' => $MessCategory,
                    // ':offset'       => (($page - 1) * MESSAGES_PER_PAGE),
                    // ':limit'        => MESSAGES_PER_PAGE
                // ));
            // }
        // }

        // foreach ($MessageResult as $MessageRow)
        // {
            // $MessagesID[]	= $MessageRow['message_id'];

            // $MessageList[]	= array(
                // 'id'		=> $MessageRow['message_id'],
                // 'time'		=> _date($LNG['php_tdformat'], $MessageRow['message_time'], $USER['timezone']),
                // 'from'		=> $MessageRow['message_from'],
                // 'subject'	=> $MessageRow['message_subject'],
                // 'sender'	=> $MessageRow['message_sender'],
                // 'type'		=> $MessageRow['message_type'],
                // 'unread'	=> $MessageRow['message_unread'],
                // 'text'		=> $MessageRow['message_text'],
            // );
        // }

        // if(!empty($MessagesID) && $MessCategory != 999) {
            // $sql = 'UPDATE %%MESSAGES%% SET message_unread = 0 WHERE message_id IN ('.implode(',', $MessagesID).') AND message_owner = :userID;';
            // $db->update($sql, array(
                // ':userID'       => $USER['id'],
            // ));
        // }

        // $this->assign(array(
            // 'MessID'		=> $MessCategory,
            // 'MessageCount'	=> $MessageCount,
            // 'MessageList'	=> $MessageList,
            // 'page'			=> $page,
            // 'maxPage'		=> $maxPage,
        // ));

        // $this->display('page.messages.view.tpl');
    // }

    private function sendData($Code, $Message)
    {
        $this->returnData['code']	= $Code;
        $this->returnData['mess']	= $Message;
        $this->sendJSON($this->returnData);
    }

    public function deleteMessage()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $db = Database::get();

        $this->initTemplate();
        $this->setWindow('ajax');

        $delMessID		= HTTP::_GP('delMessID', 0);

        if (empty($delMessID)) {
            $this->sendData(0, $LNG['error']);
        }


        $sql = 'DELETE FROM %%MESSAGES%% WHERE message_id = :messID AND message_owner = :userId;';

        $db->delete($sql, [
            ':userId' => $USER['id'],
            ':messID' => $delMessID,
        ]);

        $this->sendData($delMessID, $LNG['mg_deleted']);
    }

    public function action()
    {
        $USER =& Singleton()->USER;

        $db = Database::get();

        $MessCategory  	= HTTP::_GP('messcat', 100);
        $page		 	= HTTP::_GP('side', 1);
        $messageIDs		= HTTP::_GP('messageID', []);
        $action	= HTTP::_GP('action', "");
        $redirectUrl	= 'game.php?page=messages&category='.$MessCategory.'&side='.$page;

        if ($action == "") {
            $this->redirectTo($redirectUrl);
        }

        if ($action == 'deleteunmarked' && empty($messageIDs)) {
            $action	= 'deletetypeall';
        }

        if ($action == 'deletetypeall' && $MessCategory == 100) {
            $action	= 'deleteall';
        }

        if ($action == 'readtypeall' && $MessCategory == 100) {
            $action	= 'readall';
        }

        switch($action) {
            case 'readall':
                $sql = "UPDATE %%MESSAGES%% SET message_unread = 0 WHERE message_owner = :userID;";
                $db->update($sql, [
                    ':userID'   => $USER['id']
                ]);
                break;
            case 'readtypeall':
                $sql = "UPDATE %%MESSAGES%% SET message_unread = 0 WHERE message_owner = :userID AND message_type = :messCategory;";
                $db->update($sql, [
                    ':userID'       => $USER['id'],
                    ':messCategory' => $MessCategory
                ]);
                break;
            case 'readmarked':
                if (empty($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                $messageIDs	= array_filter($messageIDs, 'is_numeric');

                if (empty($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                $sql = 'UPDATE %%MESSAGES%% SET message_unread = 0 WHERE message_id IN ('.implode(',', array_keys($messageIDs)).') AND message_owner = :userID;';
                $db->update($sql, [
                    ':userID'       => $USER['id'],
                ]);
                break;
            case 'deleteall':
                if (Config::get()->message_delete_behavior == 1) {
                    $sql = "UPDATE %%MESSAGES%% SET message_deleted = :timestamp WHERE message_owner = :userID;";
                    $db->update($sql, [
                        ':timestamp'    => TIMESTAMP,
                        ':userID'       => $USER['id']
                    ]);
                } else {
                    $sql = "DELETE FROM %%MESSAGES%% WHERE message_owner = :userID;";
                    $db->delete($sql, [
                        ':userID'       => $USER['id']
                    ]);
                }
                break;
            case 'deletetypeall':
                if (Config::get()->message_delete_behavior == 1) {
                    $sql = "UPDATE %%MESSAGES%% SET message_deleted = :timestamp WHERE message_owner = :userID AND message_type = :messCategory;";
                    $db->update($sql, [
                        ':timestamp' => TIMESTAMP,
                        ':userID' => $USER['id'],
                        ':messCategory' => $MessCategory
                    ]);
                } else {
                    $sql = "DELETE FROM %%MESSAGES%% WHERE message_owner = :userID AND message_type = :messCategory;";
                    $db->delete($sql, [
                        ':userID' => $USER['id'],
                        ':messCategory' => $MessCategory
                    ]);
                }
                break;
            case 'deletemarked':
                if (empty($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                $messageIDs	= array_filter($messageIDs, 'is_numeric');

                if (empty($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                if (Config::get()->message_delete_behavior == 1) {
                    $sql = 'UPDATE %%MESSAGES%% SET message_deleted = :timestamp WHERE message_id IN (' . implode(',', array_keys($messageIDs)) . ') AND message_owner = :userId;';
                    $db->update($sql, [
                        ':timestamp' => TIMESTAMP,
                        ':userId' => $USER['id'],
                    ]);
                } else {
                    $sql = 'DELETE FROM %%MESSAGES%% WHERE message_id IN (' . implode(',', array_keys($messageIDs)) . ') AND message_owner = :userId;';
                    $db->delete($sql, [
                        ':userId' => $USER['id'],
                    ]);
                }
                break;
            case 'deleteunmarked':
                if (empty($messageIDs) || !is_array($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                $messageIDs	= array_filter($messageIDs, 'is_numeric');

                if (empty($messageIDs)) {
                    $this->redirectTo($redirectUrl);
                }

                if (Config::get()->message_delete_behavior == 1) {
                    $sql = 'UPDATE %%MESSAGES%% SET message_deleted = :timestamp WHERE message_id NOT IN (' . implode(',', array_keys($messageIDs)) . ') AND message_owner = :userId;';
                    $db->update($sql, [
                        ':timestamp' => TIMESTAMP,
                        ':userId' => $USER['id'],
                    ]);
                } else {
                    $sql = 'DELETE FROM %%MESSAGES%% WHERE message_id NOT IN ('.implode(',', array_keys($messageIDs)).') AND message_owner = :userId;';
                    $db->delete($sql, [
                        ':userId'       => $USER['id'],
                    ]);
                }
                break;
        }
        $this->redirectTo($redirectUrl);
    }

    public function send()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $receiverID	= HTTP::_GP('id', 0);
        $subject 	= HTTP::_GP('subject', $LNG['mg_no_subject'], true);
        $text		= HTTP::_GP('text', '', true);
        $senderName	= $USER['username'].' ['.$USER['galaxy'].':'.$USER['system'].':'.$USER['planet'].']';

        $text		= makebr($text);

        $session	= Session::load();

        if (empty($receiverID) || empty($text) || !isset($session->messageToken) || $session->messageToken != md5($USER['id'].'|'.$receiverID)) {
            $this->sendJSON($LNG['mg_error']);
        }

        $session->messageToken = null;

        PlayerUtil::sendMessage($receiverID, $USER['id'], $senderName, 1, $subject, $text, TIMESTAMP);
        $this->sendJSON($LNG['mg_message_send']);
    }

    public function write()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $this->setWindow('popup');
        $this->initTemplate();

        $db = Database::get();

        $receiverID       	= HTTP::_GP('id', 0);
        $Subject 			= HTTP::_GP('subject', $LNG['mg_no_subject'], true);

        $sql = "SELECT a.galaxy, a.system, a.planet, b.username, b.id_planet, b.settings_blockPM
        FROM %%PLANETS%% as a, %%USERS%% as b WHERE b.id = :receiverId AND a.id = b.id_planet;";

        $receiverRecord = $db->selectSingle($sql, [
            ':receiverId'   => $receiverID
        ]);

        if (!$receiverRecord) {
            $this->printMessage($LNG['mg_error']);
        }

        if ($receiverRecord['settings_blockPM'] == 1) {
            $this->printMessage($LNG['mg_receiver_block_pm']);
        }

        Session::load()->messageToken = md5($USER['id'].'|'.$receiverID);

        $this->assign([
            'subject'		=> $Subject,
            'id'			=> $receiverID,
            'OwnerRecord'	=> $receiverRecord,
        ]);

        $this->display('page.messages.write.tpl');
    }

    public function show()
    {
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;
        $MessCategory      	= HTTP::_GP('category', -1);
        $page			= HTTP::_GP('side', 1);

        $db = Database::get();

        require_once 'includes/classes/Config.class.php';
        $fleetIntoDebris = Config::get()->Fleet_Cdr;
        $defIntoDebris = Config::get()->Defs_Cdr;

        $stbSettings2 = PlayerUtil::player_stb_settings($USER);

        $combustion_tech = $USER["combustion_tech"];
        $impulse_motor_tech = $USER["impulse_motor_tech"];

        $MessageList	= [];
        $MessagesID		= [];

        $TitleColor    	= [ 0 => '#FFFF00', 1 => '#FF6699', 2 => '#FF3300', 3 => '#FF9900', 4 => '#773399', 5 => '#009933', 15 => '#6495ed', 50 => '#666600', 99 => '#007070', 100 => '#ABABAB', 999 => '#CCCCCC'];

        $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_sender = :userID AND message_type != 50;";
        $MessOut = $db->selectSingle($sql, [
            ':userID'   => $USER['id']
        ], 'state');

        $Total			= [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 15 => 0, 50 => 0, 99 => 0, 100 => 0, 999 => 0];
        $UnRead			= [0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 15 => 0, 50 => 0, 99 => 0, 100 => 0, 999 => 0];

        $sql = "SELECT message_type, SUM(message_unread) as message_unread, COUNT(*) as count FROM %%MESSAGES%% WHERE message_owner = :userID AND message_deleted IS NULL GROUP BY message_type;";
        $CategoryResult = $db->select($sql, [
            ':userID'   => $USER['id']
        ]);

        foreach ($CategoryResult as $CategoryRow) {
            $UnRead[$CategoryRow['message_type']]	= $CategoryRow['message_unread'];
            $Total[$CategoryRow['message_type']]	= $CategoryRow['count'];
        }

        $UnRead[100]	= array_sum($UnRead);
        $Total[100]		= array_sum($Total);
        $Total[999]		= $MessOut;

        $CategoryList        = [];

        foreach ($TitleColor as $CategoryID => $CategoryColor) {
            $CategoryList[$CategoryID]	= [
                'color'		=> $CategoryColor,
                'unread'	=> $UnRead[$CategoryID],
                'total'		=> $Total[$CategoryID],
            ];
        }

        //// view()

        if ($MessCategory == 999) {
            $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_sender = :userId AND message_type != 50 AND message_deleted IS NULL;";
            $MessageCount = $db->selectSingle($sql, [
                ':userId'   => $USER['id'],
            ], 'state');

            $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
            $page		= max(1, min($page, $maxPage));

            $sql = "SELECT message_id, message_time, CONCAT(username, ' [',galaxy, ':', system, ':', planet,']') as message_from, message_subject, message_sender, message_type, message_unread, message_text
			FROM %%MESSAGES%% INNER JOIN %%USERS%% ON id = message_owner
			WHERE message_sender = :userId AND message_type != 50 AND message_deleted IS NULL
			ORDER BY message_time DESC, message_id
			LIMIT :offset, :limit;";

            $MessageResult = $db->select($sql, [
                ':userId'   => $USER['id'],
                ':offset'   => (($page - 1) * MESSAGES_PER_PAGE),
                ':limit'    => MESSAGES_PER_PAGE
            ]);
        } else {
            if ($MessCategory == 100) {
                $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_owner = :userId AND message_deleted IS NULL;";
                $MessageCount = $db->selectSingle($sql, [
                    ':userId'   => $USER['id'],
                ], 'state');

                $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
                $page		= max(1, min($page, $maxPage));

                $sql = "SELECT message_id, message_time, message_from, message_subject, message_sender, message_type, message_unread, message_text
                           FROM %%MESSAGES%%
                           WHERE message_owner = :userId AND message_deleted IS NULL
                           ORDER BY message_time DESC, message_id
                           LIMIT :offset, :limit";

                $MessageResult = $db->select($sql, [
                    ':userId'       => $USER['id'],
                    ':offset'       => (($page - 1) * MESSAGES_PER_PAGE),
                    ':limit'        => MESSAGES_PER_PAGE
                ]);
            } else {
                $sql = "SELECT COUNT(*) as state FROM %%MESSAGES%% WHERE message_owner = :userId AND message_type = :messCategory AND message_deleted IS NULL;";

                $MessageCount = $db->selectSingle($sql, [
                    ':userId'       => $USER['id'],
                    ':messCategory' => $MessCategory
                ], 'state');

                $sql = "SELECT message_id, message_time, message_from, message_subject, message_sender, message_type, message_unread, message_text
                           FROM %%MESSAGES%%
                           WHERE message_owner = :userId AND message_type = :messCategory AND message_deleted IS NULL
                           ORDER BY message_time DESC, message_id
                           LIMIT :offset, :limit";

                $maxPage	= max(1, ceil($MessageCount / MESSAGES_PER_PAGE));
                $page		= max(1, min($page, $maxPage));

                $MessageResult = $db->select($sql, [
                    ':userId'       => $USER['id'],
                    ':messCategory' => $MessCategory,
                    ':offset'       => (($page - 1) * MESSAGES_PER_PAGE),
                    ':limit'        => MESSAGES_PER_PAGE
                ]);
            }
        }

        foreach ($MessageResult as $MessageRow) {
            $MessagesID[]	= $MessageRow['message_id'];

            $MessageList[]	= [
                'id'		=> $MessageRow['message_id'],
                'time'		=> _date($LNG['php_tdformat'], $MessageRow['message_time'], $USER['timezone']),
                'from'		=> $MessageRow['message_from'],
                'subject'	=> $MessageRow['message_subject'],
                'sender'	=> $MessageRow['message_sender'],
                'type'		=> $MessageRow['message_type'],
                'unread'	=> $MessageRow['message_unread'],
                'text'		=> $MessageRow['message_text'],
            ];
        }

        if (!empty($MessagesID) && $MessCategory != 999) {
            $sql = 'UPDATE %%MESSAGES%% SET message_unread = 0 WHERE message_id IN ('.implode(',', $MessagesID).') AND message_owner = :userID;';
            $db->update($sql, [
                ':userID'       => $USER['id'],
            ]);
        }

        require_once 'includes/classes/class.MarketManager.php';
        $pMarket = new MarketManager();
        $refrates = $pMarket->getReferenceRatios();
        $this->tplObj->loadscript('message.js');
        $this->assign([
            'MessID'		            => $MessCategory,
            'MessageCount'	            => $MessageCount,
            'MessageList'	            => $MessageList,
            'CategoryList'	            => $CategoryList,
            'page'			            => $page,
            'maxPage'		            => $maxPage,
            'stb_big_time'              => $stbSettings2['stb_big_time'],
            'stb_med_time'              => $stbSettings2['stb_med_time'],
            'stb_small_time'            => $stbSettings2['stb_small_time'],
            'stb_big_ress'              => $stbSettings2['stb_big_ress'],
            'stb_med_ress'              => $stbSettings2['stb_med_ress'],
            'stb_small_ress'            => $stbSettings2['stb_small_ress'],
            'refratesMetal'             => $refrates['metal'],
            'refratesCrystal'           => $refrates['crystal'],
            'refratesDeuterium'         => $refrates['deuterium'],
            'impulse_motor_tech'        => $impulse_motor_tech,
            'combustion_tech'           => $combustion_tech,
            'fleetIntoDebris'           => $fleetIntoDebris,
            'defIntoDebris'             => $defIntoDebris,
        ]);

        $this->display('page.messages.default.tpl');
    }
}
