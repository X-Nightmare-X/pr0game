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

class MissionCaseStay extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet = $Fleet;
    }

    public function TargetEvent()
    {
        $db = Database::get();

        $sql = 'SELECT * FROM %%PLANETS%% WHERE id = :planetId;';
        $targetPlanet = $db->selectSingle($sql, [
            ':planetId' => $this->_fleet['fleet_end_id'],
        ]);

        // return fleet if target planet deleted
        if ($targetPlanet == false) {
            $this->setState(FLEET_RETURN);
            $this->SaveFleet();
            return;
        }

        $sql = 'SELECT * FROM %%USERS%% WHERE id = :userId;';
        $senderUser = $db->selectSingle($sql, [
            ':userId'   => $this->_fleet['fleet_owner']
        ]);

        $fleetArray = FleetFunctions::unserialize($this->_fleet['fleet_array']);
        $duration = $this->_fleet['fleet_start_time'] - $this->_fleet['start_time'];

        $SpeedFactor = FleetFunctions::getGameSpeedFactor();
        $distance = FleetFunctions::getTargetDistance(
            [
                $this->_fleet['fleet_start_galaxy'],
                $this->_fleet['fleet_start_system'],
                $this->_fleet['fleet_start_planet']
            ],
            [
                $this->_fleet['fleet_end_galaxy'],
                $this->_fleet['fleet_end_system'],
                $this->_fleet['fleet_end_planet']
            ]
        );

        $consumption = FleetFunctions::getFleetConsumption(
            $fleetArray,
            $duration,
            $distance,
            $senderUser,
            $SpeedFactor
        );

        $this->UpdateFleet('fleet_resource_deuterium', $this->_fleet['fleet_resource_deuterium'] + $consumption / 2);

        $LNG = $this->getLanguage($senderUser['lang']);
        $TargetUserID = $this->_fleet['fleet_target_owner'];
        $TargetMessage = sprintf(
            $LNG['sys_stat_mess'],
            $LNG['type_planet_' . $this->_fleet['fleet_end_type']],
            GetTargetAddressLink($this->_fleet, ''),
            pretty_number($this->_fleet['fleet_resource_metal']),
            $LNG['tech'][901],
            pretty_number($this->_fleet['fleet_resource_crystal']),
            $LNG['tech'][902],
            pretty_number($this->_fleet['fleet_resource_deuterium']),
            $LNG['tech'][903]
        );

        PlayerUtil::sendMessage(
            $TargetUserID,
            0,
            $LNG['sys_mess_tower'],
            5,
            $LNG['sys_stat_mess_stay'],
            $TargetMessage,
            $this->_fleet['fleet_start_time'],
            null,
            1,
            $this->_fleet['fleet_universe']
        );

        $this->RestoreFleet(false);
    }

    public function EndStayEvent()
    {
        return;
    }

    public function ReturnEvent()
    {
        $LNG = $this->getLanguage(null, $this->_fleet['fleet_owner']);

        $Message = sprintf(
            $LNG['sys_stat_mess'],
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
