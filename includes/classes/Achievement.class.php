<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class Achievement
{
	/**
	 * Return all Achievements unlocked by a player.
	 *
	 * @param int     	$userID       		The id of the User
	 * 
	 * @return array
	 */
	static public function getUserAchievements($userID)
	{
		$db = Database::get();
		$sql = "SELECT * FROM %%USERS_TO_ACHIEVEMENTS%% WHERE userID = :userID;";
		$achievementList = $db->select($sql, array(
			':userID'	=> $userID
		));
		return $achievementList;
	}
	/**
	 * Return all Achievements.
	 *
	 * @return array
	 */
	static public function getAchievementList()
	{
		$db = Database::get();
		$sql = "SELECT * FROM %%ACHIEVEMENTS%%;";
		$achievementList = $db->select($sql);
		return $achievementList;
	}
	/**
	 * Unlock a specific axhievement for a User.
	 *
	 * @param int     		$userID       		The id of the User
	 * @param int      		$achievementID     	The id of the Achievement
	 * 
	 */
	static public function setAchievement($userID, $achievementID)
	{
		$db = Database::get();
		$sql = "INSERT INTO %%USERS_TO_ACHIEVEMENTS%% SET userID = :userID, achievementID = :achievementID, date = :date;";
		$db->insert($sql, array(
			':userID'		=> $userID,
			':achievementID'=> $achievementID,
			':date'			=> TIMESTAMP
		));
	}
	/**
	 * Check if a User has a specific Achievement unlocked.
	 *
	 * @param int     	$userID       		The id of the User
	 * @param int      	$achievementID     	The id of the Achievement
	 *
	 * @return array
	 */
	static public function checkAchievement($userID, $achievementID)
	{
		$db = Database::get();
		$sql = "SELECT * FROM %%USERS_TO_ACHIEVEMENTS%% WHERE userID = :userID AND achievementID = :achievementID;";
		$achievementList = $db->selectSingle($sql, array(
			':userID'		=> $userID,
			':achievementID'=> $achievementID
		));
		return $achievementList;
	}

	public static function getAvailableTitles($userID)
    {
		$db = Database::get();
		$sql = "SELECT a.id, a.title FROM %%ACHIEVEMENTS%% a inner join %%USERS_TO_ACHIEVEMENTS%% ua on ua.achievementID = a.id where ua.userID = :userID and a.title IS NOT NULL ORDER BY a.id asc;";
		$titles =$db->select($sql, [
			':userID'   => $userID,
		]);
		$returnTitles	= [];
		foreach ($titles as $title) {
			$returnTitles[$title['id']]	= $title['title'];
		}
        return $returnTitles;
    }

	public static function getAllTitles()
    {
		$db = Database::get();
		$sql = "SELECT a.id, a.title FROM %%ACHIEVEMENTS%% a inner join %%USERS_TO_ACHIEVEMENTS%% ua on ua.achievementID = a.id where a.title IS NOT NULL ORDER BY a.id asc;";
		$titles =$db->select($sql, []);
		$returnTitles	= [];
		foreach ($titles as $title) {
			$returnTitles[$title['id']]	= $title['title'];
		}
        return $returnTitles;
    }
}