<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class MiscAchievement
{
	/**
	 * Check for achievement in relation to Expeditions
	 * Slippy's blessing(12): Lose your first expedition
	 * Slippy's curse(14):Lose 10 expeditions
	 *
	 * @param array			$PLANET       	The planet array
	 * @param array			$resource       The resource array
	 * @param int	 		$userId       	The id of the User
	 */
	static public function checkExpoAchievements($userId){
		require_once 'includes/classes/Achievement.class.php';
		require_once 'includes/classes/Database.class.php';
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

	/**
	 * Check for achievement 16
	 * Dude, Where's My Debris?: Have 250+ Recyclers return with no debris
	 *
	 * @param int	 		$userId       		The id of the User
	 * @param array			$collectedGoods     The resource array
	 * @param int	 		$recCount       	The number of recyclers
	 */
	static public function checkRecAchievements($userId, $collectedGoods, $recCount){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 16) && $collectedGoods[RESOURCE_METAL] == 0 && $collectedGoods[RESOURCE_CRYSTAL] == 0 && $recCount >= 250) {
			Achievement::setAchievement($userId, 16);
		}
	}

	/**
	 * Check for achievement 51
	 * No clue, it's just there?: Drain your deuterium with the galaxy view
	 *
	 * @param int	 		$userId       		The id of the User
	 */
	static public function checkGalaxyAchievements($userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 51)) {
			Achievement::setAchievement($userId, 51);
		}
	}

	/**
	 * Check for achievement 25
	 * GDPR breach: Opt in to records sharing
	 *
	 * @param int	 		$userId       		The id of the User
	 * @param int	 		$recordsOptIn       The records opt in
	 */
	static public function checkSettingsAchievements($userId, $recordsOptIn){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 25) && $recordsOptIn == 1) {
			Achievement::setAchievement($userId, 25);
		}
	}

	/**
	 * Check for achievement 17
	 * Overlord Defense: Build 5000 missile launchers in one go
	 *
	 * @param int	 		$userId       		The id of the User
	 * @param int	 		$count       		The number of missile launchers
	 * @param int	 		$element       		The id of the element
	 */
	static public function checkShipyardAchievements($userId, $count, $element){
		require_once 'includes/classes/Achievement.class.php';
		if(!Achievement::checkAchievement($userId, 17) && $count >= 5000 && $element == MISSILE_LAUNCHER) {
			Achievement::setAchievement($userId, 17);
		}
	}
}