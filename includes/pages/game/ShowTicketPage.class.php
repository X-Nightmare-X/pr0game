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

class ShowTicketPage extends AbstractGamePage
{
    public static $requireModule = MODULE_SUPPORT;

    private $ticketObj;

    public function __construct()
    {
        parent::__construct();
        require('includes/classes/class.SupportTickets.php');
        $this->ticketObj = new SupportTickets();
    }

    public function show()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $db = Database::get();

        $sql = 'SELECT t.*, COUNT(a.`ticketID`) as answer
            FROM %%TICKETS%% t
            INNER JOIN %%TICKETS_ANSWER%% a USING (`ticketID`)
            WHERE t.`ownerID` = :userID 
            GROUP BY a.`ticketID` 
            ORDER BY t.`ticketID` DESC;';

        $ticketResult = $db->select($sql, [
            ':userID' => $USER['id']
        ]);

        $ticketList = [];

        foreach ($ticketResult as $ticketRow) {
            $ticketRow['time'] = _date($LNG['php_tdformat'], $ticketRow['time'], $USER['timezone']);

            $ticketList[$ticketRow['ticketID']] = $ticketRow;
        }

        $this->assign([
            'ticketList' => $ticketList
        ]);

        $this->display('page.ticket.default.tpl');
    }

    public function create()
    {
        $categoryList = $this->ticketObj->getCategoryList();
        // Remove debug category for users to choose from
        unset($categoryList[9]);

        $this->assign([
            'categoryList' => $categoryList,
        ]);

        $this->display('page.ticket.create.tpl');
    }

    public function send()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;
        $config = Config::get();
        $ticketID = HTTP::_GP('id', 0);
        $categoryID = HTTP::_GP('category', 0);
        $message = HTTP::_GP('message', '', true);
        $subject = HTTP::_GP('subject', '', true);

        if (empty($message)) {
            if (empty($ticketID)) {
                $this->redirectTo('game.php?page=ticket&mode=create');
            } else {
                $this->redirectTo('game.php?page=ticket&mode=view&id=' . $ticketID);
            }
        }

        if (empty($ticketID)) {
            if (empty($subject)) {
                $this->printMessage($LNG['ti_error_no_subject'], [[
                    'label' => $LNG['sys_back'],
                    'url' => 'javascript:window.history.back()'
                ]]);
            }

            $ticketID = $this->ticketObj->createTicket($USER['id'], $categoryID, $subject);

            if (isset($config->discord_tickets_hook) && !empty($config->discord_tickets_hook)) {
                require_once 'includes/classes/class.Discord.php';
                Discord::sendMessage($config->discord_tickets_hook, 'New ingame ticket created', [
                    'Ticket-ID' => $ticketID,
                    'User' => $USER['username'] . ' (' . $USER['id'] . ')',
                    'Subject' => $subject,
                    'Message' => $message,
                ]);
            }
        } else {
            $db = Database::get();

            $sql = 'SELECT `status` 
                FROM %%TICKETS%% 
                WHERE `ticketID` = :ticketID;';
            $ticketStatus = $db->selectSingle($sql, [
                ':ticketID' => $ticketID
            ], 'status');

            if ($ticketStatus == 2) {
                $this->printMessage($LNG['ti_error_closed']);
            } 
            else if (isset($config->discord_tickets_hook) && !empty($config->discord_tickets_hook)) {
                require_once 'includes/classes/class.Discord.php';
                Discord::sendMessage($config->discord_tickets_hook, 'New ticket reply', [
                    'Ticket-ID' => $ticketID,
                    'User' => $USER['username'] . ' (' . $USER['id'] . ')',
                    'Subject' => $subject,
                    'Message' => $message,
                ]);
            }
        }

        $this->ticketObj->createAnswer($ticketID, $USER['id'], $USER['username'], $subject, $message, 0);
        $this->redirectTo('game.php?page=ticket&mode=view&id=' . $ticketID);
    }

    public function view()
    {
        $USER =& Singleton()->USER;
        $LNG =& Singleton()->LNG;

        $db = Database::get();

        $ticketID = HTTP::_GP('id', 0);

        $sql = 'SELECT a.*, t.`categoryID`, t.`status` 
            FROM %%TICKETS_ANSWER%% a 
            INNER JOIN %%TICKETS%% t USING(`ticketID`) 
            WHERE a.`ticketID` = :ticketID AND t.`ownerID` = :ownerID AND t.`categoryID` != 9
            ORDER BY a.`answerID`;';
        $answerResult = $db->select($sql, [
            ':ticketID' => $ticketID,
            ':ownerID' => $USER['id']
        ]);

        $answerList = [];

        if (empty($answerResult)) {
            $this->printMessage(sprintf($LNG['ti_not_exist'], $ticketID), [[
                'label' => $LNG['sys_back'],
                'url' => 'game.php?page=ticket'
            ]]);
        }

        $ticket_status = 0;

        foreach ($answerResult as $answerRow) {
            $answerRow['time'] = _date($LNG['php_tdformat'], $answerRow['time'], $USER['timezone']);
            $answerRow['message'] = nl2br($answerRow['message']);
            $answerList[$answerRow['answerID']] = $answerRow;
            if (empty($ticket_status)) {
                $ticket_status = $answerRow['status'];
            }
        }

        $categoryList = $this->ticketObj->getCategoryList();

        $this->assign([
            'ticketID' => $ticketID,
            'categoryList' => $categoryList,
            'answerList' => $answerList,
            'status' => $ticket_status,
        ]);

        $this->display('page.ticket.view.tpl');
    }
}
