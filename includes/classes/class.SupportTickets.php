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

class SupportTickets
{
	public function createTicket($ownerID, $categoryID, $subject)
	{
        $db = Database::get();
		$sql 	= 'INSERT INTO %%TICKETS%% SET
		ownerID		= :ownerId,
		universe	= :universe,
		categoryID	= :categoryId,
		subject		= :subject,
		time		= :time;';

        $db->insert($sql, array(
			':ownerId'		=> $ownerID,
			':universe'		=> Universe::current(),
			':categoryId'	=> $categoryID,
			':subject'		=> $subject,
			':time'			=> TIMESTAMP
		));

        $sql = "SELECT `ticketID` FROM %%TICKETS%% WHERE
        `ownerID` = :ownerId AND
		`universe` = :universe AND
		`categoryID` = :categoryId AND
		`subject` = :subject AND
		`time` = :time;";
        $id = $db->selectSingle($sql, [
			':ownerId' => $ownerID,
			':universe' => Universe::current(),
			':categoryId' => $categoryID,
			':subject' => $subject,
			':time' => TIMESTAMP,
        ], 'ticketID');

		return $id;
	}

	public function createAnswer($ticketID, $ownerID, $ownerName, $subject, $message, $status)
	{
        $db = Database::get();
		$sql = 'INSERT INTO %%TICKETS_ANSWER%% SET
		ticketID	= :ticketId,
		ownerID		= :ownerId,
		ownerName	= :ownerName,
		subject		= :subject,
		message		= :message,
		time		= :time;';

        $db->insert($sql, array(
			':ticketId'		=> $ticketID,
			':ownerId'		=> $ownerID,
			':ownerName'	=> $ownerName,
			':subject'		=> $subject,
			':message'		=> $message,
			':time'			=> TIMESTAMP
		));

        $sql = "SELECT `answerID` FROM %%TICKETS%% WHERE
        `ticketID` = :ticketId,
		`ownerID` = :ownerId,
		`ownerName` = :ownerName,
		`subject` = :subject,
		`message` = :message,
		`time` = :time;";
        $answerId = $db->selectSingle($sql, [
			':ticketId' => $ticketID,
			':ownerId' => $ownerID,
			':ownerName' => $ownerName,
			':subject' => $subject,
			':message' => $message,
			':time' => TIMESTAMP,
        ], 'answerID');

		$sql	= 'UPDATE %%TICKETS%% SET status = :status WHERE ticketID = :ticketId;';

		$db->update($sql, array(
			':status'	=> $status,
			':ticketId'	=> $ticketID
		));

		return $answerId;
	}

	public function getCategoryList()
	{
		$sql	= 'SELECT * FROM %%TICKETS_CATEGORY%%;';

		$categoryResult		= Database::get()->select($sql);
		$categoryList		= array();

		foreach($categoryResult as $categoryRow)
		{
			$categoryList[$categoryRow['categoryID']]	= $categoryRow['name'];
		}

		return $categoryList;
	}
}
