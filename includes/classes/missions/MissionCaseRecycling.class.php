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

class MissionCaseRecycling extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        $pricelist =& Singleton()->pricelist;
        $resource =& Singleton()->resource;
        $resourceIDs = [901, 902, 903];
        $debrisIDs = [901, 902];
        $resQuery = [];
        $collectQuery = [];

        $collectedGoods = [];
        foreach ($debrisIDs as $debrisID) {
            $collectedGoods[$debrisID] = 0;
            $resQuery[] = 'der_' . $resource[$debrisID];
        }

        $sql = 'SELECT ' . implode(',', $resQuery) . ', (' . implode(' + ', $resQuery) . ') as total'
                . ' FROM %%PLANETS%% WHERE id = :planetId';

        $targetData = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($targetData == false) {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

        if (!empty($targetData['total'])) {
            // Get fleet capacity
            $fleetData = FleetFunctions::unserialize($this->_fleet['fleet_array']);

            $recyclerStorage = 0;
            $otherFleetStorage = 0;
            $recCount = 0;

            foreach ($fleetData as $shipId => $shipAmount) {
                if ($shipId == 209 ||  $shipId == 219) {
                    $recyclerStorage += $pricelist[$shipId]['capacity'] * $shipAmount;
                    $recCount += $shipAmount;
                } else {
                    $otherFleetStorage += $pricelist[$shipId]['capacity'] * $shipAmount;
                }
            }

            $incomingGoods = 0;
            foreach ($resourceIDs as $resourceID) {
                $incomingGoods += $this->_fleet['fleet_resource_' . $resource[$resourceID]];
            }

            $totalStorage = $recyclerStorage + min(0, $otherFleetStorage - $incomingGoods);

            $param = [
                ':planetId' => $this->_fleet['fleet_end_id'],
            ];

            // fast way
            $collectFactor = min(1, $totalStorage / $targetData['total']);
            foreach ($debrisIDs as $debrisID) {
                $fleetColName = 'fleet_resource_' . $resource[$debrisID];
                $logColName = 'fleet_gained_' . $resource[$debrisID];
                $debrisColName = 'der_' . $resource[$debrisID];

                $collectedGoods[$debrisID] = ceil($targetData[$debrisColName] * $collectFactor);
                $collectQuery[] = $debrisColName . ' = GREATEST(0, ' . $debrisColName . ' - :'
                    . $resource[$debrisID] . ')';
                $param[':' . $resource[$debrisID]] = $collectedGoods[$debrisID];

                $this->UpdateFleet($fleetColName, $this->_fleet[$fleetColName] + $collectedGoods[$debrisID]);
                $this->UpdateFleetLog($logColName, $collectedGoods[$debrisID]);
            }

            $sql = 'UPDATE %%PLANETS%% SET ' . implode(',', $collectQuery) . ' WHERE id = :planetId;';

            Database::get()->update($sql, $param);
            
        } else {
            $fleetData = FleetFunctions::unserialize($this->_fleet['fleet_array']);
            $recCount = 0;
            foreach ($fleetData as $shipId => $shipAmount) {
                if ($shipId == SHIP_RECYCLER ||  $shipId == GIGA_RECYKLER) {
                    $recCount += $shipAmount;
                }
            }
            require_once 'includes/classes/achievements/MiscAchievement.class.php';
            MiscAchievement::checkRecAchievements($this->_fleet['fleet_owner'], $collectedGoods, $recCount);
        }

        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $Message = sprintf(
            $LNG['sys_recy_gotten'],
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($collectedGoods[RESOURCE_METAL]),
            pretty_number($collectedGoods[RESOURCE_CRYSTAL]),
            pretty_number($targetData[$resQuery[0]]),
            pretty_number($targetData[$resQuery[1]]),
            $LNG['tech'][901],
            $LNG['tech'][902],
        );
        
        PlayerUtil::sendMessage(
            $this->_fleet['fleet_owner'],
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_recy_report'],
            $Message,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $sql = 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName = Database::get()->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_start_id'],
        ], 'name');

        $Message = sprintf(
            $LNG['sys_recy_mess'],
            $planetName,
            GetStartAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

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
