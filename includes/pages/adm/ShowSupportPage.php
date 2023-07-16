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

class ShowSupportPage
{
    private $ticketObj;

    public function __construct()
    {
        require('includes/classes/class.SupportTickets.php');
        $this->ticketObj	= new SupportTickets();
        $this->tplObj		= new template();
        // 2Moons 1.7TO1.6 PageClass Wrapper
        $ACTION = HTTP::_GP('mode', 'show');
        if (is_callable([$this, $ACTION])) {
            $this->{$ACTION}();
        } else {
            $this->show();
        }
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $ticketResult	= $GLOBALS['DATABASE']->query("SELECT t.*, u.username, COUNT(a.ticketID) as answer FROM ".TICKETS." t INNER JOIN ".TICKETS_ANSWER." a USING (ticketID) INNER JOIN ".USERS." u ON u.id = t.ownerID WHERE t.universe = ".Universe::getEmulated()." GROUP BY a.ticketID ORDER BY t.ticketID DESC;");
        $ticketList		= [];

        while ($ticketRow = $GLOBALS['DATABASE']->fetch_array($ticketResult)) {
            $ticketRow['time']	= _date($LNG['php_tdformat'], $ticketRow['time'], $USER['timezone']);

            $ticketList[$ticketRow['ticketID']]	= $ticketRow;
        }

        $GLOBALS['DATABASE']->free_result($ticketResult);

        $this->tplObj->assign_vars([
            'ticketList'	=> $ticketList,
            'signalColors'	=> $USER['signalColors'],
        ]);

        $this->tplObj->show('page.ticket.default.tpl');
    }

    public function send()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $ticketID	= HTTP::_GP('id', 0);
        $message	= HTTP::_GP('message', '', true);
        $change		= HTTP::_GP('change_status', 0);

        $ticketDetail	= $GLOBALS['DATABASE']->getFirstRow("SELECT ownerID, subject, status FROM ".TICKETS." WHERE ticketID = ".$ticketID.";");

        $status = ($change ? ($ticketDetail['status'] <= 1 ? 2 : 1) : 1);


        if (!$change && empty($message)) {
            HTTP::redirectTo('admin.php?page=support&mode=view&id='.$ticketID);
        }

        $subject		= "RE: ".$ticketDetail['subject'];

        if ($change && $status == 1) {
            $this->ticketObj->createAnswer($ticketID, $USER['id'], $USER['username'], $subject, $LNG['ti_admin_open'], $status);
        }

        if (!empty($message)) {
            $this->ticketObj->createAnswer($ticketID, $USER['id'], $USER['username'], $subject, $message, $status);
        }

        if ($change && $status == 2) {
            $this->ticketObj->createAnswer($ticketID, $USER['id'], $USER['username'], $subject, $LNG['ti_admin_close'], $status);
        }


        $subject	= sprintf($LNG['sp_answer_message_title'], $ticketID);
        $text		= sprintf($LNG['sp_answer_message'], $ticketID);

        PlayerUtil::sendMessage(
            $ticketDetail['ownerID'],
            $USER['id'],
            $USER['username'],
            4,
            $subject,
            $text,
            TIMESTAMP,
            null,
            1,
            Universe::getEmulated()
        );

        HTTP::redirectTo('admin.php?page=support');
    }

    public function view()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $ticketID			= HTTP::_GP('id', 0);
        $answerResult		= $GLOBALS['DATABASE']->query("SELECT a.*, t.categoryID, t.status FROM ".TICKETS_ANSWER." a INNER JOIN ".TICKETS." t USING(ticketID) WHERE a.ticketID = ".$ticketID." ORDER BY a.answerID;");
        $answerList			= [];

        $ticket_status		= 0;

        while ($answerRow = $GLOBALS['DATABASE']->fetch_array($answerResult)) {
            if (empty($ticket_status)) {
                $ticket_status = $answerRow['status'];
            }

            $answerRow['time']	= _date($LNG['php_tdformat'], $answerRow['time'], $USER['timezone']);
            $answerRow['message'] = nl2br($answerRow['message']);

            $answerList[$answerRow['answerID']]	= $answerRow;
        }

        $GLOBALS['DATABASE']->free_result($answerResult);

        $categoryList	= $this->ticketObj->getCategoryList();

        $this->tplObj->assign_vars([
            'ticketID'		=> $ticketID,
            'ticket_status' => $ticket_status,
            'categoryList'	=> $categoryList,
            'answerList'	=> $answerList,
            'signalColors'	=> $USER['signalColors'],
        ]);

        $this->tplObj->show('page.ticket.view.tpl');
    }
}
