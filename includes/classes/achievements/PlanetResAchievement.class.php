<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class PlanetResAchievement
{
	static public function checkPlanetResAchievementsResearch($Element, $userId, $Count){
		require_once 'includes/classes/Achievement.class.php';
		if ($Element == 123 && $Count == 1) {
			Achievement::setAchievement($userId, 27);
		} else if ($Element == 111) {
			switch ($Count) {
				case 3:
					Achievement::setAchievement($userId, 19);
					break;
				case 6:
					Achievement::setAchievement($userId, 20);
					break;
				case 9:
					Achievement::setAchievement($userId, 21);
					break;
				case 12:
					Achievement::setAchievement($userId, 22);
					break;
				case 15:
					Achievement::setAchievement($userId, 23);
					break;
			}
		} else if ($Element == 199 && $Count == 2) {
			Achievement::setAchievement($userId, 5);
		} else if ($Element == 120 && $Count == 12) {
			Achievement::setAchievement($userId, 24);
		} else if ($Element == 121 && $Count == 10) {
			Achievement::setAchievement($userId, 4);
		}
	}
	static public function checkPlanetResAchievementsShips($Element, $userId){
		require_once 'includes/classes/Achievement.class.php';
		if ($Element == SHIP_RIP && !Achievement::checkAchievement($userId, 9)) {
			Achievement::setAchievement($userId, 9);
		}
		if ($Element == SHIP_RIP && !Achievement::checkAchievement($userId, 52)) {
			$sql = "SELECT COUNT(*) as count FROM %%ADVANCED_STATS%% 
			WHERE build_204 = 0 AND build_205 = 0 AND build_206 = 0 AND build_207 = 0 AND build_211 = 0 
			AND build_213 = 0 AND build_214 = 0 AND build_215 = 0 AND build_216 = 0 AND build_217 = 0 
			AND build_218 = 0 AND build_219 = 0 AND userId = :userId;";
			$countFightShip = database::get()->selectSingle($sql, array(
				':userId'   => $userId
			));

			if ($countFightShip['count'] == 1) {
				Achievement::setAchievement($userId, 52);
			}
		}
	}
	static public function checkPlanetResAchievements28($PLANET, $resource, $userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId,28) && $PLANET[$resource[22]] >= 3 && $PLANET[$resource[23]] >= 3 && $PLANET[$resource[24]] >= 3) {
			Achievement::setAchievement($userId, 28);
		}
	}
}