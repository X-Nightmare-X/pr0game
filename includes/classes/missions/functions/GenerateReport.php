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

function GenerateReport($combatResult, $reportInfo, $battleType)
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
    $DATA['duration']	= $combatResult['duration'];
    $DATA['moon']	= [
        'moonName'				=> $reportInfo['moonName'],
        'moonChance'			=> (int) $reportInfo['moonChance'],
        'additionalChance'		=> (int) $reportInfo['additionalChance'],
        'moonDestroyChance'		=> (int) $reportInfo['moonDestroyChance'],
        'moonDestroySuccess'	=> (int) $reportInfo['moonDestroySuccess'],
        'fleetDestroyChance'	=> (int) $reportInfo['fleetDestroyChance'],
        'fleetDestroySuccess'	=> (int) $reportInfo['fleetDestroySuccess']
    ];
    $DATA['repaired'] = $combatResult['repaired'];
    $DATA['wreckfield'] = $combatResult['wreckfield'];
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

    if ($battleType == REAL_FIGHT) {
        require_once 'includes/classes/achievements/BattleAchievement.class.php';
		$db = Database::get();
		$mainAttacker = $reportInfo['thisFleet']['fleet_owner'];
		if ($DATA['moon']['moonDestroySuccess'] == 1) {
			$sql = "UPDATE %%ADVANCED_STATS%% SET moons_destroyed = moons_destroyed + 1 WHERE userId = :userId";
			$db->update($sql, [
				':userId' => $mainAttacker,
			]);
		}
		if ($DATA['moon']['fleetDestroySuccess'] == 1) {
			$shiptypes = explode (";", $reportInfo['thisFleet']["fleet_array"]);
			foreach ($shiptypes as $shiptype) {
				$ship = explode (",", $shiptype);
				if ($ship[0] == SHIP_RIP) {
					$sql = "UPDATE %%ADVANCED_STATS%% SET destroy_moon_rips_lost = destroy_moon_rips_lost + :lost WHERE userId = :userId";
					$db->update($sql, [
						':lost' => $ship[1],
						':userId' => $mainAttacker,
					]);
				}
			}
		}
		$sql = "SELECT dpath FROM %%USERS%% WHERE id = :id;";
		$theme = $db->selectSingle($sql, array(
			':id' => $mainAttacker,
		), 'dpath');
		if (($combatResult['won'] == "a" && $theme == "SetSail")) {
			$sql = "UPDATE %%ADVANCED_STATS%% SET set_sail_wins = set_sail_wins + 1 WHERE userId = :userId";
			$db->update($sql, [
				':userId' => $mainAttacker,
			]);
		}
		BattleAchievement::checkBattleAchievements($reportInfo, $combatResult, $DATA);
	}
	if ($battleType == EXPO_FIGHT) {
        require_once 'includes/classes/achievements/BattleAchievement.class.php';
		BattleAchievement::checkExpoBattleAchievements($reportInfo, $combatResult);
	}
    return $DATA;
}
