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

class ShowFleetStep2Page extends AbstractGamePage
{
    public static $requireModule = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    public function show()
    {
        global $USER, $PLANET, $LNG;

        $this->tplObj->loadscript('flotten.js');

        $targetGalaxy = HTTP::_GP('galaxy', 0);
        $targetSystem = HTTP::_GP('system', 0);
        $targetPlanet = HTTP::_GP('planet', 0);
        $targetType = HTTP::_GP('type', 0);
        $targetMission = HTTP::_GP('target_mission', 0);
        $fleetSpeed = HTTP::_GP('speed', 0);
        $fleetGroup = HTTP::_GP('fleet_group', 0);
        $token = HTTP::_GP('token', '');

        if (!isset($_SESSION['fleet'][$token])) {
            FleetFunctions::gotoFleetPage();
        }

        $fleetArray = $_SESSION['fleet'][$token]['fleet'];

        $db = Database::get();
        $sql = "SELECT id, id_owner, der_metal, der_crystal FROM %%PLANETS%% WHERE universe = :universe AND"
            . " galaxy = :targetGalaxy AND system = :targetSystem AND planet = :targetPlanet AND planet_type = '1';";
        $targetPlanetData = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':targetGalaxy' => $targetGalaxy,
            ':targetSystem' => $targetSystem,
            ':targetPlanet' => $targetPlanet
        ]);

        if ($targetType == 2 && $targetPlanetData['der_metal'] == 0 && $targetPlanetData['der_crystal'] == 0) {
            $this->printMessage($LNG['fl_error_empty_derbis'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable'
            ]]);
        }

        $MisInfo = [];
        $MisInfo['galaxy'] = $targetGalaxy;
        $MisInfo['system'] = $targetSystem;
        $MisInfo['planet'] = $targetPlanet;
        $MisInfo['planettype'] = $targetType;
        $MisInfo['IsAKS'] = $fleetGroup;
        $MisInfo['Ship'] = $fleetArray;

        $MissionOutput = FleetFunctions::getFleetMissions($USER, $MisInfo, $targetPlanetData);

        if (empty($MissionOutput['MissionSelector'])) {
            $this->printMessage($LNG['fl_empty_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable'
            ]]);
        }

        $GameSpeedFactor = FleetFunctions::getGameSpeedFactor();
        $MaxFleetSpeed = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $distance = FleetFunctions::getTargetDistance(
            [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
            [$targetGalaxy, $targetSystem, $targetPlanet]
        );
        $duration = FleetFunctions::getMissionDuration($fleetSpeed, $MaxFleetSpeed, $distance, $GameSpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $duration, $distance, $USER, $GameSpeedFactor);

        if ($consumption > $PLANET['deuterium']) {
            $this->printMessage($LNG['fl_not_enough_deuterium'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable'
            ]]);
        }

        if (!FleetFunctions::checkUserSpeed($fleetSpeed)) {
            FleetFunctions::gotoFleetPage(0);
        }

        $_SESSION['fleet'][$token]['speed']         = $MaxFleetSpeed;
        $_SESSION['fleet'][$token]['distance']      = $distance;
        $_SESSION['fleet'][$token]['targetGalaxy']  = $targetGalaxy;
        $_SESSION['fleet'][$token]['targetSystem']  = $targetSystem;
        $_SESSION['fleet'][$token]['targetPlanet']  = $targetPlanet;
        $_SESSION['fleet'][$token]['targetType']    = $targetType;
        $_SESSION['fleet'][$token]['fleetGroup']    = $fleetGroup;
        $_SESSION['fleet'][$token]['fleetSpeed']    = $fleetSpeed;
        $_SESSION['fleet'][$token]['ownPlanet']     = $PLANET['id'];

        if (!empty($fleet_group)) {
            $targetMission  = 2;
        }

        $fleetData  = [
            'fleetroom'     => floatToString($_SESSION['fleet'][$token]['fleetRoom']),
            'consumption'   => floatToString($consumption),
        ];

        $this->tplObj->execscript('calculateTransportCapacity();');
        $this->assign([
            'fleetdata'         => $fleetData,
            'consumption'       => floatToString($consumption),
            'mission'           => $targetMission,
            'galaxy'            => $PLANET['galaxy'],
            'system'            => $PLANET['system'],
            'planet'            => $PLANET['planet'],
            'type'              => $PLANET['planet_type'],
            'MissionSelector'   => $MissionOutput['MissionSelector'],
            'StaySelector'      => $MissionOutput['StayBlock'],
            'Exchange'          => $MissionOutput['Exchange'],
            'fl_continue'       => $LNG['fl_continue'],
            'token'             => $token,
        ]);

        $this->display('page.fleetStep2.default.tpl');
    }
}
