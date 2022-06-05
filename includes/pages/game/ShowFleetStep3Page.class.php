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

class ShowFleetStep3Page extends AbstractGamePage
{
    public static int $requireModule = MODULE_FLEET_TABLE;

    public function __construct()
    {
        parent::__construct();
    }

    private function getActivePlanet($db, $planetId)
    {    	
    	$sql    = "SELECT * FROM %%PLANETS%% WHERE id = :planetId FOR UPDATE;";
    	$planet = $db->selectSingle($sql, array(
    			':planetId' => $planetId,
    	));

    	return $planet;
    }
    
    public function show()
    {
        global $USER, $PLANET, $resource, $LNG;

        if (IsVacationMode($USER)) {
            FleetFunctions::gotoFleetPage(0);
        }
		
        $db = Database::get();
        $db->startTransaction();
        // reloading global $PLANET for database lock support.
        // not pretty, but not using the global dupes resources
        $pPlanet = $this->getActivePlanet($db, $PLANET['id']);

        $targetMission = HTTP::_GP('mission', MISSION_TRANSPORT);
        $TransportMetal = max(0, round(HTTP::_GP('metal', 0.0)));
        $TransportCrystal = max(0, round(HTTP::_GP('crystal', 0.0)));
        $TransportDeuterium = max(0, round(HTTP::_GP('deuterium', 0.0)));
        $WantedResourceType = HTTP::_GP('resEx', 0);
        $WantedResourceAmount = max(0, round(HTTP::_GP('exchange', 0.0)));
        $markettype = HTTP::_GP('markettype', 0);
        $visibility = HTTP::_GP('visibility', 0);
        $maxFlightTime = HTTP::_GP('maxFlightTime', 0);
        $stayTime = HTTP::_GP('staytime', 0);
        $token = HTTP::_GP('token', '');

        $config = Config::get();

        if (!isset($_SESSION['fleet'][$token])) {
            FleetFunctions::gotoFleetPage(1);
        }

        if ($_SESSION['fleet'][$token]['time'] < TIMESTAMP - 600) {
            unset($_SESSION['fleet'][$token]);
            FleetFunctions::gotoFleetPage(0);
        }

        $formData = $_SESSION['fleet'][$token];
        unset($_SESSION['fleet'][$token]);

        $distance = $formData['distance'];
        $targetGalaxy = $formData['targetGalaxy'];
        $targetSystem = $formData['targetSystem'];
        $targetPlanet = $formData['targetPlanet'];
        $targetType = $formData['targetType'];
        $fleetGroup = $formData['fleetGroup'];
        $fleetArray = $formData['fleet'];
        $fleetStorage = $formData['fleetRoom'];
        $fleetSpeed = $formData['fleetSpeed'];
        $ownPlanet = $formData['ownPlanet'];

        if ($ownPlanet != $PLANET['id']) {
            $this->printMessage($LNG['fl_own_planet_error'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if ($targetMission != MISSION_ACS) {
            $fleetGroup = 0;
        }

        if (
            $PLANET['galaxy'] == $targetGalaxy && $PLANET['system'] == $targetSystem
            && $PLANET['planet'] == $targetPlanet && $PLANET['planet_type'] == $targetType
        ) {
            $this->printMessage($LNG['fl_error_same_planet'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if (
            $targetGalaxy < 1 || $targetGalaxy > $config->max_galaxy ||
            $targetSystem < 1 || $targetSystem > $config->max_system ||
            $targetPlanet < 1 || $targetPlanet > ($config->max_planets + 2) ||
            ($targetType !== 1 && $targetType !== 2 && $targetType !== 3)
        ) {
            $this->printMessage($LNG['fl_invalid_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        // Transport and market type 0 have to contain resources
        if (
            ($targetMission == MISSION_TRANSPORT || ($targetMission == MISSION_TRADE && $markettype == 0))
            && $TransportMetal + $TransportCrystal + $TransportDeuterium < 1
        ) {
            $this->printMessage($LNG['fl_no_noresource'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }
        // Market typ 1 cannot contain resources
        if (
            $targetMission == MISSION_TRADE && $markettype == 1
            && $TransportMetal + $TransportCrystal + $TransportDeuterium != 0
        ) {
            $this->printMessage($LNG['fl_resources'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($targetMission == MISSION_TRADE && $WantedResourceAmount < 1) {
            $this->printMessage($LNG['fl_no_noresource_exchange'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($targetMission == MISSION_TRADE && $WantedResourceAmount > pow(10, 50)) {
            $this->printMessage($LNG['fl_invalid_mission'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        $ActualFleets = FleetFunctions::getCurrentFleets($USER['id']);

        if (FleetFunctions::getMaxFleetSlots($USER) <= $ActualFleets) {
            $this->printMessage($LNG['fl_no_slots'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $ACSTime = 0;

        if (!empty($fleetGroup)) {
            $sql = "SELECT ankunft FROM %%USERS_ACS%% INNER JOIN %%AKS%% ON id = acsID
			WHERE acsID = :acsID AND :maxFleets > (SELECT COUNT(*) FROM %%FLEETS%% WHERE fleet_group = :acsID)
            AND :maxParticipants > (SELECT COUNT(DISTINCT fleet_owner) FROM %%FLEETS%% WHERE fleet_group = acsID);";
            $ACSTime = $db->selectSingle($sql, [
                ':acsID'        => $fleetGroup,
                ':maxFleets'    => $config->max_fleets_per_acs,
                ':maxParticipants' => Config::get()->max_participants_per_acs,
            ], 'ankunft');

            if (empty($ACSTime)) {
                $fleetGroup = 0;
                $targetMission = MISSION_ATTACK;
            }
        }

        $sql = "SELECT id, id_owner, der_metal, der_crystal, destruyed, ally_deposit FROM %%PLANETS%%"
            . " WHERE universe = :universe AND galaxy = :targetGalaxy AND system = :targetSystem"
            . " AND planet = :targetPlanet AND planet_type = :targetType;";
        $targetPlanetData = $db->selectSingle($sql, [
            ':universe'     => Universe::current(),
            ':targetGalaxy' => $targetGalaxy,
            ':targetSystem' => $targetSystem,
            ':targetPlanet' => $targetPlanet,
            ':targetType' => ($targetType == 2 ? 1 : $targetType),
        ]);

        if ($targetMission == MISSION_COLONISATION) {
            if (!empty($targetPlanetData)) {
                $this->printMessage($LNG['fl_target_exists'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }

            if ($targetType != 1) {
                $this->printMessage($LNG['fl_only_planets_colonizable'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }
        }

        if ($targetMission == MISSION_COLONISATION || $targetMission == MISSION_EXPEDITION || $targetMission == MISSION_TRADE) {
            $targetPlanetData = ['id' => 0, 'id_owner' => 0, 'planettype' => 1];
        } else {
            if (!empty($targetPlanetData["destruyed"])) {
                $this->printMessage($LNG['fl_no_target'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }

            if (empty($targetPlanetData)) {
                $this->printMessage($LNG['fl_no_target'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetStep1',
                ]]);
            }
        }

        foreach ($fleetArray as $Ship => $Count) {
            if ($Count > $PLANET[$resource[$Ship]]) {
                $this->printMessage($LNG['fl_not_all_ship_avalible'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if ($targetMission == MISSION_EXPEDITION) {
            $activeExpedition = FleetFunctions::getCurrentFleets($USER['id'], MISSION_EXPEDITION, true);
            $maxExpedition = FleetFunctions::getExpeditionLimit($USER);

            if ($activeExpedition >= $maxExpedition) {
                $this->printMessage($LNG['fl_no_expedition_slot'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        $usedPlanet = isset($targetPlanetData['id_owner']);
        $myPlanet = $usedPlanet && $targetPlanetData['id_owner'] == $USER['id'];
        $targetPlayerData = [];

        if ($targetMission == MISSION_COLONISATION || $targetMission == MISSION_EXPEDITION || $targetMission == MISSION_TRADE) {
            $targetPlayerData = [
                'id'                => 0,
                'onlinetime'        => TIMESTAMP,
                'ally_id'           => 0,
                'urlaubs_modus'     => 0,
                'authattack'        => 0,
                'total_points'      => 0,
            ];
        } elseif ($myPlanet) {
            $targetPlayerData = $USER;
        } elseif (!empty($targetPlanetData['id_owner'])) {
            $sql = "SELECT user.*, stat.total_points
                FROM %%USERS%% as user
                LEFT JOIN %%STATPOINTS%% as stat ON stat.id_owner = user.id AND stat.stat_type = '1'
                WHERE user.id = :ownerID;";

            $targetPlayerData = $db->selectSingle($sql, [':ownerID'  => $targetPlanetData['id_owner']]);
        }

        if (empty($targetPlayerData)) {
            $this->printMessage($LNG['fl_empty_target'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        $MisInfo = [];
        $MisInfo['galaxy'] = $targetGalaxy;
        $MisInfo['system'] = $targetSystem;
        $MisInfo['planet'] = $targetPlanet;
        $MisInfo['planettype'] = $targetType;
        $MisInfo['IsAKS'] = $fleetGroup;
        $MisInfo['Ship'] = $fleetArray;

        $availableMissions = FleetFunctions::getFleetMissions($USER, $MisInfo, $targetPlanetData);

        if (!in_array($targetMission, $availableMissions['MissionSelector'])) {
            $this->printMessage($LNG['fl_invalid_mission'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep2',
            ]]);
        }

        if ($targetMission != MISSION_RECYCLING && IsVacationMode($targetPlayerData)) {
            $this->printMessage($LNG['fl_target_exists'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetStep1',
            ]]);
        }

        if ($targetMission == MISSION_ATTACK || $targetMission == MISSION_ACS || $targetMission == MISSION_DESTRUCTION) {
            if (FleetFunctions::checkBash($targetPlanetData['id'])) {
                $this->printMessage($LNG['fl_bash_protection'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if (
            $targetMission == MISSION_ATTACK || $targetMission == MISSION_ACS || $targetMission == MISSION_HOLD || $targetMission == MISSION_SPY
            || $targetMission == MISSION_DESTRUCTION
        ) {
            if (Config::get()->adm_attack == 1 && $targetPlayerData['authattack'] > $USER['authlevel']) {
                $this->printMessage($LNG['fl_admin_attack'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            $sql = 'SELECT total_points
			FROM %%STATPOINTS%%
			WHERE id_owner = :userId AND stat_type = :statType';

            try {
                $USER += Database::get()->selectSingle($sql, [
                    ':userId' => $USER['id'],
                    ':statType' => 1,
                ]) ?: ['total_points' => 0];
            } catch (Exception $exception) {
            }

            $IsNoobProtec = CheckNoobProtec($USER, $targetPlayerData, $targetPlayerData);

            if ($IsNoobProtec['NoobPlayer']) {
                $this->printMessage($LNG['fl_player_is_noob'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            if ($IsNoobProtec['StrongPlayer']) {
                $this->printMessage($LNG['fl_player_is_strong'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        if ($targetMission == MISSION_HOLD || $targetMission == MISSION_TRANSFER) {
            if ($targetPlayerData['ally_id'] != $USER['ally_id'] || $USER['ally_id'] == 0) {
                $sql = "SELECT COUNT(*) as state FROM %%BUDDY%%
				WHERE id NOT IN (SELECT id FROM %%BUDDY_REQUEST%% WHERE %%BUDDY_REQUEST%%.id = %%BUDDY%%.id) AND
				(owner = :ownerID AND sender = :userID) OR (owner = :userID AND sender = :ownerID);";
                $buddy = $db->selectSingle($sql, [
                    ':ownerID'  => $targetPlayerData['id'],
                    ':userID'   => $USER['id'],
                ], 'state');

                if ($buddy == 0) {
                    $this->printMessage($LNG['fl_no_same_alliance'], [[
                        'label' => $LNG['sys_back'],
                        'url'   => 'game.php?page=fleetTable',
                    ]]);
                }
            }
        }

        $fleetMaxSpeed = FleetFunctions::getFleetMaxSpeed($fleetArray, $USER);
        $SpeedFactor = FleetFunctions::getGameSpeedFactor();
        $duration = FleetFunctions::getMissionDuration($fleetSpeed, $fleetMaxSpeed, $distance, $SpeedFactor, $USER);
        $consumption = FleetFunctions::getFleetConsumption($fleetArray, $duration, $distance, $USER, $SpeedFactor);
        
        if ($PLANET[$resource[903]] < $consumption) {
            $this->printMessage($LNG['fl_not_enough_deuterium'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }

        $StayDuration = 0;

        if ($targetMission == MISSION_HOLD || $targetMission == MISSION_EXPEDITION || $targetMission == MISSION_TRADE) {
            if (!isset($availableMissions['StayBlock'][$stayTime])) {
                $this->printMessage($LNG['fl_hold_time_not_exists'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }

            $StayDuration = round($availableMissions['StayBlock'][$stayTime] * 3600, 0);
        }

        $fleetStorage -= $consumption;

        $fleetResource = [
            901 => min($TransportMetal, floor($PLANET[$resource[901]])),
            902 => min($TransportCrystal, floor($PLANET[$resource[902]])),
            903 => min($TransportDeuterium, floor($PLANET[$resource[903]] - $consumption)),
        ];

        $StorageNeeded = array_sum($fleetResource);

        if ($StorageNeeded > $fleetStorage) {
            $this->printMessage($LNG['fl_not_enough_space'], [[
                'label' => $LNG['sys_back'],
                'url'   => 'game.php?page=fleetTable',
            ]]);
        }


        if ($targetMission == MISSION_TRANSFER) {
            $attack = $USER[$resource[109]] * 10;
            $shield = $USER[$resource[110]] * 10;
            $defensive = $USER[$resource[111]] * 10;

            $attack_targ = $targetPlayerData[$resource[109]] * 10;
            $shield_targ = $targetPlayerData[$resource[110]] * 10;
            $defensive_targ = $targetPlayerData[$resource[111]] * 10;

            if ($attack < $attack_targ || $defensive < $defensive_targ || $shield < $shield_targ) {
                $this->printMessage($LNG['fl_stronger_techs'], [[
                    'label' => $LNG['sys_back'],
                    'url'   => 'game.php?page=fleetTable',
                ]]);
            }
        }

        $PLANET[$resource[901]] -= $fleetResource[901];
        $PLANET[$resource[902]] -= $fleetResource[902];
        $PLANET[$resource[903]] -= $fleetResource[903] + $consumption;

        $fleetStartTime = $duration + TIMESTAMP;
        $timeDifference = round(max(0, $fleetStartTime - $ACSTime));

        if ($fleetGroup != 0) {
            if ($timeDifference != 0) {
                FleetFunctions::setACSTime($timeDifference, $fleetGroup);
            } else {
                $fleetStartTime = $ACSTime;
            }
        }

        $fleetStayTime = $fleetStartTime + $StayDuration;
        $fleetEndTime = $fleetStayTime + $duration;

        $fleet_id = FleetFunctions::sendFleet(
            $fleetArray,
            $targetMission,
            $USER['id'],
            $PLANET['id'],
            $PLANET['galaxy'],
            $PLANET['system'],
            $PLANET['planet'],
            $PLANET['planet_type'],
            $targetPlanetData['id_owner'],
            $targetPlanetData['id'],
            $targetGalaxy,
            $targetSystem,
            $targetPlanet,
            $targetType,
            $fleetResource,
            $fleetStartTime,
            $fleetStayTime,
            $fleetEndTime,
            $fleetGroup,
            0 // missileTarget
        );

        if ($targetMission == MISSION_TRADE) {
            $sql = 'INSERT INTO %%TRADES%% SET
				transaction_type			= :transaction,
				seller_fleet_id				= :sellerFleet,
				filter_visibility			= :visibility,
				filter_flighttime			= :flightTime,
				ex_resource_type			= :resType,
				ex_resource_amount		= :resAmount;';

                $db->insert($sql, [
                    ':transaction'          => $markettype,
                    ':sellerFleet'          => $fleet_id,
                    ':resType'                  => $WantedResourceType,
                    ':resAmount'                => $WantedResourceAmount,
                    ':flightTime'               => $maxFlightTime * 3600,
                    ':visibility'               => $visibility
                ]);
        }
     	
        $db->commit();

        foreach ($fleetArray as $Ship => $Count) {
            $fleetList[$LNG['tech'][$Ship]] = $Count;
        }

        $this->tplObj->gotoside('game.php?page=fleetTable');
        $this->assign([
            'targetMission'     => $targetMission,
            'distance'          => $distance,
            'consumption'       => $consumption,
            'from'              => $PLANET['galaxy'] . ":" . $PLANET['system'] . ":" . $PLANET['planet'],
            'destination'       => $targetGalaxy . ":" . $targetSystem . ":" . $targetPlanet,
            'fleetStartTime'    => _date($LNG['php_tdformat'], $fleetStartTime, $USER['timezone']),
            'fleetEndTime'      => _date($LNG['php_tdformat'], $fleetEndTime, $USER['timezone']),
            'MaxFleetSpeed'     => $fleetMaxSpeed,
            'FleetList'         => $fleetArray,
        ]);

        $this->display('page.fleetStep3.default.tpl');
    }
}