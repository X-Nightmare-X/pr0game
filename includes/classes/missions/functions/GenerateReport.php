<?php

/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

function GenerateReport($combatResult, $reportInfo, $isReal = false, $isSimul = false, $isExpo = false)
{
    $Destroy	= ['att' => 0, 'def' => 0];
    $DATA		= [];
    $DATA['mode']	= (int) $reportInfo['moonDestroy'];
    $DATA['time']	= $reportInfo['thisFleet']['fleet_start_time'];
    $DATA['start']	= [$reportInfo['thisFleet']['fleet_start_galaxy'], $reportInfo['thisFleet']['fleet_start_system'], $reportInfo['thisFleet']['fleet_start_planet'], $reportInfo['thisFleet']['fleet_start_type']];
    $DATA['koords']	= [$reportInfo['thisFleet']['fleet_end_galaxy'], $reportInfo['thisFleet']['fleet_end_system'], $reportInfo['thisFleet']['fleet_end_planet'], $reportInfo['thisFleet']['fleet_end_type']];
    $DATA['units']	= [$combatResult['unitLost']['attacker'], $combatResult['unitLost']['defender']];
    $DATA['debris']	= $reportInfo['debris'];
    $DATA['steal']	= $reportInfo['stealResource'];
    $DATA['result']	= $combatResult['won'];
    $DATA['moon']	= [
        'moonName'				=> $reportInfo['moonName'],
        'moonChance'			=> (int) $reportInfo['moonChance'],
        'moonDestroyChance'		=> (int) $reportInfo['moonDestroyChance'],
        'moonDestroySuccess'	=> (int) $reportInfo['moonDestroySuccess'],
        'fleetDestroyChance'	=> (int) $reportInfo['fleetDestroyChance'],
        'fleetDestroySuccess'	=> (int) $reportInfo['fleetDestroySuccess']
    ];
    $DATA['repaired'] = $combatResult['repaired'];
    if (isset($reportInfo['additionalInfo'])) {
        $DATA['additionalInfo'] = $reportInfo['additionalInfo'];
    } else {
        $DATA['additionalInfo']	= "";
    }

    foreach ($combatResult['rw'][0]['attackers'] as $player) {
        $DATA['players'][$player['player']['id']]	= [
            'name'		=> $player['player']['username'],
            'koords'	=> [$player['fleetDetail']['fleet_start_galaxy'], $player['fleetDetail']['fleet_start_system'], $player['fleetDetail']['fleet_start_planet'], $player['fleetDetail']['fleet_start_type']],
            'tech'		=> [$player['techs'][0] * 100, $player['techs'][1] * 100, $player['techs'][2] * 100],
        ];
    }
    foreach ($combatResult['rw'][0]['defenders'] as $player) {
        $DATA['players'][$player['player']['id']]	= [
            'name'		=> $player['player']['username'],
            'koords'	=> [$player['fleetDetail']['fleet_start_galaxy'], $player['fleetDetail']['fleet_start_system'], $player['fleetDetail']['fleet_start_planet'], $player['fleetDetail']['fleet_start_type']],
            'tech'		=> [$player['techs'][0] * 100, $player['techs'][1] * 100, $player['techs'][2] * 100],
        ];
    }

    foreach ($combatResult['rw'] as $Round => $RoundInfo) {
        foreach ($RoundInfo['attackers'] as $FleetID => $player) {
            $playerData	= ['userID' => $player['player']['id'], 'ships' => []];

            if (array_sum($player['unit']) == 0) {
                $DATA['rounds'][$Round]['attacker'][] = $playerData;
                $Destroy['att']++;
                continue;
            }

            foreach ($player['unit'] as $ShipID => $Amount) {
                if ($Amount <= 0) {
                    continue;
                }

                $ShipInfo	= $RoundInfo['infoA'][$FleetID][$ShipID];
                $playerData['ships'][$ShipID]	= [
                    $Amount, $ShipInfo['att'], $ShipInfo['shield'], $ShipInfo['def']
                ];
            }

            $DATA['rounds'][$Round]['attacker'][] = $playerData;
        }

        foreach ($RoundInfo['defenders'] as $FleetID => $player) {
            $playerData	= ['userID' => $player['player']['id'], 'ships' => []];
            if (array_sum($player['unit']) == 0) {
                $DATA['rounds'][$Round]['defender'][] = $playerData;
                $Destroy['def']++;
                continue;
            }

            foreach ($player['unit'] as $ShipID => $Amount) {
                if ($Amount <= 0) {
                    // $Destroy['def']++;
                    continue;
                }

                $ShipInfo	= $RoundInfo['infoD'][$FleetID][$ShipID];
                $playerData['ships'][$ShipID]	= [
                    $Amount, $ShipInfo['att'], $ShipInfo['shield'], $ShipInfo['def']
                ];
            }
            $DATA['rounds'][$Round]['defender'][] = $playerData;
        }

        if (isset($RoundInfo['attack'], $RoundInfo['attackShield'], $RoundInfo['defense'], $RoundInfo['defShield'])) {
            $DATA['rounds'][$Round]['info']	= [$RoundInfo['attack'], $RoundInfo['attackShield'], $RoundInfo['defense'], $RoundInfo['defShield']];
        } else {
            $DATA['rounds'][$Round]['info']	= [null, null, null, null];
        }
    }
    require_once 'includes/classes/Achievement.class.php';
    if ($isReal) {
		$db = Database::get();
		if ($reportInfo['stealResource'][901] == 1 && $reportInfo['stealResource'][902] == 1 && $reportInfo['stealResource'][903] == 1) {
			foreach ($combatResult['rw'][0]['attackers'] as $player)
			{
				if (!Achievement::checkAchievement($player['player']['id'], 6)) {
					Achievement::setAchievement($player['player']['id'], 6);
				}
			}
		}
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		if ($reportInfo['thisFleet']['fleet_amount'] == 42 && !Achievement::checkAchievement($mainAttacker, 39) 
			&& count($combatResult['rw'][0]['attackers']) == 1) {
			Achievement::setAchievement($mainAttacker, 39);
		}
		if ($reportInfo['moonDestroySuccess'] && !Achievement::checkAchievement($mainAttacker, 10)) {
			Achievement::setAchievement($mainAttacker, 10);
		}
		$planetdefender = $combatResult['rw'][0]['defenders'][count($combatResult['rw'][0]['defenders'])-1]['player']['id'];
		if (!Achievement::checkAchievement($planetdefender, 34)) {
			Achievement::setAchievement($planetdefender, 34);
		}
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
		//should be attacker?
//		} else if ($combatResult['won'] == "r" && !Achievement::checkAchievement($planetdefender, 3)) {
//			
//			$attackers = [];
//			foreach ($combatResult['rw'][0]['attackers'] as $player)
//			{
//				if (!in_array($player['player']['id'], $attackers)) {
//					array_push($attackers, $player['player']['id']);
//                    $sql = "SELECT fleet_points FROM uni1_statpoints WHERE id_owner = :id AND stat_type = 1;";
//                    $fleet_points = $db->selectSingle($sql, array(
//                        ':id' => $player['player']['id'],
//                    ), 'fleet_points');
//                    global $resource, $reslist, $pricelist;
//                    $FleetCounts = 0;
//                    $lostFleetPoints = 0;
//
//                    foreach ($reslist['fleet'] as $Fleet) {
//                        if ($combatResult['rw'][0]['attackers'][count($combatResult['rw'][0]['attackers'])-1]['Fleet'][$Fleet] == 0) continue;
//
//                        $Units = $pricelist[$Fleet]['cost'][901] + $pricelist[$Fleet]['cost'][902] + $pricelist[$Fleet]['cost'][903];
//                        $lostFleetPoints += $Units * $USER[$resource[$Fleet]];
//                        $FleetCounts += $USER[$resource[$Fleet]];
//                    }
//                    $sql = "SELECT lang FROM uni1_users WHERE id = :id;";
//                    $lang = $db->selectSingle($sql, array(
//                        ':id' => $planetdefender,
//                    ), 'lang');
//                    if ($lostFleetPoints > 1000 && $lostFleetPoints > $fleet_points/2 && $lang == "fr") {
//                        Achievement::setAchievement($planetdefender, 3);
//                    }
//				}
//			}
//			//check for loose condition
//            foreach($attackers as $attacker) {
//                
//            }
		}
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
	if ($isExpo) {
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		if ($reportInfo['thisFleet']['fleet_amount'] >= 10000 && !Achievement::checkAchievement($mainAttacker, 7) 
			&& $combatResult['won'] == "a") {
			Achievement::setAchievement($mainAttacker, 7);
		}
	}
    return $DATA;
}
