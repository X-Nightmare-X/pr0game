<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class PlayerUtilAchievement
{
	/**
	 * Check for achievements in relation to Planet deletion
	 *
	 * @param array			$planetData       		The data of the planet
	 * @param int	 		$planetId       		The id of the planet
	 */
	static public function checkPlayerUtilAchievementsDeletion($planetData, $planetId){
		require_once 'includes/classes/Achievement.class.php';
		require_once 'includes/classes/Database.class.php';
		$db = Database::get();
		PlayerUtilAchievement::checkPlayerUtilAchievements26($planetData, $db, $planetId);
		PlayerUtilAchievement::checkPlayerUtilAchievements49($planetData, $db, $planetId);
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 40 
	 * Columbus: Colonize half a Universe away from your Home Planet
	 *
	 * @param int     		$userID       		The id of the User
	 * @param int     		$hpGala       		The home planet galaxy
	 * @param int     		$galaxy       		The galaxy of the planet
	 * @param Config     	$config       		The config of the game
	 * @param boolean    	$isHome       		Is the planet a home planet
	 */
	static public function checkPlayerUtilAchievements40($isHome, $hpGala, $userId, $galaxy, $config){
		require_once 'includes/classes/Achievement.class.php';
		if (!$isHome && !Achievement::checkAchievement($userId, 40) && $hpGala != $galaxy) {
            $max_gala_distance = floor($config->max_galaxy/2);
            $uniType = $config->uni_type;
            if ($uniType == UNIVERSE_LINEAR) {
                if (($hpGala > $galaxy && $hpGala - $galaxy > $max_gala_distance) || ($hpGala < $galaxy && $galaxy - $hpGala > $max_gala_distance)) {
                    Achievement::setAchievement($userId, 40);
                }
            } elseif ($uniType == UNIVERSE_CIRCULAR) {
                $max_distance = FleetFunctions::getTargetDistance(
                    [1,1,1],
                    [1+$max_gala_distance,1,1]
                );
                $distance = FleetFunctions::getTargetDistance(
                    [$hpGala,1,1],
                    [$galaxy,1,1]
                );
                if ($max_distance <= $distance) {
                    Achievement::setAchievement($userId, 40);
                }
            } elseif ($uniType == UNIVERSE_BALL) {
                Achievement::setAchievement($userId, 40);
            }
        }
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 26 
	 * Everything for the achievements: Delete your biggest planet pointwise
	 *
	 * @param array    		$planetData       		The data of the planet
	 * @param Database    	$db       				The database object
	 * @param int     		$planetId       		The id of the planet
	 */
	static public function checkPlayerUtilAchievements26($planetData, $db, $planetId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($planetData['id_owner'], 26)) {
			require_once 'includes/classes/class.statbuilder.php';
			$statbuidler = new Statbuilder();
			$sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userId and destruyed = 0;";
			$planets = $db->select($sql, [
				':userId'   => $planetData['id_owner']
			]);
			$biggestPlanet = 0;
			$biggestPlanetPoints = 0;
			foreach ($planets as $planet) {
				$points = $statbuidler->getBuildingScoreByPlanet($planet);
				if ($points > $biggestPlanetPoints) {
					$biggestPlanet = $planet['id'];
					$biggestPlanetPoints = $points;
				}
			}
			if($biggestPlanet == $planetId) {
				Achievement::setAchievement($planetData['id_owner'], 26);
			}
		}
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 49 
	 * We're blasting off again: Delete a planet within 60 seconds
	 *
	 * @param array    		$planetData       		The data of the planet
	 * @param Database    	$db       				The database object
	 * @param int     		$planetId       		The id of the planet
	 */
	static public function checkPlayerUtilAchievements49($planetData, $db, $planetId){
		require_once 'includes/classes/Achievement.class.php';
		if(!Achievement::checkAchievement($planetData['id_owner'], 49)){
			$sql = "SELECT creation_time FROM %%PLANETS%% WHERE id = :planetID;";
			$planet_creation_time = $db->selectSingle($sql, [
				':planetID'   => $planetId
			], 'creation_time');
			if(TIMESTAMP - $planet_creation_time <= 60){
				Achievement::setAchievement($planetData['id_owner'], 49);
			}
		}
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 36 
	 * Miesernachtsmann: Get an insulting message
	 *
	 * @param int	 		$userId       		The id of the User
	 * @param string    	$text       		The text of the message
	 */
	static public function checkPlayerUtilAchievements36($userId, $text){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 36)) {
            $targets = ['hurensohn', 'gleichstarke ziele','gegen schwächere', 'ehrenlos'];
            $find = false;
            foreach($targets as $t)
            {
                if (str_contains(strtolower($text),$t) !== false) {
                    $find = true;
                    break;
                }
            }
            if($find){
                Achievement::setAchievement($userId, 36);
            }
        }
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 33
	 * I can stop whenever I want: Return from vacation mode
	 *
	 * @param int	 		$userId       		The id of the User
	 */
	static public function checkPlayerUtilAchievements33($userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 33)) {
			Achievement::setAchievement($userId, 33);
		}
	}

	/**
	 * Check if a player completed the prerequisites for the achievement 31
	 * Off on vacation: Shoot someone into vacation mode
	 *
	 * @param int	 		$userId       		The id of the User
	 */
	static public function checkPlayerUtilAchievements31($userID)
	{
		require_once 'includes/classes/Achievement.class.php';
		require_once 'includes/classes/Database.class.php';
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
			AND `time` > :sixHours;";
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