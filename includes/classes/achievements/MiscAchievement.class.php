<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class MiscAchievement
{
	static public function checkPlayerUtilAchievementsDeletion($planetData, $planetId){
		require_once 'includes/classes/Achievement.class.php';
		$db = Database::get();
		PlayerUtilAchievement::checkPlayerUtilAchievements26($planetData, $db, $planetId);
		PlayerUtilAchievement::checkPlayerUtilAchievements49($planetData, $db, $planetId);
	}
	static public function checkExpoAchievements($userId){
		require_once 'includes/classes/Achievement.class.php';
		$sql = 'SELECT expo_count, expo_black_hole FROM %%ADVANCED_STATS%% WHERE userId = :userId;';
		$expoStats = Database::get()->selectSingle($sql, [
			':userId' => $userId,
		],);
		if ($expoStats['expo_count'] == 0 && !Achievement::checkAchievement($userId, 12)) {
			Achievement::setAchievement($userId, 12);
		} else if ($expoStats['expo_black_hole'] == 10 && !Achievement::checkAchievement($userId, 14)) {
			Achievement::setAchievement($userId, 14);
		}
	}
	static public function checkRecAchievements($userId, $collectedGoods, $recCount){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 16) && $collectedGoods[901] == 0 && $collectedGoods[902] == 0 && $recCount >= 250) {
			Achievement::setAchievement($userId, 16);
		}
	}
	static public function checkGalaxyAchievements($userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 51)) {
			Achievement::setAchievement($userId, 51);
		}
	}
	static public function checkSettingsAchievements($userId, $recordsOptIn){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 25) && $recordsOptIn == 1) {
			Achievement::setAchievement($userId, 25);
		}
	}
	static public function checkShipyardAchievements($userId, $Count, $Element){
		require_once 'includes/classes/Achievement.class.php';
		if(!Achievement::checkAchievement($userId, 17) && $Count >= 5000 && $Element == MISSILE_LAUNCHER) {
			Achievement::setAchievement($userId, 17);
		}
	}
}