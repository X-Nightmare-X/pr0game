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
	static public function vacationAchievement($userID)
	{
		$db = Database::get();
		$sql = "SELECT raport FROM %%RW%% 
			WHERE (defender LIKE '%,:user' OR defender = :user) 
			AND 'time' > :sixHours;";
		$attacks = $db->select($sql, array(
			':user'			=> $userID,
			':sixHours'		=> TIMESTAMP - TIME_6_HOURS
		));
		if ($attacks) {
			foreach ($attacks as $attack) {
				$combatReport = unserialize($attack['raport']);
				
				if ($combatReport['result'] != 'a') {
					continue;
				}
				foreach ($combatReport['rounds'][0]['attacker'] as $attacker) {
					if (!Achievement::checkAchievement($attacker['userID'], 31)) {
						Achievement::setAchievement($attacker['userID'], 31);
					}
				}
			}
		}
		$sql = "SELECT raport FROM %%RW%% 
			WHERE (attacker LIKE ':user,%' OR attacker LIKE '%,:user,%' OR attacker LIKE '%,:user' or attacker = :user) 
			AND 'time' > :sixHours;";
		$attacks = $db->select($sql, array(
			':user'			=> $userID,
			':sixHours'		=> TIMESTAMP - TIME_6_HOURS
		));
		if ($attacks) {
			foreach ($attacks as $attack) {
				$combatReport = unserialize($attack['raport']);
				if ($combatReport['result'] != 'r') {
					continue;
				}
				foreach ($combatReport['rounds'][0]['defender'] as $defender) {
					if (!Achievement::checkAchievement($defender['userID'], 31)) {
						Achievement::setAchievement($defender['userID'], 31);
					}
				}
			}
		}
	}
}