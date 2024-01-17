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
        $USER =& Singleton()->USER;
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $resource =& Singleton()->resource;
        $this->tplObj->loadscript('flotten.js');

        $targetGalaxy = HTTP::_GP('galaxy', 0);
        $targetSystem = HTTP::_GP('system', 0);
        $targetPlanet = HTTP::_GP('planet', 0);
        $targetType = HTTP::_GP('type', 0);
        $targetMission = HTTP::_GP('target_mission', -1);
        $fleetSpeed = HTTP::_GP('speed', 0);
        $fleetGroup = HTTP::_GP('fleet_group', 0);
        $token = HTTP::_GP('token', '');

        $session = Session::load();
        $fleet = $session->fleet;
        if (!isset($fleet[$token])) {
            FleetFunctions::gotoFleetPage();
        }


        $fleetArray = $fleet[$token]['fleet'];

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

        if ($targetMission == -1) {
            $sql = "SELECT prioMission1 as '1', prioMission2 as '2', prioMission3 as '3', prioMission4 as '4', prioMission5 as '5', prioMission6 as '6',
                    prioMission7 as '7', prioMission8 as '8', prioMission9 as '9', prioMission17 as '17'
                    FROM %%USERS%% WHERE universe = :universe AND id = :userID ;";
            $missionprios =$db->selectSingle($sql, [
                ':universe' => Universe::current(),
                ':userID'   => $USER['id'],
            ]);
            $bestmission=999;
            foreach ($MissionOutput['MissionSelector'] as $mission) {
                if (array_key_exists($mission, $missionprios)) {
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

        if ($consumption > $PLANET['deuterium']) {
            $this->printMessage($LNG['fl_not_enough_deuterium'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable'
            ]]);
        }

        if (!FleetFunctions::checkUserSpeed($fleetSpeed)) {
            FleetFunctions::gotoFleetPage(0);
        }

        $fleet[$token]['speed'] = $MaxFleetSpeed;
        $fleet[$token]['distance'] = $distance;
        $fleet[$token]['targetGalaxy'] = $targetGalaxy;
        $fleet[$token]['targetSystem'] = $targetSystem;
        $fleet[$token]['targetPlanet'] = $targetPlanet;
        $fleet[$token]['targetType'] = $targetType;
        $fleet[$token]['fleetGroup'] = $fleetGroup;
        $fleet[$token]['fleetSpeed'] = $fleetSpeed;
        $fleet[$token]['ownPlanet'] = $PLANET['id'];
        $session->fleet = $fleet;
        $session->save();

        if ($targetMission == 0) {
            if (count($MissionOutput['MissionSelector']) == 1) {
                $targetMission = $MissionOutput['MissionSelector'][0];
            } elseif (!empty($targetPlanetData) && $targetPlanetData['id_owner'] == $USER['id']) {
                $targetMission = MISSION_TRANSPORT;
            } else {
                if (count($fleetArray) == 1 && isset($fleetArray[SHIP_PROBE])) {
                    $targetMission = MISSION_SPY;
                } elseif (!empty($targetPlanetData)) {
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
                    } else {
                        $targetMission = MISSION_ATTACK;
                    }
                } else {
                    $targetMission = MISSION_ATTACK;
                }
            }
        }

        if (!empty($fleetGroup) && $targetMission == MISSION_ATTACK) {
            $targetMission  = MISSION_ACS;
        }

        $question = '';
        if (($targetMission == MISSION_ACS || $targetMission == MISSION_ATTACK) && !empty($targetPlanetData)) {
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
                } else {
                    $question = sprintf(
                        $LNG['fl_attack_confirm_diplo'],
                        (($targetAllianceData['owner_2'] == $targetAllianceData['id'] && $targetAllianceData['level'] == 1) ? $LNG['al_diplo_level'][0] : $LNG['al_diplo_level'][$targetAllianceData['level']]),
                        '['.$targetAllianceData['ally_tag'].'] '.$targetAllianceData['ally_name']
                    );
                }
            } elseif ($buddy['anz']> 0) {
                $question = $LNG['fl_attack_confirm_buddy'];
            }
        }

        $fleetData  = [
            'fleetroom'     => floatToString($fleet[$token]['fleetRoom']),
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
            'predefinedRes'     => $fleet[$token]['predefinedResources']
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

    public function checkResources(): void
    {
        $PLANET =& Singleton()->PLANET;
        $LNG =& Singleton()->LNG;
        $USER =& Singleton()->USER;

        $targetMission = HTTP::_GP('mission', MISSION_TRANSPORT);
        $TransportMetal = max(0, round(HTTP::_GP('metal', 0.0)));
        $TransportCrystal = max(0, round(HTTP::_GP('crystal', 0.0)));
        $TransportDeuterium = max(0, round(HTTP::_GP('deuterium', 0.0)));
        $WantedResourceType = HTTP::_GP('resEx', 0);
        $WantedResourceAmount = max(0, round(HTTP::_GP('exchange', 0.0)));
        $isFleetTrade = HTTP::_GP('markettype', 0);

        if ($PLANET['metal'] < $TransportMetal) {
            $this->sendJSON(sprintf($LNG['fl_not_enough'], $LNG['tech'][RESOURCE_METAL]));
        }
        if ($PLANET['crystal'] < $TransportCrystal) {
            $this->sendJSON(sprintf($LNG['fl_not_enough'], $LNG['tech'][RESOURCE_CRYSTAL]));
        }

        $token = HTTP::_GP('token', '');
        $session = Session::load();
        $fleet = $session->fleet;
        if (!isset($fleet[$token]) || !isset($fleet[$token]['fleet']) || !isset($fleet[$token]['distance']) ||
            !isset($fleet[$token]['fleetSpeed']) || !isset($fleet[$token]['fleetRoom'])) {
            $this->sendJSON(['message' => $LNG['fl_error'], 'error' => true]);
        }
        $formData = $fleet[$token];
        $fleetArray = $formData['fleet'];
        $distance = $formData['distance'];
        $fleetSpeed = $formData['fleetSpeed'];
        $fleetStorage = $formData['fleetRoom'];

        $GameSpeedFactor = FleetFunctions::getGameSpeedFactor();
        $MaxFleetSpeed = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $duration = FleetFunctions::getMissionDuration($fleetSpeed, $MaxFleetSpeed, $distance, $GameSpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $duration, $distance, $USER, $GameSpeedFactor);

        if ($PLANET['deuterium'] < $TransportDeuterium + $consumption) {
            $this->sendJSON(sprintf($LNG['fl_not_enough'], $LNG['tech'][RESOURCE_DEUT]));
        }

        if ($fleetStorage < $TransportMetal + $TransportCrystal + $TransportDeuterium + $consumption || $fleetStorage < $WantedResourceAmount) {
            $this->sendJSON($LNG['fl_not_enough_space']);
        }

        if ($targetMission == MISSION_TRADE) {
            $anz = 0;
            $anz += $TransportMetal > 0 ? 1 : 0;
            $anz += $TransportCrystal > 0 ? 1 : 0;
            $anz += $TransportDeuterium > 0 ? 1 : 0;

            if ($anz == 0 && $isFleetTrade) {
                $this->sendJSON($LNG['fl_trade_no_resource_loaded']);
            } elseif ($anz > 1) {
                $this->sendJSON($LNG['fl_trade_to_many_resources_loaded']);
            }

            if ($TransportMetal > 0 && $WantedResourceType == 1) {
                $this->sendJSON(sprintf($LNG['fl_trade_same_resource'], $LNG['tech'][RESOURCE_METAL], $LNG['tech'][RESOURCE_METAL]));
            } elseif ($TransportCrystal > 0 && $WantedResourceType == 2) {
                $this->sendJSON(sprintf($LNG['fl_trade_same_resource'], $LNG['tech'][RESOURCE_CRYSTAL], $LNG['tech'][RESOURCE_CRYSTAL]));
            } elseif ($TransportDeuterium > 0 && $WantedResourceType == 3) {
                $this->sendJSON(sprintf($LNG['fl_trade_same_resource'], $LNG['tech'][RESOURCE_DEUT], $LNG['tech'][RESOURCE_DEUT]));
            }
        }

        $this->sendJSON('OK');
    }
}
