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

class MissionCaseDestruction extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        $resource =& Singleton()->resource;
        $reslist =& Singleton()->reslist;
        $db = Database::get();

        $fleetAttack = [];
        $fleetDefend = [];

        $userAttack = [];
        $userDefend = [];

        $incomingFleets = [];

        $stealResource = [
            901 => 0,
            902 => 0,
            903 => 0,
        ];

        $debris = [];
        $planetDebris = [];

        $debrisResource = [901, 902];

        $messageHTML = <<<HTML
<div class="raportMessage">
	<table>
		<tr>
			<td colspan="2">
			<a href="game.php?page=raport&raport=%s" target="_blank"><span class="%s">%s %s (%s)</span></a>
			</td>
		</tr>
		<tr>
			<td>%s</td><td><span class="%s">%s: %s</span>&nbsp;<span class="%s">%s: %s</span></td>
		</tr>
		<tr>
			<td>%s</td>
			<td>
                <span>%s:&nbsp;<span class="reportSteal element901">%s</span>&nbsp;</span>
                <span>%s:&nbsp;<span class="reportSteal element902">%s</span>&nbsp;</span>
                <span>%s:&nbsp;<span class="reportSteal element903">%s</span></span>
			</td>
		</tr>
		<tr>
			<td>%s</td>
			<td>
                <span>%s:&nbsp;<span class="reportDebris element901">%s</span>&nbsp;</span>
                <span>%s:&nbsp;<span class="reportDebris element902">%s</span></span>
			</td>
		</tr>
	</table>
