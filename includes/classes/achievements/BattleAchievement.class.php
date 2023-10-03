<?php

/**
 * pr0game powered by steemnova
 * achievements
 * (c) 2022 reflexrecon
 */

class BattleAchievement
{
	static public function checkBattleAchievements($reportInfo, $combatResult, $DATA)
	{
		require_once 'includes/classes/Achievement.class.php';
		$db = Database::get();
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		$planetdefender = $combatResult['rw'][0]['defenders'][count($combatResult['rw'][0]['defenders'])-1]['player']['id'];
		BattleAchievement::checkBattleAchievement6($reportInfo, $combatResult);
		BattleAchievement::checkBattleAchievement39($reportInfo, $combatResult, $mainAttacker);
		BattleAchievement::checkBattleAchievement10($reportInfo, $mainAttacker);
		BattleAchievement::checkBattleAchievement34($planetdefender);
		BattleAchievement::checkBattleAchievement47($DATA, $mainAttacker, $db);
		BattleAchievement::checkBattleAchievement48($DATA, $mainAttacker, $db, $reportInfo);
		BattleAchievement::checkBattleAchievement37($planetdefender, $combatResult);
		BattleAchievement::checkBattleAchievement35($mainAttacker, $combatResult, $db, $planetdefender);
		BattleAchievement::checkBattleAchievement38($mainAttacker, $combatResult, $db, $planetdefender);
		BattleAchievement::checkBattleAchievement32($mainAttacker, $combatResult, $db);
		BattleAchievement::checkBattleAchievement3($combatResult, $planetdefender, $db);
		BattleAchievement::checkBattleAchievement45($mainAttacker, $combatResult, $db, $planetdefender);
		BattleAchievement::checkBattleAchievement43($combatResult, $planetdefender, $db, $DATA);
	}
	static public function checkExpoBattleAchievements($reportInfo, $combatResult){
		require_once 'includes/classes/Achievement.class.php';
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		BattleAchievement::checkBattleAchievement7($reportInfo, $combatResult, $mainAttacker);
	}
	static public function checkBattleAchievement6($reportInfo, $combatResult){
		if ($reportInfo['stealResource'][901] == 1 && $reportInfo['stealResource'][902] == 1 && $reportInfo['stealResource'][903] == 1) {
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!Achievement::checkAchievement($player['player']['id'], 6)) {
					Achievement::setAchievement($player['player']['id'], 6);
				}
			}
		}
	}
	static public function checkBattleAchievement39($reportInfo, $combatResult, $mainAttacker){
		if ($reportInfo['thisFleet']['fleet_amount'] == 42 && !Achievement::checkAchievement($mainAttacker, 39) 
			&& count($combatResult['rw'][0]['attackers']) == 1) {
			Achievement::setAchievement($mainAttacker, 39);
		}
	}
	static public function checkBattleAchievement10($reportInfo, $mainAttacker){
		if ($reportInfo['moonDestroySuccess'] && !Achievement::checkAchievement($mainAttacker, 10)) {
			Achievement::setAchievement($mainAttacker, 10);
		}
	}
	static public function checkBattleAchievement34($planetdefender){
		if (!Achievement::checkAchievement($planetdefender, 34)) {
			Achievement::setAchievement($planetdefender, 34);
		}
	}
	static public function checkBattleAchievement47($DATA, $mainAttacker, $db){
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
	static public function checkBattleAchievement48($DATA, $mainAttacker, $db, $reportInfo){
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
	static public function checkBattleAchievement37($planetdefender, $combatResult){
		if (!Achievement::checkAchievement($planetdefender, 37) && $combatResult['won'] == "a") {
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
				Achievement::setAchievement($planetdefender, 37);
			}
		}
	}
	static public function checkBattleAchievement35($mainAttacker, $combatResult, $db, $planetdefender){
		if (!Achievement::checkAchievement($mainAttacker, 35) && $combatResult['won'] == "a" && count($combatResult['rw'][0]['attackers']) == 1) {
			$sql = "SELECT total_points FROM %%STATPOINTS%% a WHERE id_owner = :attacker AND stat_type = 1;";
			$attackerPoints = $db->selectSingle($sql, array(
				':attacker' => $mainAttacker,
			), 'total_points');

			$sql = "SELECT total_points FROM %%STATPOINTS%% a WHERE id_owner = :defender AND stat_type = 1;";
			$defenderPoints = $db->selectSingle($sql, array(
				':defender' => $planetdefender,
			), 'total_points');
			if ($attackerPoints/10 > $defenderPoints) {
				Achievement::setAchievement($mainAttacker, 35);
			}
		}
	}
	static public function checkBattleAchievement38($mainAttacker, $combatResult, $db, $planetdefender){
		if(!Achievement::checkAchievement($mainAttacker, 38) && count($combatResult['rw'][0]['attackers']) == 1) {
			$sql = "SELECT ally_id FROM %%USERS%% WHERE id = :attacker;";
			$attackerAlly = $db->selectSingle($sql, array(
				':attacker' => $mainAttacker,
			), 'ally_id');
			$sql = "SELECT ally_id FROM %%USERS%% WHERE id = :defender;";
			$defenderAlly = $db->selectSingle($sql, array(
				':defender' => $planetdefender,
			), 'ally_id');
			if ($attackerAlly != 0 && $attackerAlly == $defenderAlly) {
				Achievement::setAchievement($mainAttacker, 38);
			}
		}
	}
	static public function checkBattleAchievement32($mainAttacker, $combatResult, $db){
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
	static public function checkBattleAchievement3($combatResult, $planetdefender, $db){
		if (($combatResult['won'] == "a" && !Achievement::checkAchievement($planetdefender, 3))) {
			if ($combatResult['won'] == "a") {
				//check for loose condition mindestens 1k fleet punkte und mindestens 50%der flotte
				$sql = "SELECT fleet_points FROM uni1_statpoints WHERE id_owner = :id AND stat_type = 1;";
				$fleet_points = $db->selectSingle($sql, array(
					':id' => $planetdefender,
				), 'fleet_points');
				global $resource, $reslist, $pricelist;
				$FleetCounts = 0;
				$lostFleetPoints = 0;

				foreach ($reslist['fleet'] as $Fleet) {
					
					if ($combatResult['rw'][0]['defenders'][count($combatResult['rw'][0]['defenders'])-1]['unit'] == 0) continue;

					$Units = $pricelist[$Fleet]['cost'][901] + $pricelist[$Fleet]['cost'][902];

					$lostFleetPoints += $Units / 1000 ;
				}
				$sql = "SELECT lang FROM uni1_users WHERE id = :id;";
				$lang = $db->selectSingle($sql, array(
					':id' => $planetdefender,
				), 'lang');
				if ($lostFleetPoints > 1000 && $lostFleetPoints > $fleet_points/2 && $lang == "fr") {
					Achievement::setAchievement($planetdefender, 3);
				}
			}
		}
	}
	static public function checkBattleAchievement45($mainAttacker, $combatResult, $db, $planetdefender){
		$sql = "SELECT defs_rank FROM uni1_statpoints WHERE id_owner = :id AND stat_type = 1;";
		$defs_rank = $db->selectSingle($sql, array(
			':id' => $planetdefender,
		), 'defs_rank');
		if($defs_rank <= 5 && $combatResult['won'] == "a") {
			$countAttacker = 0;
			$attackers = [];
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!in_array($player['player']['id'], $attackers)&& !Achievement::checkAchievement($mainAttacker, 45)) {
					$countAttacker++;
					array_push($attackers, $player['player']['id']);
				}
			}
			foreach ($attackers as $attacker) {
				Achievement::setAchievement($attacker, 45);
			}
		}
	}
	static public function checkBattleAchievement43($combatResult, $planetdefender, $db, $DATA){
		if($combatResult['won'] == "r")
		{
			$sql = "SELECT onlinetime FROM %%USERS%% WHERE id = :defender;";
			$onlinetime = $db->selectSingle($sql, array(
				':defender' => $planetdefender,
			), 'onlinetime');
			if($onlinetime < TIMESTAMP - INACTIVE){
				$sql = "SELECT count(*) as count FROM %%LOG_FLEETS%% WHERE fleet_owner = :defender AND fleet_end_time - start_time >= :inactive AND fleet_end_time < :timestamp AND 
					fleet_start_galaxy = :start_galaxy AND fleet_start_system = :start_system AND fleet_start_planet = :start_planet AND fleet_start_type = :start_type;";
				$count = $db->selectSingle($sql, array(
					':defender' => $planetdefender,
					':inactive' => INACTIVE,
					':timestamp' => TIMESTAMP,
					':start_galaxy' => $DATA['koords'][0],
					':start_system' => $DATA['koords'][1],
					':start_planet' => $DATA['koords'][2],
					':start_type' => $DATA['koords'][3],
				), 'count');

				if ($count > 0 && !Achievement::checkAchievement($planetdefender, 43)) {
					Achievement::setAchievement($planetdefender, 43);
				}
			}
		}
	}
	static public function checkBattleAchievement7($reportInfo, $combatResult, $mainAttacker){
		if ($reportInfo['thisFleet']['fleet_amount'] >= 10000 && !Achievement::checkAchievement($mainAttacker, 7) 
			&& $combatResult['won'] == "a") {
			Achievement::setAchievement($mainAttacker, 7);
		}
	}
}