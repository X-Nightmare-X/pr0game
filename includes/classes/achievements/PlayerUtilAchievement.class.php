<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class PlayerUtilAchievement
{
	static public function checkPlayerUtilAchievementsDeletion($planetData, $planetId){
		require_once 'includes/classes/Achievement.class.php';
		$db = Database::get();
		PlayerUtilAchievement::checkPlayerUtilAchievements26($planetData, $db, $planetId);
		PlayerUtilAchievement::checkPlayerUtilAchievements49($planetData, $db, $planetId);
	}
	static public function checkPlanetResAchievementsShips($Element, $userid){
		require_once 'includes/classes/Achievement.class.php';
		if ($Element == SHIP_RIP && !Achievement::checkAchievement($userid, 9)) {
			Achievement::setAchievement($userid, 9);
		}
		if ($Element == SHIP_RIP && !Achievement::checkAchievement($userid, 52)) {
			$sql = "SELECT COUNT(*) as count FROM %%ADVANCED_STATS%% 
			WHERE build_204 = 0 AND build_205 = 0 AND build_206 = 0 AND build_207 = 0 AND build_211 = 0 
			AND build_213 = 0 AND build_214 = 0 AND build_215 = 0 AND build_216 = 0 AND build_217 = 0 
			AND build_218 = 0 AND build_219 = 0 AND userId = :userId;";
			$countFightShip = database::get()->selectSingle($sql, array(
				':userId'   => $userid
			));

			if ($countFightShip['count'] == 1) {
				Achievement::setAchievement($userid, 52);
			}
		}
	}
	
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
	static public function checkPlayerUtilAchievements26($planetData, $db, $planetId){
		if (!Achievement::checkAchievement($planetData['id_owner'], 26)) {
			require_once 'includes/classes/class.statbuilder.php';
			$statbuidler = new Statbuilder();
			$sql = "SELECT * FROM %%PLANETS%% WHERE id_owner = :userId;";
			$planets = $db->select($sql, [
				':userId'   => $planetData['id_owner']
			]);
			$biggestPlanet = 0;
			$biggestPlanetPoints = 0;
			foreach ($planets as $planet) {
				$points = $statbuidler->getBuildingScoreByPlanet($planet);
				if ($points > $biggestPlanetPoints) {
					$biggestPlanet = $planet['id'];
				}
			}
			if($biggestPlanet == $planetId) {
				Achievement::setAchievement($planetData['id_owner'], 26);
			}
		}
	}
	static public function checkPlayerUtilAchievements49($planetData, $db, $planetId){
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
	static public function checkPlayerUtilAchievements33($userId){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($userId, 33)) {
			Achievement::setAchievement($userId, 33);
		}
	}

	static public function checkPlayerUtilAchievements31($userID)
	{
		require_once 'includes/classes/Achievement.class.php';
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