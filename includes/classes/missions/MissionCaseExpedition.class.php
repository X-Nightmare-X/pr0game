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

class MissionCaseExpedition extends MissionFunctions implements Mission
{
    private $LNG;
    private $logbook = '';

    public function __construct($fleet)
    {
        $this->_fleet = $fleet;
        $this->LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);
    }

    public function TargetEvent()
    {
        $this->setState(FLEET_HOLD);
        $this->UpdateFleet('fleet_no_m_return', 1);
        $this->SaveFleet();
    }

    private function calculatePointsAndCapacity()
    {
        global $pricelist, $reslist;

        $expeditionPoints = [];

        foreach ($reslist['fleet'] as $shipId) {
            $expeditionPoints[$shipId] = ($pricelist[$shipId]['cost'][RESOURCE_METAL]
                    + $pricelist[$shipId]['cost'][RESOURCE_CRYSTAL]) * 5 / 1000;
        }

        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
        $fleetPoints = 0;
        $fleetCapacity = 0;

        foreach ($fleetArray as $shipId => $shipAmount) {
            $fleetCapacity += $shipAmount * $pricelist[$shipId]['capacity'];
            $fleetPoints += $shipAmount * $expeditionPoints[$shipId];
        }

        $fleetCapacity -= $this->_fleet['fleet_resource_metal'] + $this->_fleet['fleet_resource_crystal']
            + $this->_fleet['fleet_resource_deuterium'];

        return [$fleetPoints, $fleetCapacity];
    }

    // 89 / 10 / 1%
    private function determineEventSize()
    {
        $eventSize = mt_rand(0, 99);

        if (10 < $eventSize) {
            $eventCategory = 0; // 89%
        } elseif (0 < $eventSize) {
            $eventCategory = 1; // 10%
        } else {
            $eventCategory = 2; // 1%
        }

        return $eventCategory;
    }

    private function handleEventFoundRes()
    {
        global $resource;

        $LNG = $this->LNG;
        $config = Config::get($this->_fleet['fleet_universe']);
        $factor = 0;

        $fleetData = $this->calculatePointsAndCapacity();
        $fleetPoints = $fleetData[0];
        $fleetCapacity = $fleetData[1];

        switch ($this->determineEventSize()) {
            case 0:
                $Message = $LNG['sys_expe_found_ress_1_' . mt_rand(1, 4)];
                $factor = mt_rand(10, 50);
                break;
            case 1:
                $Message = $LNG['sys_expe_found_ress_2_' . mt_rand(1, 3)];
                $factor = mt_rand(50, 100);
                break;
            case 2:
                $Message = $LNG['sys_expe_found_ress_3_' . mt_rand(1, 2)];
                $factor = mt_rand(100, 200);
                break;
        }

        $chanceToFound = mt_rand(1, 100);
        if ($chanceToFound <= $config->expo_ress_met_chance) {
            $resourceId = RESOURCE_METAL;
        } elseif ($chanceToFound <= $config->expo_ress_met_chance + $config->expo_ress_crys_chance) {
            $resourceId = RESOURCE_CRYSTAL;
            $factor = $factor / 2;
        } else {
            $resourceId = RESOURCE_DEUT;
            $factor = $factor / 3;
        }

        $foundResources = $factor * max(min($fleetPoints, FleetFunctions::calculateMaxFactorRes($this->_fleet['fleet_universe'])), 200);

        if ($fleetCapacity < $foundResources) {
            $foundResources = $fleetCapacity;
            $Message .= '<br><br>' . sprintf(
                    $LNG['sys_expe_found_goods'],
                    pretty_number($foundResources),
                    $LNG['tech'][$resourceId]
                );
            $this->logbook = $LNG['sys_expe_found_ress_logbook_' . mt_rand(1, 4)] . '<br>' . $this->logbook;
        } else {
            $Message .= '<br><br>' . sprintf(
                    $LNG['sys_expe_found_goods'],
                    pretty_number($foundResources),
                    $LNG['tech'][$resourceId]
                );
        }

        $fleetColName = 'fleet_resource_' . $resource[$resourceId];
        $this->UpdateFleet($fleetColName, $this->_fleet[$fleetColName] + $foundResources);
        $logColName = 'fleet_gained_' . $resource[$resourceId];
        $this->UpdateFleetLog($logColName, $foundResources);

        return $Message;
    }

    private function determineFindableShips($fleetArray)
    {
        $findableShips[SHIP_PROBE] = [210, 202];
        $findableShips[SHIP_SMALL_CARGO] = [210, 202, 204];
        $findableShips[SHIP_LIGHT_FIGHTER] = [210, 202, 204, 203];
        $findableShips[SHIP_LARGE_CARGO] = [210, 202, 204, 203, 205];
        $findableShips[SHIP_HEAVY_FIGHTER] = [210, 202, 204, 203, 205, 206];
        $findableShips[SHIP_CRUISER] = [210, 202, 204, 203, 205, 206, 207];
        $findableShips[SHIP_BATTLESHIP] = [210, 202, 204, 203, 205, 206, 207, 215];
        $findableShips[SHIP_BATTLECRUISER] = [210, 202, 204, 203, 205, 206, 207, 215, 211];
        $findableShips[SHIP_BOMBER] = [210, 202, 204, 203, 205, 206, 207, 215, 211, 213];
        $findableShips[SHIP_DESTROYER] = [210, 202, 204, 203, 205, 206, 207, 215, 211, 213];

        $highestShipId = SHIP_PROBE; // just in case someone uses a colony ship or something...

        $relevantShipIdsAsc = [SHIP_PROBE, SHIP_SMALL_CARGO, SHIP_LIGHT_FIGHTER, SHIP_LARGE_CARGO, SHIP_HEAVY_FIGHTER,
            SHIP_CRUISER, SHIP_BATTLESHIP, SHIP_BATTLECRUISER, SHIP_BOMBER, SHIP_DESTROYER];

        foreach ($relevantShipIdsAsc as $value) {
            if (array_key_exists($value, $fleetArray)) {
                $highestShipId = $value;
            }
        }

        $trueFindableShips = $findableShips[$highestShipId];
        return $trueFindableShips;
    }

    private function determineFoundShips($foundPoints, $findableShips)
    {
        global $pricelist;
        $Found = [];
        $upperValue = 3;

        $remainingPoints = $foundPoints;
        while ($findableShips != null && $upperValue > 0) {
            $ID = $findableShips[rand(0, count($findableShips) - 1)];
            $shipCost = ($pricelist[$ID]['cost'][RESOURCE_METAL] + $pricelist[$ID]['cost'][RESOURCE_CRYSTAL]);
            $MaxFound = floor($remainingPoints / $shipCost);
            $Count = mt_rand(0, $MaxFound);

            if ($MaxFound <= 0 || $Count <= 0) {
                $upperValue--;
                continue;
            }

            // determine how many shipX were found, subtract their points from $remainingPoints, break if negative
            if (array_key_exists($ID, $Found)) {
                $Found[$ID] += $Count;
            } else {
                $Found[$ID] = $Count;
            }
            $remainingPoints -= $Count * $shipCost;
            if ($remainingPoints <= 0) {
                break;
            }
        }

        return $Found;
    }

    private function handleEventFoundShips()
    {
        global $reslist;

        $LNG = $this->LNG;
        $Size = 0;
        $Message = "";
        $fleetPoints = $this->calculatePointsAndCapacity()[0]; // only points, capacity is irrelevant
        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        switch ($this->determineEventSize()) {
            case 0:
                $Size = mt_rand(10, 50);
                $Message = $LNG['sys_expe_found_ships_1_' . mt_rand(1, 4)];
                break;
            case 1:
                $Size = mt_rand(52, 100);
                $Message = $LNG['sys_expe_found_ships_2_' . mt_rand(1, 2)];
                break;
            case 2:
                $Size = mt_rand(102, 200);
                $Message = $LNG['sys_expe_found_ships_3_' . mt_rand(1, 2)];
                break;
        }

        $MaxPoints = FleetFunctions::calculateMaxFactor($this->_fleet['fleet_universe']);

        if ($fleetPoints < $MaxPoints) {
            $this->logbook = $LNG['sys_expe_found_ships_logbook_' . mt_rand(1, 3)] . '<br>' . $this->logbook;
        }

        $FoundShipMess = "";
        $NewFleetArray = "";
        $CountTotal = 0;
        $foundPoints = max(round($Size * min($fleetPoints, $MaxPoints)), 10000);
        $findableShips = $this->determineFindableShips($fleetArray);
        $Found = $this->determineFoundShips($foundPoints, $findableShips);


        foreach ($reslist['fleet'] as $ID) {
            $Count = 0;
            if (!empty($Found[$ID])) {
                $Count += $Found[$ID];
                $FoundShipMess .= '<br>' . $LNG['tech'][$ID] . ': ' . pretty_number($Count);
            }
            if (!empty($fleetArray[$ID])) {
                $Count += $fleetArray[$ID];
            }

            if ($Count > 0) {
                $NewFleetArray .= $ID . "," . floatToString($Count) . ';';
                $CountTotal += $Count;
            }
        }
        if (empty($Found)) {
            $Message .= '<br><br>' . $LNG['sys_expe_found_ships_nothing'];
        } else {
            $Message .= '<br><br>' . $LNG['sys_expe_back_home_ships_flound'] . $FoundShipMess;
        }

        $this->UpdateFleet('fleet_array', $NewFleetArray);
        $this->UpdateFleet('fleet_amount', $CountTotal);

        return $Message;
    }

    // pirate or alien. original: pirate chance = 58/84 ~ 69%
    private function setupPiratesOrAliens($fleetArray)
    {
        $LNG = $this->LNG;
        $targetFleetData = [];
        $pirates = (mt_rand(1, 100) <= 69);

        switch ($this->determineEventSize()) {
            case 0:
                $Message = $pirates
                    ? $LNG['sys_expe_attack_1_1_' . rand(1, 5)] : $LNG['sys_expe_attack_2_1_' . rand(1, 4)];
                $attackFactor = $pirates ? (30 + mt_rand(-3, 3)) / 100 : (40 + mt_rand(-4, 4)) / 100;
                $bonusShip = $pirates ? SHIP_LIGHT_FIGHTER : SHIP_HEAVY_FIGHTER;
                $targetFleetData[$bonusShip] = 5;
                break;
            case 1:
                $Message = $pirates
                    ? $LNG['sys_expe_attack_1_2_' . rand(1, 3)] : $LNG['sys_expe_attack_2_2_' . rand(1, 3)];
                $attackFactor = $pirates ? (50 + mt_rand(-5, 5)) / 100 : (60 + mt_rand(-6, 6)) / 100;
                $bonusShip = $pirates ? SHIP_CRUISER : SHIP_BATTLECRUISER;
                $targetFleetData[$bonusShip] = 3;
                break;
            case 2:
                $Message = $pirates
                    ? $LNG['sys_expe_attack_1_3_' . rand(1, 3)] : $LNG['sys_expe_attack_2_3_' . rand(1, 3)];
                $attackFactor = $pirates ? (80 + mt_rand(-8, 8)) / 100 : (90 + mt_rand(-9, 9)) / 100;
                $bonusShip = $pirates ? SHIP_BATTLESHIP : SHIP_DESTROYER;
                $targetFleetData[$bonusShip] = 2;
                break;
        }

        foreach ($fleetArray as $shipId => $shipAmount) {
            if (!isset($targetFleetData[$shipId])) {
                $targetFleetData[$shipId] = 0;
            }

            $rawShips = $shipAmount * $attackFactor;
            $targetFleetData[$shipId] += $pirates ? floor($rawShips) : ceil($rawShips);
        }

        $targetFleetData = array_filter($targetFleetData);

        $returnArray = [
            'message' => $Message,
            'fleetData' => $targetFleetData,
            'techbonus' => $pirates ? -3 : 3,
            'username' => $pirates ? $LNG['sys_expe_attackname_1'] : $LNG['sys_expe_attackname_2'],
        ];


        return $returnArray;
    }

    private function determineEnemyData($playerFleet)
    {
        $senderData = $playerFleet['player'];

        $encounterSetupData = $this->setupPiratesOrAliens($playerFleet['unit']);
        $techBonus = $encounterSetupData['techbonus'];
        $targetFleetData = $encounterSetupData['fleetData'];

        $targetData = [
            'id' => 0,
            'username' => $encounterSetupData['username'],
            'military_tech' => max($senderData['military_tech'] + $techBonus, 0),
            'shield_tech' => max($senderData['shield_tech'] + $techBonus, 0),
            'defence_tech' => max($senderData['defence_tech'] + $techBonus, 0),
        ];

        $fleetDefend = [];
        $fleetDefend[0]['fleetDetail'] = [
            'fleet_start_galaxy' => $this->_fleet['fleet_end_galaxy'],
            'fleet_start_system' => $this->_fleet['fleet_end_system'],
            'fleet_start_planet' => $this->_fleet['fleet_end_planet'],
            'fleet_start_type' => 1,
            'fleet_end_galaxy' => $this->_fleet['fleet_end_galaxy'],
            'fleet_end_system' => $this->_fleet['fleet_end_system'],
            'fleet_end_planet' => $this->_fleet['fleet_end_planet'],
            'fleet_end_type' => 1,
            'fleet_resource_metal' => 0,
            'fleet_resource_crystal' => 0,
            'fleet_resource_deuterium' => 0,
        ];

        $bonusList = BuildFunctions::getBonusList();

        $fleetDefend[0]['player'] = $targetData;
        $fleetDefend[0]['player']['factor'] = ArrayUtil::combineArrayWithSingleElement($bonusList, 0);
        $fleetDefend[0]['unit'] = $targetFleetData;

        return [$fleetDefend, $encounterSetupData['message']];
    }

    private function generateCombatReport($combatResult)
    {
        require_once('includes/classes/missions/functions/GenerateReport.php');

        $LNG = $this->LNG;
        $debris = [901 => 0, 902 => 0];
        $stealResource = [901 => 0, 902 => 0, 903 => 0];
        // test this.
        $messageHTML = <<<HTML
<div class="raportMessage">
    <table>
        <tr>
            <td colspan="2">
                <a href="CombatReport.php?raport=%s" target="_blank"><span class="%s">%s %s (%s)</span></a>
            </td>
        </tr>
        <tr>
            <td>%s</td>
            <td><span class="%s">%s: %s</span>&nbsp;<span class="%s">%s: %s</span></td>
        </tr>
        <tr>
            <td>%s</td>
            <td>
                <span>%s:&nbsp;<span class="raportSteal element901">%s</span>&nbsp;</span>
                <span>%s:&nbsp;<span class="raportSteal element902">%s</span>&nbsp;</span>
                <span>%s:&nbsp;<span class="raportSteal element903">%s</span></span>
            </td>
        </tr>
        <tr>
        <td>%s</td>
        <td>
            <span>%s:&nbsp;<span class="raportDebris element901">%s</span>&nbsp;</span>
            <span>%s:&nbsp;<span class="raportDebris element902">%s</span></span>
        </td>
        </tr>
    </table>
</div>
HTML;
        //Minize HTML
        $messageHTML = str_replace(["\n", "\t", "\r"], "", $messageHTML);

        $reportInfo = [
            'thisFleet' => $this->_fleet,
            'debris' => $debris,
            'stealResource' => $stealResource,
            'moonChance' => 0,
            'moonDestroy' => false,
            'moonName' => null,
            'moonDestroyChance' => null,
            'moonDestroySuccess' => null,
            'fleetDestroyChance' => null,
            'fleetDestroySuccess' => null,
        ];

        $reportData = GenerateReport($combatResult, $reportInfo);
        $reportID = md5(uniqid('', true) . TIMESTAMP);

        $sql = "INSERT INTO %%RW%% SET
			rid			= :reportId,
			raport		= :reportData,
			time		= :time,
			attacker    = :attacker;";

        Database::get()->insert($sql, [
            ':reportId' => $reportID,
            ':reportData' => serialize($reportData),
            ':time' => $this->_fleet['fleet_start_time'],
            ':attacker' => $this->_fleet['fleet_owner'],
        ]);

        switch ($combatResult['won']) {
            case "a":
                $attackClass = 'raportWin';
                $defendClass = 'raportLose';
                break;
            case "r":
                $attackClass = 'raportLose';
                $defendClass = 'raportWin';
                break;
            default:
                $attackClass = 'raportDraw';
                $defendClass = 'raportDraw';
                break;
        }

        $message = sprintf(
            $messageHTML,
            $reportID,
            $attackClass,
            $LNG['sys_mess_attack_report'],
            sprintf(
                $LNG['sys_adress_planet'],
                $this->_fleet['fleet_end_galaxy'],
                $this->_fleet['fleet_end_system'],
                $this->_fleet['fleet_end_planet']
            ),
            $LNG['type_planet_short_' . $this->_fleet['fleet_end_type']],
            $LNG['sys_lost'],
            $attackClass,
            $LNG['sys_attack_attacker_pos'],
            pretty_number($combatResult['unitLost']['attacker']),
            $defendClass,
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

        $result = [];
        $result['msg'] = $message;
        $result['link'] = sprintf(
            '<a href="CombatReport.php?raport=%s" target="_blank"> %s </a>',
            $reportID,
            $LNG['sys_mess_attack_report']
        );

        return $result;
    }

    private function handleEventCombat()
    {
        $LNG = $this->LNG;
        $config = Config::get($this->_fleet['fleet_universe']);
        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';

        $senderData = Database::get()->selectSingle($sql, [
            ':userId' => $this->_fleet['fleet_owner'],
        ]);

        $fleetID = $this->_fleet['fleet_id'];

        $fleetAttack = [];
        $fleetAttack[$fleetID]['fleetDetail'] = $this->_fleet;
        $fleetAttack[$fleetID]['player'] = $senderData;
        $fleetAttack[$fleetID]['unit'] = $fleetArray;

        // todo $Message as class variable? /discusswithself
        $enemyData = $this->determineEnemyData($fleetAttack[$fleetID]);
        $fleetDefend = $enemyData[0];
        $Message = $enemyData[1];

        require_once 'includes/classes/missions/functions/calculateAttack.php';
        $combatResult = calculateAttack($fleetAttack, $fleetDefend, $config->Fleet_Cdr, $config->Defs_Cdr);

        $fleetArray = '';
        $totalCount = 0;

        $fleetID = $this->_fleet['fleet_id'];
        $fleetAttack[$fleetID]['unit'] = array_filter($fleetAttack[$fleetID]['unit']);
        foreach ($fleetAttack[$fleetID]['unit'] as $element => $amount) {
            $fleetArray .= $element . ',' . $amount . ';';
            $totalCount += $amount;
        }

        if ($totalCount <= 0) {
            $this->KillFleet();
        } else {
            $this->UpdateFleet('fleet_array', substr($fleetArray, 0, -1));
            $this->UpdateFleet('fleet_amount', $totalCount);
            $fleetArray = FleetFunctions::unserialize($fleetArray);
        }

        $report = $this->generateCombatReport($combatResult);
        $Message .= '<br><br>' . $report['msg'];

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            3,
            $LNG['sys_mess_attack_report'],
            $report['msg'],
            $this->_fleet['fleet_end_stay']
        );

        return $Message;
    }

    private function chooseEvent()
    {
        $LNG = $this->LNG;
        // Get Expeditions count in this system
        $sql = "SELECT COUNT(*) AS total FROM %%LOG_FLEETS%% where `fleet_end_galaxy` = :fleet_end_galaxy"
            . " and `fleet_end_system` = :fleet_end_system and `fleet_end_planet` = :fleet_end_planet"
            . " and `fleet_end_stay` > UNIX_TIMESTAMP(NOW() - INTERVAL 1 DAY)";

        $expeditionsCount = Database::get()->selectSingle($sql, [
            'fleet_end_galaxy' => $this->_fleet['fleet_end_galaxy'],
            'fleet_end_system' => $this->_fleet['fleet_end_system'],
            'fleet_end_planet' => $this->_fleet['fleet_end_planet']
        ], 'total');

        // Hold time bonus
        $holdTime = ($this->_fleet['fleet_end_stay'] - $this->_fleet['fleet_start_time']) / 3600;

        $GetEvent = mt_rand(0, floor(1000 - $holdTime * 10));
        // Depletion check
        if ($expeditionsCount <= 10) {
            $chanceDepleted = 0;
            $this->logbook = $LNG['sys_expe_depleted_not_' . mt_rand(1, 2)];
        } elseif ($expeditionsCount <= 25) {
            $chanceDepleted = 25;
            $this->logbook = $LNG['sys_expe_depleted_min_' . mt_rand(1, 3)];
        } elseif ($expeditionsCount <= 50) {
            $chanceDepleted = 50;
            $this->logbook = $LNG['sys_expe_depleted_med_' . mt_rand(1, 3)];
        } else {
            $chanceDepleted = 75;
            $this->logbook = $LNG['sys_expe_depleted_max_' . mt_rand(1, 3)];
        }

        $depleted = mt_rand(0, 100);
        if ($depleted < $chanceDepleted) {
            $GetEvent = 1000; // nothing happens
        }

        return $GetEvent;
    }

    public function EndStayEvent()
    {
        $LNG = $this->LNG;
        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id'],
        ], 'name');
        $Message = sprintf(
            $LNG['sys_expe_report_msg'] . '<br><br>',
            $planetName,
            GetStartAddressLink($this->_fleet, '')
        );
        // Get a seed into the number generator (to make the results unpredictable).
        mt_srand(floor(microtime(true) * 10000));
        usleep(50);


        $GetEvent = $this->chooseEvent();

        if ($GetEvent < 370) {
            // Find resources: 37%. Values from http://owiki.de/Expedition + 4.5% compensation for dark matter
            $Message .= $this->handleEventFoundRes();
        } elseif ($GetEvent < 635) {
            // Find abandoned ships: 26.5%. Values from http://owiki.de/Expedition + 4.5% for dark matter
            $Message .= $this->handleEventFoundShips();
        } elseif ($GetEvent < 719) {
            // Find pirates or aliens: 8,4% - 69% (total 5.8%) pirates , 31% (2.6%) aliens.
            $Message .= $this->handleEventCombat();
        } elseif ($GetEvent < 722) {
            // Black hole: 0,3%
            $this->KillFleet();
            $Message .= $LNG['sys_expe_lost_fleet_' . mt_rand(1, 4)];
        } elseif ($GetEvent < 812) {
            // The fleet delays or return earlier: 9%
            # http://owiki.de/Expedition#Ver.C3.A4nderte_Flugzeit
            $chance = mt_rand(0, 100);
            $Wrapper = [2, 2, 2, 2, 2, 2, 2, 3, 3, 5];

            $normalBackTime = $this->_fleet['fleet_end_time'] - $this->_fleet['fleet_end_stay'];
            $stayTime = $this->_fleet['fleet_end_stay'] - $this->_fleet['fleet_start_time'];
            $factor = $Wrapper[mt_rand(0, 9)];

            if ($chance < 75) {
                // More return time
                $endTime = $this->_fleet['fleet_end_stay'] + $normalBackTime + $stayTime * $factor;
                $this->UpdateFleet('fleet_end_time', $endTime);
                $Message .= $LNG['sys_expe_time_slow_' . mt_rand(1, 6)];
            } else {
                // Less return time
                $endTime = $this->_fleet['fleet_end_stay'] + max(1, $normalBackTime - $stayTime / 3 * $factor);
                $this->UpdateFleet('fleet_end_time', $endTime);
                $Message .= $LNG['sys_expe_time_fast_' . mt_rand(1, 3)];
            }
        } else {
            $Message .= $LNG['sys_expe_nothing_' . mt_rand(1, 8)]; // default
        }

        if (isset($fleetArray[SHIP_PROBE])) {
            $Message .= '<br><br>' . $this->logbook;
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            15,
            $LNG['sys_expe_report'],
            $Message,
            $this->_fleet['fleet_end_stay'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function ReturnEvent()
    {
        global $pricelist, $reslist;
        $LNG = $this->LNG;
        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id'],
        ], 'name');
        $Message = sprintf(
            $LNG['sys_expe_back_home'],
            $planetName,
            GetStartAddressLink($this->_fleet, '')
        );

        if (
            $this->_fleet['fleet_resource_metal'] > 0
            || $this->_fleet['fleet_resource_crystal'] > 0
            || $this->_fleet['fleet_resource_deuterium'] > 0
        ) {
            $Message .= '<br><br>' . sprintf(
                    $LNG['sys_expe_back_home_ress'],
                    pretty_number($this->_fleet['fleet_resource_metal']),
                    $LNG['tech'][901],
                    pretty_number($this->_fleet['fleet_resource_crystal']),
                    $LNG['tech'][902],
                    pretty_number($this->_fleet['fleet_resource_deuterium']),
                    $LNG['tech'][903]
                );
        }

        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
        $fleetArrayStart = FleetFunctions::unserialize($this->_fleet['fleet_start_array']);

        // correct empty fleets resulting from a fixed bug in findShips event
        if (empty($fleetArray) && !empty($fleetArrayStart)) {
            $this->_fleet['fleet_array'] = $this->_fleet['fleet_start_array'];
            $this->UpdateFleet('fleet_array', $this->_fleet['fleet_start_array']);
            //impossible when fleet is killed
            $this->SaveFleet();
        }

        if (isset($fleetArrayStart)) {
            $fleetAmmount = array_sum($fleetArray);
            $fleetAmmountStart = array_sum($fleetArrayStart);
            if ($fleetAmmountStart != $fleetAmmount) {
                if ($fleetAmmountStart < $fleetAmmount) {
                    $Message .= '<br><br>' . sprintf(
                            $LNG['sys_expe_back_home_ships_flound']
                        );
                } elseif ($fleetAmmountStart > $fleetAmmount) {
                    $Message .= '<br><br>' . sprintf(
                            $LNG['sys_expe_back_home_ships_lost']
                        );
                }
                foreach ($reslist['fleet'] as $shipId) {
                    if (isset($fleetArrayStart[$shipId]) && isset($fleetArray[$shipId])) {
                        if ($fleetArrayStart[$shipId] != $fleetArray[$shipId]) {
                            $Message .= '<br>' . $LNG['tech'][$shipId] . ': '
                                . pretty_number(abs($fleetArrayStart[$shipId] - $fleetArray[$shipId]));
                        }
                    } elseif (isset($fleetArrayStart[$shipId])) {
                        $Message .= '<br>' . $LNG['tech'][$shipId] . ': '
                            . pretty_number(abs($fleetArrayStart[$shipId]));
                    } elseif (isset($fleetArray[$shipId])) {
                        $Message .= '<br>' . $LNG['tech'][$shipId] . ': ' . pretty_number(abs($fleetArray[$shipId]));
                    }
                }
            }
        }

        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            4,
            $LNG['sys_mess_fleetback'],
            $Message,
            $this->_fleet['fleet_end_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->RestoreFleet();
    }
}
