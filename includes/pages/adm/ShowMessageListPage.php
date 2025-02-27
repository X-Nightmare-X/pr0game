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

function ShowMessageListPage()
{
    $LNG =& Singleton()->LNG;
    $USER =& Singleton()->USER;
    $page		= HTTP::_GP('side', 1);
    $type		= HTTP::_GP('type', 100);
    $sender		= HTTP::_GP('sender', '', UTF8_SUPPORT);
    $receiver	= HTTP::_GP('receiver', '', UTF8_SUPPORT);
    $dateStart	= HTTP::_GP('dateStart', []);
    $dateEnd	= HTTP::_GP('dateEnd', []);

    $perSide	= 50;

    $messageList	= [];
    $userWhereSQL	= '';
    $dateWhereSQL	= '';
    $countJoinSQL	= '';

    $categories	= $LNG['mg_type'];
    unset($categories[999]);

    $dateStart	= array_filter($dateStart, 'is_numeric');
    $dateEnd	= array_filter($dateEnd, 'is_numeric');

    $useDateStart	= count($dateStart) == 3;
    $useDateEnd		= count($dateEnd) == 3;

    if ($useDateStart && $useDateEnd) {
        $dateWhereSQL	= ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']).' AND '.mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']);
    } elseif ($useDateStart) {
        $dateWhereSQL	= ' AND message_time > '.mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']);
    } elseif ($useDateStart) {
        $dateWhereSQL	= ' AND message_time < '.mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']);
    }

    if (!empty($sender)) {
        $countJoinSQL	.= ' LEFT JOIN '.USERS.' as us ON message_sender = us.id';
        $userWhereSQL	.= ' AND us.username = "'.$GLOBALS['DATABASE']->escape($sender).'"';
    }

    if (!empty($receiver)) {
        $countJoinSQL	.= ' LEFT JOIN '.USERS.' as u ON message_owner = u.id';
        $userWhereSQL	.= ' AND u.username = "'.$GLOBALS['DATABASE']->escape($receiver).'"';
    }

    if ($type != 100) {
        $MessageCount	= $GLOBALS['DATABASE']->getFirstCell("SELECT COUNT(*) FROM ".MESSAGES.$countJoinSQL." WHERE message_type = ".$type." AND message_universe = ".Universe::getEmulated().$dateWhereSQL.$userWhereSQL.";");
    } else {
        $MessageCount	= $GLOBALS['DATABASE']->getFirstCell("SELECT COUNT(*) FROM ".MESSAGES.$countJoinSQL." WHERE message_universe = ".Universe::getEmulated().$dateWhereSQL.$userWhereSQL.";");
    }

    $maxPage	= max(1, ceil($MessageCount / $perSide));
    $page		= max(1, min($page, $maxPage));

    $sqlLimit	= (($page - 1) * $perSide).", ".($perSide - 1);

    if ($type == 100) {
        $messageRaw	= $GLOBALS['DATABASE']->query("SELECT u.username, us.username as senderName, m.* 
		FROM ".MESSAGES." as m 
		LEFT JOIN ".USERS." as u ON m.message_owner = u.id 
		LEFT JOIN ".USERS." as us ON m.message_sender = us.id
		WHERE m.message_universe = ".Universe::getEmulated()."
		".$dateWhereSQL."
		".$userWhereSQL."
		ORDER BY message_time DESC, message_id DESC
		LIMIT ".$sqlLimit.";");
    } else {
        $messageRaw	= $GLOBALS['DATABASE']->query("SELECT u.username, us.username as senderName, m.* 
		FROM ".MESSAGES." as m
		LEFT JOIN ".USERS." as u ON m.message_owner = u.id
		LEFT JOIN ".USERS." as us ON m.message_sender = us.id
		WHERE m.message_type = ".$type." AND message_universe = ".Universe::getEmulated()."
		".$dateWhereSQL."
		".$userWhereSQL."
		ORDER BY message_time DESC, message_id DESC
		LIMIT ".$sqlLimit.";");
    }

    while ($messageRow = $GLOBALS['DATABASE']->fetch_array($messageRaw)) {
        $messageList[$messageRow['message_id']]	= [
            'sender'	=> empty($messageRow['senderName']) ? $messageRow['message_from'] : $messageRow['senderName'].' (ID:&nbsp;'.$messageRow['message_sender'].')',
            'receiver'	=> $messageRow['username'].' (ID:&nbsp;'.$messageRow['message_owner'].')',
            'subject'	=> $messageRow['message_subject'],
            'text'		=> $messageRow['message_text'],
            'type'		=> $messageRow['message_type'],
            'deleted'	=> $messageRow['message_deleted'] != null,
            'time'		=> str_replace(' ', '&nbsp;', _date($LNG['php_tdformat'], $messageRow['message_time']), $USER['timezone']),
        ];
    }

    $template 	= new template();

    $template->assign_vars([
        'categories'	=> $categories,
        'maxPage'		=> $maxPage,
        'page'			=> $page,
        'messageList'	=> $messageList,
        'type'			=> $type,
        'dateStart'		=> $dateStart,
        'dateEnd'		=> $dateEnd,
        'sender'		=> $sender,
        'receiver'		=> $receiver,
        'Selected'		=> 0,
        'signalColors'	=> $USER['signalColors']
    ]);

    $template->show('MessageList.tpl');
}
