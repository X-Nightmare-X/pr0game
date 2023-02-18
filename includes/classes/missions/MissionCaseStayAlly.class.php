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

class MissionCaseStayAlly extends MissionFunctions implements Mission
{
    public function __construct($Fleet)
    {
        $this->_fleet	= $Fleet;
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

        $this->setState(FLEET_HOLD);
        $this->SaveFleet();
    }

    public function EndStayEvent()
    {
        $this->setState(FLEET_RETURN);
        $this->SaveFleet();
    }

    public function ReturnEvent()
    {
        $LNG		= $this->getLanguage(null, $this->_fleet['fleet_owner']);
        $sql		= 'SELECT name FROM %%PLANETS%% WHERE id = :planetId;';
        $planetName	= Database::get()->selectSingle($sql, [
            ':planetId'	=> $this->_fleet['fleet_start_id'],
        ], 'name');

        $Message	= sprintf($LNG['sys_tran_mess_back'], $planetName, GetStartAddressLink($this->_fleet, ''));
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
