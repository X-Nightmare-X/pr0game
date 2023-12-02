<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class PlanetRessAchievement
{
	/**
	 * Check for achievements in relation to Technology researches
	 *
	 * @param int			$element       	The id of the element
	 * @param int	 		$userId       	The id of the User
	 * @param int			$level       	The level of the element
	 */
	static public function checkPlanetRessAchievementsResearch($element, $userId, $level){
		require_once 'includes/classes/Achievement.class.php';
		if ($element == INTERGALACTIC_TECH && $level == 1) {
			Achievement::setAchievement($userId, 27);
		} else if ($element == DEFENCE_TECH) {
			switch ($level) {
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
		} else if ($element == GRAVITON_TECH && $level == 2) {
			Achievement::setAchievement($userId, 5);
		} else if ($element == LASER_TECH && $level == 12) {
			Achievement::setAchievement($userId, 24);
		} else if ($element == IONIC_TECH && $level == 10) {
			Achievement::setAchievement($userId, 4);
		}
	}

	/**
	 * Check for achievements in relation to Build ships
	 * Build your first Deathstar (Achievement 9)
	 * Build your first Deathstar as your first battleship (Achievement 52)
	 *
	 * @param int			$element       	The id of the element
	 * @param int	 		$userId       	The id of the User
	 */
	static public function checkPlanetRessAchievementsShips($element, $userId){
		require_once 'includes/classes/Achievement.class.php';
		require_once 'includes/classes/Database.class.php';
		if ($element == SHIP_RIP && !Achievement::checkAchievement($userId, 9)) {
			Achievement::setAchievement($userId, 9);
		}
		if ($element == SHIP_RIP && !Achievement::checkAchievement($userId, 52)) {
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

	/**
	 * Check for achievement 28
	 * Alternate gameplay: Build Metal, Crystal and Deut storages to Level 3 each on a Moon
	 *
	 * @param array			$PLANET       	The planet array
	 * @param array			$resource       The resource array
	 * @param int	 		$userId       	The id of the User
	 */
	static public function checkPlanetRessAchievements28($PLANET, $resource, $userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId,28) && $PLANET[$resource[METAL_STORE]] >= 3 && $PLANET[$resource[CRYSTAL_STORE]] >= 3 && $PLANET[$resource[DEUTERIUM_STORE]] >= 3) {
			Achievement::setAchievement($userId, 28);
		}
	}
}