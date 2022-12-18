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
        global $USER, $PLANET, $LNG, $resource;

        $this->tplObj->loadscript('flotten.js');

        $targetGalaxy = HTTP::_GP('galaxy', 0);
        $targetSystem = HTTP::_GP('system', 0);
        $targetPlanet = HTTP::_GP('planet', 0);
        $targetType = HTTP::_GP('type', 0);
        $targetMission = HTTP::_GP('target_mission', -1);
        $fleetSpeed = HTTP::_GP('speed', 0);
        $fleetGroup = HTTP::_GP('fleet_group', 0);
        $token = HTTP::_GP('token', '');

        if (!isset($_SESSION['fleet'][$token])) {
            FleetFunctions::gotoFleetPage();
        }


        $fleetArray = $_SESSION['fleet'][$token]['fleet'];

        $db = Database::get();
        $sql = "SELECT id, id_owner, der_metal, der_crystal, tf_active
            FROM %%PLANETS%%
            WHERE universe = :universe AND galaxy = :targetGalaxy AND
            system = :targetSystem AND planet = :targetPlanet AND planet_type = '1';";
        $targetPlanetData = $db->selectSingle($sql, [
            ':universe' => Universe::current(),
            ':targetGalaxy' => $targetGalaxy,
            ':targetSystem' => $targetSystem,
            ':targetPlanet' => $targetPlanet
        ]);

        if ($targetType == 2 && !empty($targetPlanetData) && ($targetPlanetData['der_metal'] + $targetPlanetData['der_crystal']) == 0 &&
            $targetPlanetData['tf_active'] == 0) {
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

        if($targetMission==-1){
            $sql = "SELECT prioMission1 as '1',prioMission2 as '2',prioMission3 as '3',prioMission4 as '4',prioMission5 as '5',prioMission6 as '6',
                    prioMission7 as '7',prioMission8 as '8',prioMission9 as '9',prioMission17 as '17'
                    FROM %%USERS%% WHERE universe = :universe AND id = :userID ;";
            $missionprios =$db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':userID'   => $USER['id'],
            ]);
            $bestmission=999;
            foreach ($MissionOutput['MissionSelector']  as $mission1 => $mission) {
                    if(array_key_exists($mission,$missionprios)) {
                        $score = $missionprios[$mission];
                        if ($score < $bestmission) {
                            $targetMission = $mission;
                            $bestmission = $score;
                        }
                    }
                }

        }
        $GameSpeedFactor = FleetFunctions::getGameSpeedFactor();
        $MaxFleetSpeed = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $distance = FleetFunctions::getTargetDistance(
            [$PLANET['galaxy'], $PLANET['system'], $PLANET['planet']],
            [$targetGalaxy, $targetSystem, $targetPlanet]
        );
        $duration = FleetFunctions::getMissionDuration($fleetSpeed, $MaxFleetSpeed, $distance, $GameSpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $duration, $distance, $USER, $GameSpeedFactor);

        if ($targetMission == MISSION_COLONISATION) {
            $fleetReturnArray = $fleetArray;
            $fleetReturnArray[SHIP_COLONIZER] -= 1;
            $consumptionReturn = FleetFunctions::getFleetConsumption($fleetReturnArray, $duration, $distance, $USER, $GameSpeedFactor) / 2;
            $consumption += $consumptionReturn;
        }

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

        if (!empty($fleetGroup)) {
            $targetMission  = MISSION_ACS;
        }

        if ($targetMission == 0) {
            if (count($MissionOutput['MissionSelector']) == 1) {
                $targetMission = $MissionOutput['MissionSelector'][0];
            } else if ($targetPlanetData['id_owner'] == $USER['id']) {
                $targetMission = MISSION_TRANSPORT;
            }
            else {
                if (count($fleetArray) == 1 && isset($fleetArray[SHIP_PROBE])) {
                    $targetMission = MISSION_SPY;
                } else {
                    $sql = "SELECT a.id, a.ally_tag, a.ally_name, d.level, d.owner_2
                        FROM %%USERS%% u
                        JOIN %%ALLIANCE%% a ON a.id = u.ally_id
                        JOIN %%DIPLO%% d ON ((d.owner_1 = u.ally_id AND d.owner_2 = :ally_id) OR (d.owner_2 = u.ally_id AND d.owner_1 = :ally_id)) and d.accept = 1
                        WHERE u.universe = :universe AND u.id = :targetPlayerId AND d.level IN (1,2,4,6);";
                    $targetAllianceData = $db->selectSingle($sql, [
                        ':universe' => Universe::current(),
                        ':targetPlayerId' => $targetPlanetData['id_owner'],
                        ':ally_id' => $USER['ally_id'],
                    ]);

                    $sql = "SELECT count(*) as anz FROM %%BUDDY%% WHERE (sender = :id AND owner = :targetPlayerId) OR (sender = :targetPlayerId AND owner = :id);";
                    $buddy = $db->selectSingle($sql, [
                        ':id' => $USER['id'],
                        ':targetPlayerId' => $targetPlanetData['id_owner'],
                    ]);

                    if ($targetAllianceData || $buddy['anz']> 0) {
                        $targetMission = MISSION_TRANSPORT;
                    }
                    else {
                        $targetMission = MISSION_ATTACK;
                    }
                }
            }
        }

        $question = '';
        if ($targetMission == MISSION_ACS || $targetMission == MISSION_ATTACK) {
            $sql = "SELECT a.id, a.ally_tag, a.ally_name, d.level, d.owner_2
                FROM %%USERS%% u
                JOIN %%ALLIANCE%% a ON a.id = u.ally_id
                JOIN %%DIPLO%% d ON ((d.owner_1 = u.ally_id AND d.owner_2 = :ally_id) OR (d.owner_2 = u.ally_id AND d.owner_1 = :ally_id)) and d.accept = 1
                WHERE u.universe = :universe AND u.id = :targetPlayerId AND d.level IN (1,2,4,6);";
            $targetAllianceData = $db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':targetPlayerId' => $targetPlanetData['id_owner'],
                ':ally_id' => $USER['ally_id'],
            ]);

            $sql = "SELECT count(*) as anz FROM %%BUDDY%% WHERE (sender = :id AND owner = :targetPlayerId) OR (sender = :targetPlayerId AND owner = :id);";
            $buddy = $db->selectSingle($sql, [
                ':id' => $USER['id'],
                ':targetPlayerId' => $targetPlanetData['id_owner'],
            ]);

            if ($targetAllianceData) {
                if ($USER['lang'] == 'tr') {
                    $question = sprintf(
                        $LNG['fl_attack_confirm_diplo'],
                        '['.$targetAllianceData['ally_tag'].'] '.$targetAllianceData['ally_name'],
                        (($targetAllianceData['owner_2'] == $targetAllianceData['id'] && $targetAllianceData['level'] == 1) ? $LNG['al_diplo_level'][0] : $LNG['al_diplo_level'][$targetAllianceData['level']])
                    );
                }
                else {
                    $question = sprintf(
                        $LNG['fl_attack_confirm_diplo'],
                        (($targetAllianceData['owner_2'] == $targetAllianceData['id'] && $targetAllianceData['level'] == 1) ? $LNG['al_diplo_level'][0] : $LNG['al_diplo_level'][$targetAllianceData['level']]),
                        '['.$targetAllianceData['ally_tag'].'] '.$targetAllianceData['ally_name']
                    );
                }
            }
            elseif ($buddy['anz']> 0) {
                $question = $LNG['fl_attack_confirm_buddy'];
            }
        }

        $fleetData  = [
            'fleetroom'     => floatToString($_SESSION['fleet'][$token]['fleetRoom']),
            'consumption'   => floatToString($consumption),
        ];

        $this->tplObj->execscript('calculateTransportCapacity();checkHold('.$targetMission.');');
        $this->assign([
            'question'          => $question,
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
            'duration'          => $duration,
            'predefinedRes'     => $_SESSION['fleet'][$token]['predefinedResources']
        ]);

        foreach ($fleetArray as $Ship => $Count) {
            if ($Count > $PLANET[$resource[$Ship]]) {
                $this->printMessage($LNG['fl_not_all_ship_avalible'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }



        $this->display('page.fleetStep2.default.tpl');
    }
}
