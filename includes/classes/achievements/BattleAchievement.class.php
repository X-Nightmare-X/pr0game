<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class BattleAchievement
{
	/**
	 * Check for achievements related to pvp battles
	 *
	 * @param array			$reportInfo       	The report array
	 * @param array			$combatResult       The combat result array
	 * @param array			$DATA       		The report data array
	 */
	static public function checkBattleAchievements($reportInfo, $combatResult, $DATA)
	{
		require_once 'includes/classes/Achievement.class.php';
		require_once 'includes/classes/Database.class.php';
		$db = Database::get();
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		$planetDefender = $combatResult['rw'][0]['defenders'][0]['player']['id'];
		BattleAchievement::checkBattleAchievement6($reportInfo, $combatResult);
		BattleAchievement::checkBattleAchievement39($reportInfo, $combatResult, $mainAttacker);
		BattleAchievement::checkBattleAchievement10($reportInfo, $mainAttacker);
		BattleAchievement::checkBattleAchievement34($planetDefender);
		BattleAchievement::checkBattleAchievement47($DATA, $mainAttacker, $db);
		BattleAchievement::checkBattleAchievement48($DATA, $mainAttacker, $db, $reportInfo);
		BattleAchievement::checkBattleAchievement37($planetDefender, $combatResult);
		BattleAchievement::checkBattleAchievement35($mainAttacker, $combatResult, $db, $planetDefender);
		BattleAchievement::checkBattleAchievement38($mainAttacker, $combatResult, $db, $planetDefender);
		BattleAchievement::checkBattleAchievement32($mainAttacker, $combatResult, $db);
		BattleAchievement::checkBattleAchievement3($combatResult, $planetDefender, $db);
		BattleAchievement::checkBattleAchievement45($combatResult, $db, $planetDefender);
		BattleAchievement::checkBattleAchievement43($combatResult, $planetDefender, $db, $DATA);
	}

	/**
	 * Check for achievements related to expedition battles
	 *
	 * @param array			$reportInfo       	The report array
	 * @param array			$combatResult       The combat result array
	 */
	static public function checkExpoBattleAchievements($reportInfo, $combatResult){
		require_once 'includes/classes/Achievement.class.php';
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		BattleAchievement::checkBattleAchievement7($reportInfo, $combatResult, $mainAttacker);
	}

	/**
	 * Check for achievement 6
	 * Low performer: Have an attack come back with exqactly 1 metal, 1 crystal and 1 deuterium
	 *
	 * @param array			$reportInfo       	The report array
	 * @param array			$combatResult       The combat result array
	 */
	static public function checkBattleAchievement6($reportInfo, $combatResult){
		require_once 'includes/classes/Achievement.class.php';
		if ($reportInfo['stealResource'][RESOURCE_METAL] == 1 && $reportInfo['stealResource'][RESOURCE_CRYSTAL] == 1 && $reportInfo['stealResource'][RESOURCE_CRYSTAL] == 1) {
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!Achievement::checkAchievement($player['player']['id'], 6)) {
					Achievement::setAchievement($player['player']['id'], 6);
				}
			}
		}
	}

	/**
	 * Check for achievement 39
	 * The meaning of life: Attack someone with 42 ships
	 *
	 * @param array			$reportInfo       	The report array
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$mainAttacker       The id of the User
	 */
	static public function checkBattleAchievement39($reportInfo, $combatResult, $mainAttacker){
		require_once 'includes/classes/Achievement.class.php';
		if ($reportInfo['thisFleet']['fleet_amount'] == 42 && !Achievement::checkAchievement($mainAttacker, 39)
			&& count($combatResult['rw'][0]['attackers']) == 1) {
			Achievement::setAchievement($mainAttacker, 39);
		}
	}

	/**
	 * Check for achievement 10
	 * Marble game: Destroy one moon
	 *
	 * @param array			$reportInfo       	The report array
	 * @param int	 		$mainAttacker       The id of the User
	 */
	static public function checkBattleAchievement10($reportInfo, $mainAttacker){
		require_once 'includes/classes/Achievement.class.php';
		if ($reportInfo['moonDestroySuccess'] && !Achievement::checkAchievement($mainAttacker, 10)) {
			Achievement::setAchievement($mainAttacker, 10);
		}
	}

	/**
	 * Check for achievement 34
	 * Noble farm: Get attacked once
	 *
	 * @param int	 		$planetDefender     The id of the User
	 */
	static public function checkBattleAchievement34($planetDefender){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($planetDefender, 34)) {
			Achievement::setAchievement($planetDefender, 34);
		}
	}

	/**
	 * Check for achievement 47
	 * Jack the Ripper: Destroy 5 moons
	 *
	 * @param array			$DATA       		The report data array
	 * @param int	 		$mainAttacker       The id of the User
	 * @param Database	 	$db       			The database object
	 */
	static public function checkBattleAchievement47($DATA, $mainAttacker, $db){
		require_once 'includes/classes/Achievement.class.php';
		if ($DATA['moon']['moonDestroySuccess'] == 1) {
			$sql = "SELECT moons_destroyed FROM %%ADVANCED_STATS%% WHERE userId = :id;";
			$count = $db->selectSingle($sql, array(
				':id' => $mainAttacker,
			), 'moons_destroyed');
			if (!Achievement::checkAchievement($mainAttacker, 47) && $count >= 5) {
				Achievement::setAchievement($mainAttacker, 47);
			}
		}
	}

	/**
	 * Check for achievement 48
	 * Rip the Jacker: Lose 5 rips while trying to destroy moons
	 *
	 * @param array			$DATA       		The report data array
	 * @param int	 		$mainAttacker       The id of the User
	 * @param Database	 	$db       			The database object
	 * @param array			$reportInfo       	The report array
	 */
	static public function checkBattleAchievement48($DATA, $mainAttacker, $db, $reportInfo){
		require_once 'includes/classes/Achievement.class.php';
		if ($DATA['moon']['fleetDestroySuccess'] == 1) {
			$shiptypes = explode (";", $reportInfo['thisFleet']["fleet_array"]);
			foreach ($shiptypes as $shiptype) {
				$ship = explode (",", $shiptype);
				if ($ship[0] == 214) {
					$sql = "SELECT destroy_moon_rips_lost FROM %%ADVANCED_STATS%% WHERE userId = :id;";
					$count = $db->selectSingle($sql, array(
						':id' => $mainAttacker,
					), 'destroy_moon_rips_lost');

					if (!Achievement::checkAchievement($mainAttacker, 48) && $count >= 5) {
						Achievement::setAchievement($mainAttacker, 48);
					}
				}
			}
		}
	}

	/**
	 * Check for achievement 37
	 * I am not one of those: Get attacked by a 5 man ACS and lose
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$planetDefender     The id of the User
	 */
	static public function checkBattleAchievement37($planetDefender, $combatResult){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($planetDefender, 37) && $combatResult['won'] == "a") {
			$countAttacker = 0;
			$attackers = [];
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!in_array($player['player']['id'], $attackers)) {
					$countAttacker++;
					array_push($attackers, $player['player']['id']);
				}
			}
            //max aks participants var?
			if ($countAttacker == 5) {
				Achievement::setAchievement($planetDefender, 37);
			}
		}
	}

	/**
	 * Check for achievement 35
	 * That's not nice!: Attack an active player with less than 10% of your points
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$mainAttacker     	The id of the User
	 * @param Database	 	$db       			The database object
	 * @param int	 		$planetDefender     The id of the User
	 */
	static public function checkBattleAchievement35($mainAttacker, $combatResult, $db, $planetDefender){
		require_once 'includes/classes/Achievement.class.php';
		if (!Achievement::checkAchievement($mainAttacker, 35) && $combatResult['won'] == "a" && count($combatResult['rw'][0]['attackers']) == 1) {
			$sql = "SELECT total_points FROM %%STATPOINTS%% a WHERE id_owner = :attacker AND stat_type = 1;";
			$attackerPoints = $db->selectSingle($sql, array(
				':attacker' => $mainAttacker,
			), 'total_points');

			$sql = "SELECT total_points FROM %%STATPOINTS%% a WHERE id_owner = :defender AND stat_type = 1;";
			$defenderPoints = $db->selectSingle($sql, array(
				':defender' => $planetDefender,
			), 'total_points');

			$sql = "SELECT onlinetime FROM %%USERS%% WHERE id = :defender;";
			$defenderOnlinetime = $db->selectSingle($sql, array(
				':defender' => $planetDefender,
			), 'onlinetime');

			if ($attackerPoints/10 > $defenderPoints && $defenderOnlinetime > TIMESTAMP - INACTIVE) {
				Achievement::setAchievement($mainAttacker, 35);
			}
		}
	}

	/**
	 * Check for achievement 38
	 * Cornag and Offz: Attack a player with the same alliance
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$mainAttacker     	The id of the User
	 * @param Database	 	$db       			The database object
	 * @param int	 		$planetDefender     The id of the User
	 */
	static public function checkBattleAchievement38($mainAttacker, $combatResult, $db, $planetDefender){
		require_once 'includes/classes/Achievement.class.php';
		if(!Achievement::checkAchievement($mainAttacker, 38) && count($combatResult['rw'][0]['attackers']) == 1) {
			$sql = "SELECT ally_id FROM %%USERS%% WHERE id = :attacker;";
			$attackerAlly = $db->selectSingle($sql, array(
				':attacker' => $mainAttacker,
			), 'ally_id');
			$sql = "SELECT ally_id FROM %%USERS%% WHERE id = :defender;";
			$defenderAlly = $db->selectSingle($sql, array(
				':defender' => $planetDefender,
			), 'ally_id');
			if ($attackerAlly != 0 && $attackerAlly == $defenderAlly) {
				Achievement::setAchievement($mainAttacker, 38);
			}
		}
	}

	/**
	 * Check for achievement 32
	 * Privateer: Win 1000 battles with the SetSail theme active
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$mainAttacker     	The id of the User
	 * @param Database	 	$db       			The database object
	 */
	static public function checkBattleAchievement32($mainAttacker, $combatResult, $db){
		require_once 'includes/classes/Achievement.class.php';
		$sql = "SELECT dpath FROM %%USERS%% WHERE id = :id;";
		$theme = $db->selectSingle($sql, array(
			':id' => $mainAttacker,
		), 'dpath');
		if (($combatResult['won'] == "a" && $theme == "SetSail")) {
			$sql = "SELECT set_sail_wins FROM %%ADVANCED_STATS%% WHERE userId = :id;";
			$count = $db->selectSingle($sql, array(
				':id' => $mainAttacker,
			), 'set_sail_wins');
			if (!Achievement::checkAchievement($mainAttacker, 32) && $count >= 1000) {
				Achievement::setAchievement($mainAttacker, 32);
			}
		}
	}

	/**
	 * Check for achievement 3
	 * Deserved: Lose atleast 1000 fleet points and atleast 50% of your fleet while your language is set to french
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$planetDefender     The id of the User
	 * @param Database	 	$db       			The database object
	 */
	static public function checkBattleAchievement3($combatResult, $planetDefender, $db){
		require_once 'includes/classes/Achievement.class.php';
		if (($combatResult['won'] == "a" && !Achievement::checkAchievement($planetDefender, 3))) {
			if ($combatResult['won'] == "a") {
				//check for loose condition mindestens 1k fleet punkte und mindestens 50%der flotte
				$sql = "SELECT fleet_points FROM uni1_statpoints WHERE id_owner = :id AND stat_type = 1;";
				$fleet_points = $db->selectSingle($sql, array(
					':id' => $planetDefender,
				), 'fleet_points');
				global $resource, $reslist, $pricelist;
				$FleetCounts = 0;
				$lostFleetPoints = 0;

				foreach ($reslist['fleet'] as $Fleet) {

					if ($combatResult['rw'][0]['defenders'][0]['unit'] == 0) continue;

					$Units = $pricelist[$Fleet]['cost'][901] + $pricelist[$Fleet]['cost'][902];

					$lostFleetPoints += $Units / 1000 ;
				}
				$sql = "SELECT lang FROM uni1_users WHERE id = :id;";
				$lang = $db->selectSingle($sql, array(
					':id' => $planetDefender,
				), 'lang');
				if ($lostFleetPoints > 1000 && $lostFleetPoints > $fleet_points/2 && $lang == "fr") {
					Achievement::setAchievement($planetDefender, 3);
				}
			}
		}
	}

	/**
	 * Check for achievement 45
	 * Locksmith: Win agaisnt a top 5 defense rank player
	 *
	 * @param int	 		$mainAttacker       The id of the User
	 * @param array			$combatResult       The combat result array
	 * @param Database	 	$db       			The database object
	 * @param int	 		$planetDefender     The id of the User
	 */
	static public function checkBattleAchievement45($combatResult, $db, $planetDefender){
		require_once 'includes/classes/Achievement.class.php';
		$sql = "SELECT defs_rank FROM uni1_statpoints WHERE id_owner = :id AND stat_type = 1;";
		$defs_rank = $db->selectSingle($sql, array(
			':id' => $planetDefender,
		), 'defs_rank');
		if($defs_rank <= 5 && $combatResult['won'] == "a") {
			$countAttacker = 0;
			$attackers = [];
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!in_array($player['player']['id'], $attackers) && !Achievement::checkAchievement($player['player']['id'], 45)) {
					$countAttacker++;
					array_push($attackers, $player['player']['id']);
				}
			}
			foreach ($attackers as $attacker) {
				Achievement::setAchievement($attacker, 45);
			}
		}
	}

	/**
	 * Check for achievement 43
	 * With my Eyes closed: Win as a definder while your account is flagged as inactive, with a fleet that was away for 7 days
	 *
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$planetDefender     The id of the User
	 * @param Database	 	$db       			The database object
	 * @param array			$DATA       		The report data array
	 */
	static public function checkBattleAchievement43($combatResult, $planetDefender, $db, $DATA){
		require_once 'includes/classes/Achievement.class.php';
		if($combatResult['won'] == "r")
		{
			$sql = "SELECT onlinetime FROM %%USERS%% WHERE id = :defender;";
			$onlinetime = $db->selectSingle($sql, array(
				':defender' => $planetDefender,
			), 'onlinetime');
			if($onlinetime < TIMESTAMP - INACTIVE){
				$sql = "SELECT count(*) as count FROM %%LOG_FLEETS%% WHERE fleet_owner = :defender AND fleet_end_time - start_time >= :inactive AND fleet_end_time < :timestamp AND
					fleet_start_galaxy = :start_galaxy AND fleet_start_system = :start_system AND fleet_start_planet = :start_planet AND fleet_start_type = :start_type;";
				$count = $db->selectSingle($sql, array(
					':defender' => $planetDefender,
					':inactive' => INACTIVE,
					':timestamp' => TIMESTAMP,
					':start_galaxy' => $DATA['koords'][0],
					':start_system' => $DATA['koords'][1],
					':start_planet' => $DATA['koords'][2],
					':start_type' => $DATA['koords'][3],
				), 'count');

				if ($count > 0 && !Achievement::checkAchievement($planetDefender, 43)) {
					Achievement::setAchievement($planetDefender, 43);
				}
			}
		}
	}

	/**
	 * Check for achievement 7
	 * French revolution: Win against pirates or aliens with atleast 10.000 ships
	 *
	 * @param array			$reportInfo       	The report array
	 * @param array			$combatResult       The combat result array
	 * @param int	 		$mainAttacker       The id of the User
	 */
	static public function checkBattleAchievement7($reportInfo, $combatResult, $mainAttacker){
		require_once 'includes/classes/Achievement.class.php';
		if ($reportInfo['thisFleet']['fleet_amount'] >= 10000 && !Achievement::checkAchievement($mainAttacker, 7)
			&& $combatResult['won'] == "a") {
			Achievement::setAchievement($mainAttacker, 7);
		}
	}
}