</div>
HTML;
        //Minize HTML
        $messageHTML = str_replace(["\n", "\t", "\r"], "", $messageHTML);

        $sql = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
        $targetPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($targetPlanet == false) {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

        // select wreckfield to lock it, can be empty
        if ($targetPlanet['planet_type'] == 3) {
            $sql = "SELECT * FROM %%PLANETS%% WHERE id_luna = :planetId;";
            $targetPlanetID = $db->selectSingle($sql, [
                ':planetId' => $this->_fleet['fleet_end_id']
            ], 'id');
        } else {
            $targetPlanetID = $targetPlanet['id'];
        }
        $sql = "SELECT * FROM %%PLANET_WRECKFIELD%% WHERE planetId = :planetId FOR UPDATE;";
        $db->selectSingle($sql, [
            ':planetId' => $targetPlanetID
        ]);

        $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
        $targetUser = $db->selectSingle($sql, [
            ':userId'   => $targetPlanet['id_owner'],
        ]);

        $planetUpdater = new ResourceUpdate();

        list($targetUser, $targetPlanet) = $planetUpdater->CalcResource(
            $targetUser,
            $targetPlanet,
            true,
            $this->_fleet['fleet_start_time']
        );

        if ($this->_fleet['fleet_group'] != 0) {
            $sql = "DELETE FROM %%AKS%% WHERE id = :acsId;";
            $db->delete($sql, [
                ':acsId'    => $this->_fleet['fleet_group'],
            ]);

            $sql = "SELECT * FROM %%FLEETS%% WHERE fleet_group = :acsId FOR UPDATE;";

            $incomingFleetsResult = $db->select($sql, [
                ':acsId'    => $this->_fleet['fleet_group'],
            ]);

            foreach ($incomingFleetsResult as $incomingFleetRow) {
                $incomingFleets[$incomingFleetRow['fleet_id']] = $incomingFleetRow;
            }

            unset($incomingFleetsResult);
        } else {
            $incomingFleets = [$this->_fleet['fleet_id'] => $this->_fleet];
        }

        foreach ($incomingFleets as $fleetID => $fleetDetail) {
            $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
            $fleetAttack[$fleetID]['player'] = $db->selectSingle($sql, [
                ':userId'   => $fleetDetail['fleet_owner']
            ]);

            $fleetAttack[$fleetID]['fleetDetail'] = $fleetDetail;
            $fleetAttack[$fleetID]['unit'] = FleetFunctions::unserialize($fleetDetail['fleet_array']);

            $userAttack[$fleetAttack[$fleetID]['player']['id']] = $fleetAttack[$fleetID]['player']['username'];
        }

        $sql = "SELECT * FROM %%FLEETS%%
		WHERE fleet_mission		= :mission
		AND fleet_end_id		= :fleetEndId
		AND fleet_start_time    <= :timeStamp
		AND fleet_end_stay      >= :timeStamp FOR UPDATE;";

        $targetFleetsResult = $db->select($sql, [
            ':mission'      => MISSION_HOLD,
            ':fleetEndId'   => $this->_fleet['fleet_end_id'],
            ':timeStamp'    => TIMESTAMP
        ]);

        foreach ($targetFleetsResult as $fleetDetail) {
            $fleetID = $fleetDetail['fleet_id'];

            $sql = "SELECT * FROM %%USERS%% WHERE id = :userId;";
            $fleetDefend[$fleetID]['player'] = $db->selectSingle($sql, [
                ':userId'   => $fleetDetail['fleet_owner'],
            ]);

            $fleetDefend[$fleetID]['fleetDetail'] = $fleetDetail;
            $fleetDefend[$fleetID]['unit'] = FleetFunctions::unserialize($fleetDetail['fleet_array']);

            $userDefend[$fleetDefend[$fleetID]['player']['id']] = $fleetDefend[$fleetID]['player']['username'];
        }

        unset($targetFleetsResult);

        $fleetDefend[0]['player'] = $targetUser;
        $fleetDefend[0]['fleetDetail'] = [
            'fleet_start_galaxy'    => $targetPlanet['galaxy'],
            'fleet_start_system'    => $targetPlanet['system'],
            'fleet_start_planet'    => $targetPlanet['planet'],
            'fleet_start_type'      => $targetPlanet['planet_type'],
        ];

        $fleetDefend[0]['unit'] = [];

        foreach (array_merge($reslist['fleet'], $reslist['defense']) as $elementID) {
            if (empty($targetPlanet[$resource[$elementID]])) {
                continue;
            }

            $fleetDefend[0]['unit'][$elementID] = $targetPlanet[$resource[$elementID]];
        }

        $userDefend[$fleetDefend[0]['player']['id']] = $fleetDefend[0]['player']['username'];

        require_once 'includes/classes/missions/functions/calculateAttack.php';

        $fleetIntoDebris = Config::get($this->_fleet['fleet_universe'])->fleet_debris_percentage;
        $defIntoDebris = Config::get($this->_fleet['fleet_universe'])->def_debris_percentage;

        $combatResult = calculateAttack($fleetAttack, $fleetDefend, $fleetIntoDebris, $defIntoDebris);

        foreach ($fleetAttack as $fleetID => $fleetDetail) {
            $fleetArray = '';
            $totalCount = 0;

            $fleetDetail['unit'] = array_filter($fleetDetail['unit']);
            foreach ($fleetDetail['unit'] as $elementID => $amount) {
                $fleetArray .= $elementID . ',' . floatToString($amount) . ';';
                $totalCount += $amount;
            }

            if ($totalCount == 0) {
                if ($this->_fleet['fleet_id'] == $fleetID) {
                    $this->KillFleet();
                } else {
                    $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
					FROM %%FLEETS%%
					INNER JOIN %%FLEETS_EVENT%% ON fleetID = fleet_id
					WHERE fleet_id = :fleetId;';

                    $db->delete($sql, [
                        ':fleetId'  => $fleetID,
                    ]);
                }

                $sql = 'UPDATE %%LOG_FLEETS%% SET fleet_state = :fleetState WHERE fleet_id = :fleetId;';
                $db->update($sql, [
                    ':fleetId'      => $fleetID,
                    ':fleetState'   => FLEET_HOLD,
                ]);

                unset($fleetAttack[$fleetID]);
            } elseif ($totalCount > 0) {
                $sql = "UPDATE %%FLEETS%% fleet, %%LOG_FLEETS%% log SET
				fleet.fleet_array   = :fleetData,
				fleet.fleet_amount  = :fleetCount,
				log.fleet_array		= :fleetData,
				log.fleet_amount    = :fleetCount
				WHERE fleet.fleet_id = :fleetId AND log.fleet_id = :fleetId;";

                $db->update($sql, [
                    ':fleetData'    => substr($fleetArray, 0, -1),
                    ':fleetCount'   => $totalCount,
                    ':fleetId'      => $fleetID
                ]);
            } else {
                throw new OutOfRangeException("Negative Fleet amount ....");
            }
        }

        foreach ($fleetDefend as $fleetID => $fleetDetail) {
            if ($fleetID != 0) {
                // Stay fleet
                $fleetArray = '';
                $totalCount = 0;

                $fleetDetail['unit'] = array_filter($fleetDetail['unit']);

                foreach ($fleetDetail['unit'] as $elementID => $amount) {
                    $fleetArray .= $elementID . ',' . floatToString($amount) . ';';
                    $totalCount += $amount;
                }

                if ($totalCount == 0) {
                    $sql = 'DELETE %%FLEETS%%, %%FLEETS_EVENT%%
					FROM %%FLEETS%%
					INNER JOIN %%FLEETS_EVENT%% ON fleetID = fleet_id
					WHERE fleet_id = :fleetId;';

                    $db->delete($sql, [
                        ':fleetId'  => $fleetID
                    ]);

                    $sql = 'UPDATE %%LOG_FLEETS%% SET fleet_state = :fleetState WHERE fleet_id = :fleetId;';
                    $db->update($sql, [
                        ':fleetId'      => $fleetID,
                        ':fleetState'   => FLEET_HOLD,
                    ]);

                    unset($fleetDefend[$fleetID]);
                } elseif ($totalCount > 0) {
                    $sql = "UPDATE %%FLEETS%% fleet, %%LOG_FLEETS%% log SET
					fleet.fleet_array   = :fleetData,
					fleet.fleet_amount  = :fleetCount,
					log.fleet_array		= :fleetData,
					log.fleet_amount    = :fleetCount
					WHERE fleet.fleet_id = :fleetId AND log.fleet_id = :fleetId;";

                    $db->update($sql, [
                        ':fleetData'    => substr($fleetArray, 0, -1),
                        ':fleetCount'   => $totalCount,
                        ':fleetId'      => $fleetID
                    ]);
                } else {
                    throw new OutOfRangeException("Negative Fleet amount ....");
                }
            } else {
                $params = [':planetId' => $this->_fleet['fleet_end_id']];

                // Planet fleet
                $fleetArray = [];
                foreach ($fleetDetail['unit'] as $elementID => $amount) {
                    $fleetArray[] = '`' . $resource[$elementID] . '` = :' . $resource[$elementID];
                    $params[':' . $resource[$elementID]] = $amount;
                }

                if (!empty($fleetArray)) {
                    $sql = 'UPDATE %%PLANETS%% SET ' . implode(', ', $fleetArray) . ' WHERE id = :planetId;';
                    $db->update($sql, $params);
                }
            }
        }

        if ($combatResult['won'] == "a") {
            require_once 'includes/classes/missions/functions/calculateSteal.php';
            $stealResource = calculateSteal($fleetAttack, $targetPlanet, $this->_fleet['fleet_universe']);
        }

        if ($this->_fleet['fleet_end_type'] == 3) {
            // Use planet debris, if attack on moons
            $sql = "SELECT der_metal, der_crystal FROM %%PLANETS%% WHERE id_luna = :moonId;";
            $targetDebris = $db->selectSingle($sql, [
                ':moonId'   => $this->_fleet['fleet_end_id']
            ]);
            $targetPlanet['der_metal'] += $targetDebris['der_metal'];
            $targetPlanet['der_crystal'] += $targetDebris['der_crystal'];
        }

        foreach ($debrisResource as $elementID) {
            $debris[$elementID] = $combatResult['debris']['attacker'][$elementID]
                + $combatResult['debris']['defender'][$elementID];
            $planetDebris[$elementID] = $targetPlanet['der_' . $resource[$elementID]] + $debris[$elementID];
        }

        $reportInfo = [
            'thisFleet'             => $this->_fleet,
            'debris'                => $debris,
            'stealResource'         => $stealResource,
            'moonChance'            => null,
            'additionalChance'      => 0,
            'moonDestroy'           => true,
            'moonName'              => null,
            'moonDestroyChance'     => null,
            'moonDestroySuccess'    => null,
            'fleetDestroyChance'    => null,
            'fleetDestroySuccess'   => false,
        ];

        switch ($combatResult['won']) {
            // Win
            case "a":
                $moonDestroyChance = round(
                    (100 - sqrt($targetPlanet['diameter']))
                        * sqrt($fleetAttack[$this->_fleet['fleet_id']]['unit'][214]),
                    1
                );

                // Max 100% | Min 0%
                $moonDestroyChance = min($moonDestroyChance, 100);
                $moonDestroyChance = max($moonDestroyChance, 0);

                $randChance = mt_rand(1, 100);
                if ($randChance <= $moonDestroyChance) {
                    $sql = 'SELECT id FROM %%PLANETS%% WHERE id_luna = :moonId;';
                    $planetID = $db->selectSingle($sql, [
                        ':moonId'   => $targetPlanet['id'],
                    ], 'id');


                    $sql = 'UPDATE %%FLEETS%% SET
					fleet_start_type		= 1,
					fleet_start_id			= :planetId
					WHERE fleet_start_id    = :moonId;';

                    $db->update($sql, [
                        ':planetId' => $planetID,
                        ':moonId'   => $targetPlanet['id'],
                    ]);

                    $sql = 'UPDATE %%FLEETS%% SET
					fleet_end_type	= 1,
					fleet_end_id	= :planetId,
					fleet_mission	= IF(fleet_mission = :dest, 1, fleet_mission)
					WHERE fleet_end_id = :moonId
					AND fleet_id != :fleetId;';

                    $db->update($sql, [
                        ':planetId' => $planetID,
                        ':moonId'   => $targetPlanet['id'],
                        ':fleetId'  => $this->_fleet['fleet_id'],
                        ':dest'     => MISSION_DESTRUCTION,
                    ]);

                    $sql = "UPDATE %%AKS%% SET target = :planetId WHERE target = :moonId;";
                    $db->update($sql, [
                        ':planetId' => $planetID,
                        ':moonId'   => $targetPlanet['id'],
                    ]);

                    // Redirect fleets from moon to player's main planet.
                    $db->update(
                        "UPDATE %%FLEETS%% SET fleet_start_id = :main_id, fleet_start_galaxy = :main_galaxy, "
                            . "fleet_start_system = :main_system, fleet_start_planet = :main_planet, "
                            . "fleet_start_type = 1 WHERE fleet_start_id = :destroyed",
                        [
                            ':main_id' => $targetUser['id_planet'],
                            ':main_galaxy' => $targetUser['galaxy'],
                            ':main_system' => $targetUser['system'],
                            ':main_planet' => $targetUser['planet'],
                            ':destroyed' => $targetPlanet['id'],
                        ]
                    );

                    $reportInfo['moonDestroySuccess'] = 1;
                } else {
                    $reportInfo['moonDestroySuccess'] = 0;
                }

                $fleetDestroyChance = round(sqrt($targetPlanet['diameter']) / 2);

                $randChance = mt_rand(1, 100);
                if ($randChance <= $fleetDestroyChance) {
                    MissionFunctions::updateLostAdvancedStats($this->_fleet['fleet_owner'], $fleetAttack[$this->_fleet['fleet_id']]['unit']);
                    $this->KillFleet();
                    $reportInfo['fleetDestroySuccess'] = true;
                } else {
                    $reportInfo['fleetDestroySuccess'] = false;
                }

                $reportInfo['moonDestroyChance'] = $moonDestroyChance;
                $reportInfo['fleetDestroyChance'] = $fleetDestroyChance;

                $attackStatus = 'wons';
                $defendStatus = 'loos';
                $class = ['raportWin', 'raportLose'];
                break;
            case "r":
                // Lose
                $attackStatus = 'loos';
                $defendStatus = 'wons';
                $class = ['raportLose', 'raportWin'];
                $reportInfo['moonDestroySuccess'] = -1;
                break;
            default:
                // Draw
                $attackStatus = 'draws';
                $defendStatus = 'draws';
                $class = ['raportDraw', 'raportDraw'];
                $reportInfo['moonDestroySuccess'] = -1;
                break;
        }

        require_once 'includes/classes/missions/functions/GenerateReport.php';
        $reportData = GenerateReport($combatResult, $reportInfo, REAL_FIGHT);

        require_once('includes/classes/missions/functions/GenerateWreckField.php');
        GenerateWreckField($targetPlanetID, $combatResult);

        $reportID = md5(uniqid('', true) . TIMESTAMP);

        $sql = 'INSERT INTO %%RW%% SET
		rid 		= :reportId,
		raport 		= :reportData,
		time 		= :time,
		attacker    = :attackers,
		defender    = :defenders;';

        $db->insert($sql, [
            ':reportId'     => $reportID,
            ':reportData'   => serialize($reportData),
            ':time'         => $this->_fleet['fleet_start_time'],
            ':attackers'    => implode(',', array_keys($userAttack)),
            ':defenders'    => implode(',', array_keys($userDefend)),
        ]);

        $i = 0;
        $units = $combatResult['unitLost']['attacker'] + $combatResult['unitLost']['defender'];
        $universe = $this->_fleet['fleet_universe'];
        $topkb = $this->checkForTopKB($units, $universe);

        foreach ([$userAttack, $userDefend] as $data) {
            foreach ($data as $userID => $userName) {
                $LNG = $this->getLanguage(null, $userID);

                $message = sprintf(
                    $messageHTML,
                    $reportID,
                    $class[$i],
                    $LNG['sys_mess_attack_report'],
                    sprintf(
                        $LNG['sys_adress_planet'],
                        $this->_fleet['fleet_end_galaxy'],
                        $this->_fleet['fleet_end_system'],
                        $this->_fleet['fleet_end_planet']
                    ),
                    $LNG['type_planet_short_' . $this->_fleet['fleet_end_type']],
                    $LNG['sys_lost'],
                    $class[0],
                    $LNG['sys_attack_attacker_pos'],
                    pretty_number($combatResult['unitLost']['attacker']),
                    $class[1],
                    $LNG['sys_attack_defender_pos'],
                    pretty_number($combatResult['unitLost']['defender']),
                    $LNG['sys_gain'],
                    $LNG['tech'][901],
                    pretty_number($stealResource[901]),
                    $LNG['tech'][902],
                    pretty_number($stealResource[902]),
                    $LNG['tech'][903],
                    pretty_number($stealResource[903]),
                    $LNG['sys_debris'],
                    $LNG['tech'][901],
                    pretty_number($debris[901]),
                    $LNG['tech'][902],
                    pretty_number($debris[902])
                );

                PlayerUtil::sendMessage(
                    $userID,
                    0,
                    $LNG['sys_mess_tower'],
                    3,
                    $LNG['sys_mess_attack_report'],
                    $message,
                    $this->_fleet['fleet_start_time'],
                    null,
                    1,
                    $this->_fleet['fleet_universe']
                );

                if ($topkb) {
                    $sql = "INSERT INTO %%TOPKB_USERS%% SET
                    rid			= :reportId,
                    role		= :userRole,
                    username    = :username,
                    uid			= :userId;";

                    $db->insert($sql, [
                        ':reportId' => $reportID,
                        ':userRole' => $i + 1,
                        ':username' => $userName,
                        ':userId'   => $userID,
                    ]);
                }
            }

            $i++;
        }

        if ($this->_fleet['fleet_end_type'] == 3) {
            $debrisType = 'id_luna';
        } else {
            $debrisType = 'id';
        }

        $sql = 'UPDATE %%PLANETS%% SET
        der_metal	= :metal,
        der_crystal	= :crystal,
        tf_active = :new_debris
        WHERE ' . $debrisType . ' = :planetId;';

        $db->update($sql, [
            ':metal'    => $planetDebris[901],
            ':crystal'  => $planetDebris[902],
            ':new_debris' => 1,
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        if ($reportInfo['moonDestroySuccess'] == 1) {
            PlayerUtil::deletePlanet($targetPlanet['id']);
        } else {
            $sql = 'UPDATE %%PLANETS%% SET
            metal		= metal - :metal,
            crystal		= crystal - :crystal,
            deuterium   = deuterium - :deuterium
            WHERE id = :planetId;';

            $db->update($sql, [
                ':metal'        => $stealResource[901],
                ':crystal'      => $stealResource[902],
                ':deuterium'    => $stealResource[903],
                ':planetId'     => $this->_fleet['fleet_end_id'],
            ]);
        }

        if ($topkb) {
        $sql = 'INSERT INTO %%TOPKB%% SET
            units 		= :units,
            rid			= :reportId,
            time		= :time,
            universe    = :universe,
            result		= :result;';

            $db->insert($sql, [
                ':units'    => $units,
                ':reportId' => $reportID,
                ':time'     => $this->_fleet['fleet_start_time'],
                ':universe' => $this->_fleet['fleet_universe'],
                ':result'   => $combatResult['won'],
            ]);
        }

        $sql = 'UPDATE %%USERS%% SET
		`' . $attackStatus . '` = `' . $attackStatus . '` + 1,
		kbmetal		= kbmetal + :debrisMetal,
		kbcrystal   = kbcrystal + :debrisCrystal,
		lostunits   = lostunits + :lostUnits,
		desunits    = desunits + :destroyedUnits
		WHERE id IN (' . implode(',', array_keys($userAttack)) . ');';

        $db->update($sql, [
            ':debrisMetal'      => $debris[901],
            ':debrisCrystal'    => $debris[902],
            ':lostUnits'        => $combatResult['unitLost']['attacker'],
            ':destroyedUnits'   => $combatResult['unitLost']['defender'],
        ]);

        $sql = 'UPDATE %%USERS%% SET
		`' . $defendStatus . '` = `' . $defendStatus . '` + 1,
		kbmetal		= kbmetal + :debrisMetal,
		kbcrystal   = kbcrystal + :debrisCrystal,
		lostunits   = lostunits + :lostUnits,
		desunits    = desunits + :destroyedUnits
		WHERE id IN (' . implode(',', array_keys($userDefend)) . ');';

        $db->update($sql, [
            ':debrisMetal'      => $debris[901],
            ':debrisCrystal'    => $debris[902],
            ':lostUnits'        => $combatResult['unitLost']['defender'],
            ':destroyedUnits'   => $combatResult['unitLost']['attacker'],
        ]);

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $this->sendAttackReturnMessage();

        $this->RestoreFleet();
    }
}
