<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class Achievement
{
	static public function getUserAchievements($userID)
	{
		$db = Database::get();
		$sql = "SELECT * FROM %%USERS_TO_ACHIEVEMENTS%% WHERE userID = :userID;";
		$achievementList = $db->select($sql, array(
			':userID'	=> $userID
		));
		return $achievementList;
	}

	static public function getAchievementList()
	{
		$db = Database::get();
		$sql = "SELECT * FROM %%ACHIEVEMENTS%%;";
		$achievementList = $db->select($sql);
		return $achievementList;
	}

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
}